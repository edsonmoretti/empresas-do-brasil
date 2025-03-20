<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use function Symfony\Component\String\u;

/**
 * @property Estabelecimento $estabelecimento
 */
class Empresa extends Model
{
    protected $fillable = [
        'cnpj_basico',
        'razao_social',
        'natureza_juridica',
        'qualificacao_responsavel',
        'capital_social',
        'porte_empresa',
        'ente_federativo_responsavel',
    ];

    public static array $filterable = [
        'cnpj_basico',
        'razao_social:contains',

    ];

    public function estabelecimento(): HasOne
    {
        return $this->hasOne(Estabelecimento::class, 'cnpj_basico', 'cnpj_basico');

    }

    /**
     * Retorna o Quadro de Sócios e Administradores da empresa
     * @return HasMany
     */
    public function qsa(): HasMany
    {
        return $this->hasMany(Socio::class, 'cnpj_basico', 'cnpj_basico');
    }

    public function toArray()
    {
        $array = [
            ...parent::toArray(),
            ...$this->estabelecimento->toArray(),
        ];

        // reorganizando o array
        $cnpjArray = [
            'cnpj' => $array['cnpj'],
            'cnpj_formatado' => formatCnpj($array['cnpj'])
        ];
        $array = array_merge($cnpjArray,$array);

        // Coloca o QSA no final do array (antes da data de atualização)
        $qsa = $array['qsa'];
        unset($array['qsa']);
        $array['qsa'] = $qsa;

        $array['atualizado_em'] = $this->updated_at->format('d/m/Y H:i:s');

        unset($array['id']);
        unset($array['estabelecimento']);
        unset($array['cnpj_basico']);
        unset($array['created_at']);
        unset($array['updated_at']);

        return $array;
    }
}

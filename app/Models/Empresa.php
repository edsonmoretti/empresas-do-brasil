<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'cnpj:static-function|WhereCnpj',
    ];

    public static function WhereCnpj(Builder $builder, $value): Builder
    {
        // join cnpj_basico em estabelecimento e filtra por cnpj
        return $builder->whereHas('estabelecimento', function ($query) use ($value) {
            // colunas cnpj_basico,cnpj_ordem,cnpj_dv (as 3 colunas formam o cnpj)
            $query->whereRaw('CONCAT(cnpj_basico, cnpj_ordem, cnpj_dv) = ?', [$value]);
        });
    }

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

        $enderecoCompleto = $array['tipo_logradouro'] . ' ' . $array['logradouro'] . ', ' . $array['numero'] . ' - ' . $array['bairro'] . ', ' . $array['municipio'] . ' - ' . $array['uf'] . ', CEP: ' . $array['cep'];
        $enderecoCompleto = str_replace('  ', ' ', $enderecoCompleto);

        // reorganizando o array
        $cnpjArray = [
            'cnpj' => $array['cnpj'],
            'cnpj_formatado' => formatCnpj($array['cnpj']),
            'razao_social' => $array['razao_social'],
            'nome_fantasia' => $array['nome_fantasia'],
            'endereco_completo' => $enderecoCompleto,
        ];

        $array = array_merge($cnpjArray, $array);

        if (is_string($array['cnae_fiscal_secundaria'])) {
            $array['cnae_fiscal_secundaria'] = explode(',', $array['cnae_fiscal_secundaria']);
        }

        // Correio eletronico em minúsculo
        $array['correio_eletronico'] = strtolower($array['correio_eletronico']);

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

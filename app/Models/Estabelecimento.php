<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Estabelecimento extends Model
{
    protected $fillable = [
        'cnpj_basico',
        'cnpj_ordem',
        'cnpj_dv',
        'identificador_matriz_filial',
        'nome_fantasia',
        'situacao_cadastral',
        'data_situacao_cadastral',
        'motivo_situacao_cadastral',
        'nome_cidade_exterior',
        'pais',
        'data_inicio_atividade',
        'cnae_fiscal_principal',
        'cnae_fiscal_secundaria',
        'tipo_logradouro',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cep',
        'uf',
        'municipio', // código relacionado com a tabela municipios
        'ddd1',
        'telefone1',
        'ddd2',
        'telefone2',
        'ddd_fax',
        'fax',
        'correio_eletronico',
        'situacao_especial',
        'data_situacao_especial',
    ];

    public static array $filterable = [
        'uf',
        'municipio:static-function|WhereMunicipioNameLike',
        'bairro:contains',
    ];

    public static function WhereMunicipioNameLike(Builder $builder, $value): Builder
    {
        // like lower case all
        return $builder->whereHas('municipio', function ($query) use ($value) {
            $query->whereRaw('LOWER(nome) like ?', ['%' . strtolower($value) . '%']);
        });
    }


    public function municipio(): BelongsTo
    {
        return $this->belongsTo(
            Municipio::class,
            'municipio',
            'codigo_dados_abertos'
        );
    }

    public function getMunicipioNomeAttribute()
    {
        return $this->municipio()->first()->nome ?? null;
    }

    public function toArray(): array
    {
        $array = parent::toArray();
        $array['municipio'] = $this->municipio_nome;
        $array['cnpj'] = $this->cnpj_basico.$this->cnpj_ordem.$this->cnpj_dv;
        unset($array['cnpj_basico']);
        unset($array['cnpj_ordem']);
        unset($array['cnpj_dv']);
        unset($array['id']);
        return $array;
    }
}

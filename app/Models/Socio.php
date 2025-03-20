<?php

namespace App\Models;

class Socio extends Model
{
    protected $fillable = [
        'cnpj_basico',
        'identificador_socio',
        'nome_socio',
        'cpf_cnpj_socio',
        'qualificacao_socio',
        'data_entrada_sociedade',
        'pais',
        'cpf_representante_legal',
        'nome_representante_legal',
        'qualificacao_representante_legal',
        'faixa_etaria',
    ];
}

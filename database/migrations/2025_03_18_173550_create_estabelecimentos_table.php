<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('estabelecimentos', function (Blueprint $table) {
            $table->id();
            $table->string('cnpj_basico', 10);
            $table->string('cnpj_ordem', 6);
            $table->string('cnpj_dv', 4);
            $table->string('identificador_matriz_filial', 2);
            $table->string('nome_fantasia')->nullable();
            $table->string('situacao_cadastral', 2);
            $table->string('data_situacao_cadastral')->nullable();
            $table->string('motivo_situacao_cadastral', 4)->nullable();
            $table->string('nome_cidade_exterior')->nullable();
            $table->string('pais', 10)->nullable();
            $table->string('data_inicio_atividade')->nullable();
            $table->string('cnae_fiscal_principal', 10);
            $table->text('cnae_fiscal_secundaria')->nullable();
            $table->string('tipo_logradouro')->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cep', 10)->nullable();
            $table->string('uf', 2);
            $table->string('municipio', 50);
            $table->string('ddd1', 3)->nullable();
            $table->string('telefone1', 12)->nullable();
            $table->string('ddd2', 3)->nullable();
            $table->string('telefone2', 12)->nullable();
            $table->string('ddd_fax', 3)->nullable();
            $table->string('fax', 12)->nullable();
            $table->string('correio_eletronico')->nullable();
            $table->string('situacao_especial')->nullable();
            $table->string('data_situacao_especial')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estabelecimentos');
    }
};

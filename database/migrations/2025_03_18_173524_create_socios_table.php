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
        Schema::create('socios', function (Blueprint $table) {
            $table->id();
            $table->string('cnpj_basico', 8);
            $table->string('identificador_socio', 1);
            $table->string('nome_socio');
            $table->string('cpf_cnpj_socio', 14);
            $table->string('qualificacao_socio', 2);
            $table->date('data_entrada_sociedade')->nullable();
            $table->string('pais', 3)->nullable();
            $table->string('cpf_representante_legal')->nullable();
            $table->string('nome_representante_legal')->nullable();
            $table->string('qualificacao_representante_legal', 2)->nullable();
            $table->string('faixa_etaria', 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('socios');
    }
};

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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('cnpj_basico', 8);
            $table->string('razao_social');
            $table->string('natureza_juridica', 4);
            $table->string('qualificacao_responsavel', 2);
            $table->string('capital_social', 15, 2);
            $table->string('porte_empresa', 2);
            $table->string('ente_federativo_responsavel')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};

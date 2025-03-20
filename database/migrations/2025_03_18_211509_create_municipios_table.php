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
        Schema::create('municipios', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_dados_abertos', 10)->index()->unique();
            $table->string('nome', 100);
            $table->string('uf', 2)->nullable();
            $table->string('codigo_ibge', 10)->nullable();
            $table->timestamps();
        });

        // index codigo_dados_abertos
        Schema::table('municipios', function (Blueprint $table) {
            $table->index('codigo_dados_abertos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipios');
    }
};

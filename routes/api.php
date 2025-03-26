<?php

use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\EstabelecimentoController;
use App\Http\Controllers\Api\SocioController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'estabelecimentos'], function () {
    Route::get('/', [EstabelecimentoController::class, 'index']);
    Route::get('/{id}', [EstabelecimentoController::class, 'show']);
});

Route::group(['prefix' => 'empresas'], function () {
    Route::get('/', [EmpresaController::class, 'index']);
    Route::get('/cnpj/{cnpj}', [EmpresaController::class, 'getByCnpj']);
});

Route::group(['prefix' => 'socios'], function () {
    Route::get('/', [SocioController::class, 'index']);
});

<?php

use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\EstabelecimentoController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'estabelecimento'], function () {
    Route::get('/', [EstabelecimentoController::class, 'index']);
    Route::get('/{id}', [EstabelecimentoController::class, 'show']);
});

Route::group(['prefix' => 'empresas'], function () {
    Route::get('/', [EmpresaController::class, 'index']);
    Route::get('/{id}', [EmpresaController::class, 'show']);
});

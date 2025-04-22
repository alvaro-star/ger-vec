<?php

use App\Http\Controllers\PessoaController;
use App\Http\Controllers\RevisaoController;
use App\Http\Controllers\VeiculoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('pessoas', PessoaController::class);
Route::get('pessoas/{id}/veiculos', [VeiculoController::class, 'veiculosByPessoa']);
Route::apiResource('veiculos', VeiculoController::class);
Route::get('veiculos/{id}/revisoes', [RevisaoController::class, 'revisoesByVeiculo']);
Route::apiResource('revisoes', RevisaoController::class);

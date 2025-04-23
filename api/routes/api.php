<?php

use App\Http\Controllers\PessoaController;
use App\Http\Controllers\RevisaoController;
use App\Http\Controllers\VeiculoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('pessoas/{id}/veiculos', [VeiculoController::class, 'veiculosByPessoa']);
Route::get('/pessoas/statistics', [PessoaController::class, 'statistics']);
Route::apiResource('pessoas', PessoaController::class);

Route::get('/veiculos/statistics/marcas/n_veiculos', [VeiculoController::class, 'nVeiculosGroupByMarca']);
Route::get('/veiculos/statistics/sexo-marca/n_veiculos', [VeiculoController::class, 'nVeiculosBySexoAndMarca']);
Route::get('/veiculos/statistics/marcas/revisoes', [VeiculoController::class, 'nRevisoesGroupByMarca']);

Route::apiResource('veiculos', VeiculoController::class);
Route::get('veiculos/{id}/revisoes', [RevisaoController::class, 'revisoesByVeiculo']);
Route::get('/pessoas/statistics/sexo/n_veiculos', [PessoaController::class, 'countVeiculosBySexo']);

Route::apiResource('revisoes', RevisaoController::class);
Route::get('/pessoas/statistics/revisoes/quantidade-media-tempo', [RevisaoController::class, 'countRevisoesByPessoa']);

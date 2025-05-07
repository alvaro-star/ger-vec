<?php

use App\Http\Controllers\MarcaController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\RevisaoController;
use App\Http\Controllers\VeiculoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/pessoas/statistics', [PessoaController::class, 'statistics']);
Route::get('/pessoas/statistics/sexo/n_veiculos', [PessoaController::class, 'countVeiculosBySexo']);
Route::get('/pessoas/statistics/revisoes/quantidade-media-tempo', [RevisaoController::class, 'countRevisoesByPessoa']);
Route::apiResource('pessoas', PessoaController::class);


Route::get('/veiculos/statistics/sexo-marca/n_veiculos', [VeiculoController::class, 'nVeiculosBySexoAndMarca']);
Route::get('/veiculos/placas', [VeiculoController::class, 'listPlacas']);

Route::apiResource('veiculos', VeiculoController::class);

Route::apiResource('revisoes', RevisaoController::class);
Route::apiResource('marcas', MarcaController::class);

Route::get('/marcas/statistics/group_pais', [MarcaController::class, 'groupByPais']);
Route::get('/marcas/statistics/n_veiculos', [VeiculoController::class, 'nVeiculosGroupByMarca']);
Route::get('/marcas/statistics/n_revisoes', [VeiculoController::class, 'nRevisoesGroupByMarca']);

Route::get('/test-redis', function () {
    $redis = Redis::connection();
    $redis->set('foo', 'bar');
    return $redis->get('foo');
});

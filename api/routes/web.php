<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/teste',  function () {
    return ['teste' => 'steve'];
});

require __DIR__ . '/auth.php';

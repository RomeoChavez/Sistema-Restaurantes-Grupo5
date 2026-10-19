<?php

use App\Http\Controllers\MesaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PlatoController;
use Illuminate\Support\Facades\Route;

Route::apiResource('mesas', MesaController::class);
Route::apiResource('platos', PlatoController::class);
Route::apiResource('pedidos', PedidoController::class);
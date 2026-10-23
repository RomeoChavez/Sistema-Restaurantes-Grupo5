<?php

use App\Http\Controllers\AdminMesaController;
use App\Http\Controllers\AdminPlatoController;
use App\Http\Controllers\ClientePedidoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MesaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PlatoController;
use App\Http\Controllers\RegistroController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('inicio');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');

    Route::get('/registro', [RegistroController::class, 'create'])->name('registro.create');
    Route::post('/registro', [RegistroController::class, 'store'])->name('registro.store')->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/reservar', [ClientePedidoController::class, 'create'])->name('cliente.reservar');
    Route::post('/reservar', [ClientePedidoController::class, 'store'])->name('cliente.pedidos.store');
    Route::get('/mis-pedidos', [ClientePedidoController::class, 'index'])->name('cliente.pedidos');

    Route::get('/admin', [HomeController::class, 'admin'])->middleware('rol:admin')->name('admin.inicio');
    Route::post('/admin/platos', [AdminPlatoController::class, 'store'])->middleware('rol:admin')->name('admin.platos.store');
    Route::post('/admin/platos/{plato}/toggle', [AdminPlatoController::class, 'toggle'])->middleware('rol:admin')->name('admin.platos.toggle');
    Route::post('/admin/mesas', [AdminMesaController::class, 'store'])->middleware('rol:admin')->name('admin.mesas.store');
    Route::post('/admin/mesas/{mesa}/estado', [AdminMesaController::class, 'cambiarEstado'])->middleware('rol:admin')->name('admin.mesas.estado');

    Route::get('/cocina', [HomeController::class, 'cocina'])->middleware('rol:admin,mesero')->name('cocina.inicio');
    Route::post('/cocina/pedidos/{pedido}/estado', [HomeController::class, 'cambiarEstadoPedido'])->middleware('rol:admin,mesero')->name('cocina.pedidos.estado');

    Route::middleware('rol:admin,mesero')->group(function () {
        Route::apiResource('mesas', MesaController::class);
        Route::apiResource('platos', PlatoController::class);
        Route::apiResource('pedidos', PedidoController::class);
    });
});
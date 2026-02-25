<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\OrdenController;

Route::get('/', function () {

    $cards = [
        [
            'title' => 'Total Clientes',
            'value' => 8,
            'change' => '+12% vs mes anterior',
            'icon' => 'users',
            'color' => 'blue'
        ],
        [
            'title' => 'Vehículos Registrados',
            'value' => 8,
            'change' => '+8% vs mes anterior',
            'icon' => 'car',
            'color' => 'green'
        ],
        [
            'title' => 'Órdenes Activas',
            'value' => 3,
            'change' => '+3 vs mes anterior',
            'icon' => 'clipboard-list',
            'color' => 'purple'
        ],
        [
            'title' => 'Órdenes Completadas',
            'value' => 2,
            'change' => '+25% vs mes anterior',
            'icon' => 'check-circle',
            'color' => 'emerald'
        ],
    ];

    return view('dashboard', compact('cards'));

})->name('dashboard'); // 👈 IMPORTANTE

// Clientes
Route::resource('clientes', ClienteController::class);

Route::post('/clientes/{id}/restore', [ClienteController::class, 'restore'])
    ->name('clientes.restore');

Route::delete('/clientes/{id}/force-delete', [ClienteController::class, 'forceDelete'])
    ->name('clientes.forceDelete');

// Vehiculos
Route::resource('vehiculos', VehiculoController::class);

Route::post('vehiculos/{id}/restore', [VehiculoController::class,'restore'])->name('vehiculos.restore');

Route::delete('vehiculos/{id}/forceDelete', [VehiculoController::class,'forceDelete'])->name('vehiculos.forceDelete');

// Servicios
Route::resource('servicios', ServicioController::class);

Route::post('servicios/{id}/restore', 
    [ServicioController::class, 'restore'])->name('servicios.restore');

Route::delete('servicios/{id}/force-delete', 
    [ServicioController::class, 'forceDelete'])->name('servicios.forceDelete');

// Orden trabajo
Route::resource('ordenes', OrdenController::class);
Route::post('ordenes/{id}/restore', [OrdenController::class, 'restore'])->name('ordenes.restore');
Route::delete('ordenes/{id}/force-delete', [OrdenController::class, 'forceDelete'])->name('ordenes.forceDelete');
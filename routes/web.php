<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;

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

Route::resource('clientes', ClienteController::class);

Route::post('/clientes/{id}/restore', [ClienteController::class, 'restore'])
    ->name('clientes.restore');

Route::delete('/clientes/{id}/force-delete', [ClienteController::class, 'forceDelete'])
    ->name('clientes.forceDelete');

@extends('layouts.app')

@section('content')

<h1 class="text-2xl font-semibold text-gray-800">Dashboard</h1>
<p class="text-gray-500 text-sm mb-8">Resumen general del sistema</p>

@php
    $cards = [
        ['title' => 'Total Clientes', 'value' => '8', 'change' => '+12% vs mes anterior', 'color' => 'blue', 'icon' => 'users'],
        ['title' => 'Vehículos Registrados', 'value' => '8', 'change' => '+8% vs mes anterior', 'color' => 'purple', 'icon' => 'car'],
        ['title' => 'Órdenes Activas', 'value' => '3', 'change' => '+3 vs mes anterior', 'color' => 'orange', 'icon' => 'clipboard-list'],
        ['title' => 'Ganancia del Mes', 'value' => 'RD$67,200', 'change' => '+15% vs mes anterior', 'color' => 'green', 'icon' => 'dollar-sign'],
        ['title' => 'Productos en Inventario', 'value' => '201', 'change' => '-5% vs mes anterior', 'color' => 'indigo', 'icon' => 'package'],
        ['title' => 'Órdenes Completadas', 'value' => '2', 'change' => '+25% vs mes anterior', 'color' => 'teal', 'icon' => 'check-circle'],
    ];
@endphp

<!-- CARDS -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-10">

    @foreach($cards as $card)
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm 
                    hover:shadow-xl hover:-translate-y-1 hover:border-blue-200 
                    transition-all duration-300 ease-in-out cursor-pointer group">

            <div class="flex justify-between items-center">

                <div>
                    <p class="text-gray-500 text-sm">{{ $card['title'] }}</p>
                    <h2 class="text-3xl font-semibold text-gray-800 mt-2">
                        {{ $card['value'] }}
                    </h2>
                    <p class="text-green-500 text-sm mt-2">
                        {{ $card['change'] }}
                    </p>
                </div>

                <div class="w-14 h-14 rounded-2xl bg-{{ $card['color'] }}-100 
                            flex items-center justify-center 
                            group-hover:scale-110 transition duration-300">

                    <i data-lucide="{{ $card['icon'] }}" 
                       class="w-6 h-6 text-{{ $card['color'] }}-600">
                    </i>

                </div>

            </div>

        </div>
    @endforeach

</div>

<!-- GRÁFICOS -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <h3 class="font-semibold text-gray-700 mb-4">Ventas Mensuales</h3>
        <div class="h-72 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 text-sm">
            Aquí irá el gráfico de línea
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <h3 class="font-semibold text-gray-700 mb-4">Órdenes por Mes</h3>
        <div class="h-72 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 text-sm">
            Aquí irá el gráfico de barras
        </div>
    </div>

</div>

@endsection

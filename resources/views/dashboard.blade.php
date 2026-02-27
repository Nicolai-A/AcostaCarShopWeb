@extends('layouts.app')

@section('content')

<h1 class="text-2xl font-semibold text-gray-800">Dashboard</h1>
<p class="text-gray-500 text-sm mb-8">Resumen general del sistema</p>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-10">

    {{-- Tarjeta Clientes (Ahora todas usan el diseño azul) --}}
    <x-dashboard-card title="Total Clientes" :value="$totalClientes" icon="users" />
    
    <x-dashboard-card title="Vehículos Registrados" :value="$totalVehiculos" icon="car" />
    
    <x-dashboard-card title="Órdenes Activas" :value="$ordenesActivas" icon="clipboard-list" />
    
    {{-- Tarjeta Ganancia ($) --}}
    <x-dashboard-card title="Ganancia del Mes" :value="'$' . number_format($gananciaMes, 2)" icon="dollar-sign" />
    
    <x-dashboard-card title="Unidades en Inventario" :value="$totalProductos" icon="package" />
    
    <x-dashboard-card title="Órdenes Completadas" :value="$ordenesCompletadas" icon="check-circle" />

    </div>
    @if($productosBajoStock->isNotEmpty())
    <div class="bg-white p-6 rounded-2xl border border-red-100 shadow-sm mb-10">
        <div class="flex items-center gap-3 mb-4">
            <div class="p-2 bg-red-100 rounded-lg text-red-600">⚠️</div>
            <h3 class="font-semibold text-gray-800">Alerta de Stock Crítico</h3>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @foreach($productosBajoStock as $producto)
                <div class="bg-red-50 p-4 rounded-xl border border-red-100">
                    <p class="text-sm font-medium text-gray-700">{{ $producto->nombre }}</p>
                    <p class="text-2xl font-bold text-red-600">{{ $producto->stock }} <span class="text-xs font-normal">unidades</span></p>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm lg:col-span-2">
            <h3 class="font-semibold text-gray-700 mb-4">🏆 Top 5 Clientes (Por Gasto)</h3>
            <div class="space-y-3">
                @forelse($rankingClientes as $cliente)
                    <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">👤</span>
                            <div>
                                <p class="font-medium text-gray-800">{{ $cliente->nombre }} {{ $cliente->apellido }}</p>
                                <p class="text-xs text-gray-500">{{ $cliente->ordenes->count() }} órdenes</p>
                            </div>
                        </div>
                        <span class="font-bold text-lg text-blue-600">
                            ${{ number_format($cliente->ordenes_sum_total ?? 0, 2) }}
                        </span>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4">Aún no hay clientes con órdenes finalizadas.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <h3 class="font-semibold text-gray-700 mb-4">📦 Estado de Inventario</h3>
            <div class="text-center bg-indigo-50 p-6 rounded-xl border border-indigo-100">
                <p class="text-sm text-indigo-700 mb-1">Valor Total en Stock (Venta)</p>
                <p class="text-4xl font-black text-indigo-900">${{ number_format($valorInventario, 2) }}</p>
                <p class="text-xs text-indigo-600 mt-2">Basado en {{ \App\Models\Producto::sum('stock') }} unidades totales</p>
            </div>
        </div>

    </div>





<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <h3 class="font-semibold text-gray-700 mb-4">Ventas Mensuales (Últimos 6 meses)</h3>
        <canvas id="ventasChart" class="h-72"></canvas>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <h3 class="font-semibold text-gray-700 mb-4">Órdenes por Mes (Últimos 6 meses)</h3>
        <canvas id="ordenesChart" class="h-72"></canvas>
    </div>

</div>
<div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-10">
    <h3 class="font-semibold text-gray-700 mb-4">Ingresos del Mes: Servicios vs Productos</h3>
    <div class="flex justify-center items-center">
        <canvas id="serviciosProductosChart" class="max-h-72"></canvas>
    </div>
</div>

{{-- 🔥 Scripts para los gráficos (Chart.js) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de Ventas (Linea)
    const ctxVentas = document.getElementById('ventasChart').getContext('2d');
    new Chart(ctxVentas, {
        type: 'line',
        data: {
            // Aseguramos que los datos sean arrays válidos
            labels: {!! json_encode($ventasMensuales->pluck('mes')) !!},
            datasets: [{
                label: 'Ventas $',
                data: {!! json_encode($ventasMensuales->pluck('total_venta')) !!},
                borderColor: '#2563EB', // Azul
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                fill: true,
                tension: 0.4
            }]
        }
    });

    // Gráfico de Órdenes (Barras)
    const ctxOrdenes = document.getElementById('ordenesChart').getContext('2d');
    new Chart(ctxOrdenes, {
        type: 'bar',
        data: {
            labels: {!! json_encode($ordenesPorMes->pluck('mes')) !!},
            datasets: [{
                label: 'Cantidad de Órdenes',
                data: {!! json_encode($ordenesPorMes->pluck('total_ordenes')) !!},
                backgroundColor: '#2563EB', // Azul
            }]
        }
    });

    const ctxComparativa = document.getElementById('serviciosProductosChart').getContext('2d');
    new Chart(ctxComparativa, {
        type: 'doughnut', // O 'pie'
        data: {
            labels: ['Servicios', 'Productos'],
            datasets: [{
                data: [{{ $gananciaServicios }}, {{ $gananciaProductos }}],
                backgroundColor: ['#10B981', '#2563EB'], // Verde y Azul
                borderColor: '#ffffff',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });


</script>

@endsection
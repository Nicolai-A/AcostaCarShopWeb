<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\Orden;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Datos para las tarjetas
        $totalClientes = Cliente::count();
        $totalVehiculos = Vehiculo::count();
        $ordenesActivas = Orden::where('estado', 'Pendiente')->count();
        $totalProductos = Producto::sum('stock');
        
        $gananciaMes = Orden::where('estado', 'Finalizado')
                            ->whereMonth('fecha', Carbon::now()->month)
                            ->whereYear('fecha', Carbon::now()->year)
                            ->sum('total');

        $ordenesCompletadas = Orden::where('estado', 'Finalizado')
                                    ->whereMonth('fecha', Carbon::now()->month)
                                    ->whereYear('fecha', Carbon::now()->year)
                                    ->count();

        // 2. 🔥 Datos para Gráfico 1: Ventas Mensuales (Últimos 6 meses)
    // 2. 🔥 SOLUCIÓN DEFINITIVA: Usar una subconsulta para agrupar
        $ventasMensuales = Orden::where('estado', 'Finalizado')
            ->where('fecha', '>=', Carbon::now()->subMonths(6))
            ->select(
                DB::raw("DATE_FORMAT(fecha, '%M %Y') as mes"),
                DB::raw('SUM(total) as total_venta'),
                DB::raw("DATE_FORMAT(fecha, '%Y-%m') as mes_orden") // Alias para ordenar
            )
            ->groupBy('mes', 'mes_orden')
            ->orderBy('mes_orden', 'asc')
            ->get();

        // 3. 🔥 SOLUCIÓN DEFINITIVA: Usar una subconsulta para agrupar
        $ordenesPorMes = Orden::where('fecha', '>=', Carbon::now()->subMonths(6))
            ->select(
                DB::raw("DATE_FORMAT(fecha, '%M %Y') as mes"),
                DB::raw('COUNT(id) as total_ordenes'),
                DB::raw("DATE_FORMAT(fecha, '%Y-%m') as mes_orden") // Alias para ordenar
            )
            ->groupBy('mes', 'mes_orden')
            ->orderBy('mes_orden', 'asc')
            ->get();
        // 🔥 NUEVO: Productos con bajo stock (ej: menos de 5 unidades)
        $productosBajoStock = Producto::where('stock', '<', 5)
                                    ->orderBy('stock', 'asc')
                                    ->take(5) // Mostrar los 5 más críticos
                                    ->get();

        // 🔥 NUEVO: Desglose de ganancias del mes
        $gananciaServicios = Orden::where('estado', 'Finalizado')
                                ->whereMonth('fecha', Carbon::now()->month)
                                ->whereYear('fecha', Carbon::now()->year)
                                ->whereHas('servicios') // Asegurar que tenga servicios
                                ->with('servicios')
                                ->get()
                                ->sum(function($orden) {
                                    return $orden->servicios->sum('pivot.precio');
                                });

        $gananciaProductos = Orden::where('estado', 'Finalizado')
                                ->whereMonth('fecha', Carbon::now()->month)
                                ->whereYear('fecha', Carbon::now()->year)
                                ->whereHas('productos') // Asegurar que tenga productos
                                ->with('productos')
                                ->get()
                                ->sum(function($orden) {
                                    // Solo sumar productos marcados como 'cobrar'
                                    return $orden->productos->where('pivot.cobrar', true)->sum(function($p) {
                                        return $p->pivot->precio_unitario * $p->pivot->cantidad;
                                    });
                                });
                                    


        // 🔥 NUEVO: Ranking de Clientes (Los 5 que más han gastado en órdenes finalizadas)
        $rankingClientes = Cliente::select('id', 'nombre', 'apellido')
                ->withSum(['ordenes' => function($query) {
                    $query->where('estado', 'Finalizado');
                }], 'total')
                ->orderBy('ordenes_sum_total', 'desc')
                ->take(5)
                ->get();

            // 🔥 NUEVO: Gestión de Estado de Inventario (Valor total del inventario)
            // Asumiendo que usas 'precio_venta' para valorar el stock actual
        $valorInventario = Producto::sum(DB::raw('stock * precio_venta'));
        
        return view('dashboard', compact(
            'totalClientes', 'totalVehiculos', 'ordenesActivas',
            'gananciaMes', 'totalProductos', 'ordenesCompletadas',
            'ventasMensuales', 'ordenesPorMes','productosBajoStock','gananciaServicios',
            'gananciaProductos','rankingClientes','valorInventario'
        ));
    }
}
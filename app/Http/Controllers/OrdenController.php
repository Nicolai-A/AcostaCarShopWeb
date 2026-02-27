<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\Producto;
use App\Models\Servicio;
use App\Models\Orden;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\OrdenFacturaMail;
use Illuminate\Support\Facades\Mail;    

class OrdenController extends Controller
{
    public function index()
    {
        $ordenes = Orden::with([
            'cliente' => fn($q) => $q->withTrashed(), 
            'vehiculo' => fn($q) => $q->withTrashed(),
            'productos',
            'servicios'
        ])->get();
        $clientes = Cliente::with('vehiculos')->get();
        $ordenesEliminadas = Orden::onlyTrashed()
            ->with([
                'cliente' => fn($q) => $q->withTrashed(), 
                'vehiculo' => fn($q) => $q->withTrashed()
            ])->get();
        $servicios = Servicio::all();
        $vehiculos = Vehiculo::all();

        return view('ordenes.index', compact('ordenes','ordenesEliminadas', 'clientes', 'servicios', 'vehiculos'));
    }

    public function create()
    {
        $clientes = Cliente::with('vehiculos')->get();
        $servicios = Servicio::all();
        $vehiculos = Vehiculo::all();
        $productos = Producto::where('stock', '>', 0)->get(); // Cargar productos con stock

        return view('ordenes.create', compact('clientes','servicios','vehiculos', 'productos'));
    }

    public function store(Request $request)
    {
        $validated=$request->validate([
            'cliente_id'=>'required|exists:clientes,id',
            'servicios'   => 'nullable|array',
            'productos'  => 'nullable|array',
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'fecha' => 'required|date',
            'total' => 'required|numeric|min:0.01',
            'costo_insumos' => 'nullable|numeric|min:0',
            'notas_insumos' => 'nullable|string',       
        ]);
        
        $orden = Orden::create([
            'cliente_id'  => $request->cliente_id,
            'vehiculo_id' => $request->vehiculo_id,
            'fecha'       => $request->fecha,
            'total'       => $request->total,
            'estado'      => 'Pendiente',
            'costo_insumos' => $request->costo_insumos ?? 0,
            'notas_insumos' => $request->notas_insumos,
        ]);

        if ($request->has('servicios') && is_array($request->servicios)) {
            foreach ($request->servicios as $servicio) {
                $orden->servicios()->attach($servicio['id'], [
                    'precio' => $servicio['precio']
                ]);
            }
        }
        if ($request->has('productos')) {
            foreach ($request->productos as $prodData) {
            $producto = Producto::find($prodData['id']);
            
            if ($producto && $producto->stock >= $prodData['cantidad']) {
                $orden->productos()->attach($prodData['id'], [
                    'cantidad' => $prodData['cantidad'],
                    'precio_unitario' => $prodData['precio'],
                    // Guardar si se cobra o no, por defecto true si no viene en el request
                    'cobrar' => isset($prodData['cobrar']) ? true : false, 
                ]);

                $producto->decrement('stock', $prodData['cantidad']);
            }
        }
    }

        return redirect()->route('ordenes.index')
            ->with('Success','Orden registrado correctamente');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'fecha' => 'required|date',
            'total' => 'required|numeric',
            //'servicios' => 'required|array|min:1',
            'costo_insumos' => 'nullable|numeric|min:0',
            'notas_insumos' => 'nullable|string',
        ]);
        
        $orden = Orden::findOrFail($id);

    // 1. --- LOGICA DE STOCK PARA PRODUCTOS ---

    // A. Revertir stock de los productos actuales antes de actualizar
    foreach ($orden->productos as $productoActual) {
        $productoActual->increment('stock', $productoActual->pivot->cantidad);
    }
    
    // B. Quitar la vinculación actual
    $orden->productos()->detach();

    // C. Vincular nuevos productos y restar stock
    if ($request->has('productos')) {
        $productosSync = [];
        foreach ($request->productos as $prodData) {
            $producto = Producto::find($prodData['id']);
            
            if ($producto && $producto->stock >= $prodData['cantidad']) {
                // Preparamos los datos para el sync
                $productosSync[$prodData['id']] = [
                    'cantidad' => $prodData['cantidad'],
                    'precio_unitario' => $prodData['precio'],
                    // 🔥 AJUSTE AQUÍ: Asegurar que el booleano llega correctamente
                    'cobrar' => filter_var($prodData['cobrar'], FILTER_VALIDATE_BOOLEAN), 
                ];

                // Restamos el stock
                $producto->decrement('stock', $prodData['cantidad']);
            }
        }
        // 🔥 SOLUCIÓN: Usar sync con los datos mapeados
        $orden->productos()->sync($productosSync);
    }

    // 2. Actualizar datos de la orden
    $orden->update([
        'vehiculo_id' => $request->vehiculo_id,
        'fecha' => $request->fecha,
        'total' => $request->total,
        'costo_insumos' => $request->costo_insumos ?? 0,
        'notas_insumos' => $request->notas_insumos,
    ]);

    // 3. --- LOGICA DE SERVICIOS ---
    if ($request->has('servicios')) {
        $serviciosSync = [];
        foreach ($request->servicios as $s) {
            $serviciosSync[$s['id']] = ['precio' => $s['precio']];
        }
        $orden->servicios()->sync($serviciosSync);
    } else {
        $orden->servicios()->detach(); // Si no hay servicios, quitarlos todos
    }

    return redirect()->route('ordenes.index')->with('success', 'Orden actualizada correctamente');
}

    public function destroy(string $id)
    {
        $orden = Orden::findOrFail($id);
        $orden->delete(); // Esto la mueve a la papelera (SoftDelete)
            return back()->with('success', 'Orden enviada a la papelera');
    }
    public function restore($id)
    {
        Orden::withTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Orden restaurada correctamente');
    }

    public function forceDelete($id)
    {
        Orden::withTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Orden eliminada definitivamente');
    }
    public function descargarPDF($id)
    {
        $orden = Orden::with(['cliente', 'vehiculo', 'servicios'])->findOrFail($id);
        
        // Cargamos la vista y le pasamos la variable $orden
        $pdf = Pdf::loadView('ordenes.pdf', compact('orden'));
        
        // Esto descargará el archivo directamente en tu navegador
        return $pdf->download('orden_trabajo_'.$orden->id.'.pdf');
    }
    public function enviarEmail($id)
    {
        try {
            $orden = Orden::with(['cliente', 'vehiculo', 'servicios'])->findOrFail($id);
            Mail::to($orden->cliente->email)->send(new \App\Mail\OrdenFacturaMail($orden));
            
            return back()->with('success', 'Email enviado');
        } catch (\Exception $e) {
            // Esto te dirá exactamente qué falló si hay un error
            return back()->with('error', 'Error al enviar: ' . $e->getMessage());
        }
    }
    public function actualizarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:Pendiente,Finalizado,Cancelado',
        ]);

        $orden = Orden::findOrFail($id);
        $orden->estado = $request->estado;
        $orden->save();

        return redirect()->back()->with('success', 'Estado de la orden actualizado a ' . $request->estado);
    }
}
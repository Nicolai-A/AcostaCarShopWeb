<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\Servicio;
use App\Models\Orden;

class OrdenController extends Controller
{
    public function index()
    {
        $ordenes = Orden::with([
            'cliente' => fn($q) => $q->withTrashed(), 
            'vehiculo' => fn($q) => $q->withTrashed(),
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

        return view('ordenes.create', compact('clientes','servicios','vehiculos'));
    }

    public function store(Request $request)
    {
        $validated=$request->validate([
            'cliente_id'=>'required|exists:clientes,id',
            'servicios'   => 'required|array|min:1',
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

        foreach ($request->servicios as $servicio) {
            $orden->servicios()->attach($servicio['id'], [
                'precio' => $servicio['precio']
            ]);
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
            'servicios' => 'required|array|min:1',
            'costo_insumos' => 'nullable|numeric|min:0',
            'notas_insumos' => 'nullable|string',
        ]);
        
        $orden = Orden::findOrFail($id);
        
        $orden->update([
            'vehiculo_id' => $request->vehiculo_id,
            'fecha' => $request->fecha, // No olvides actualizar la fecha si se cambió
            'total' => $request->total,
            'costo_insumos' => $request->costo_insumos ?? 0,
            'notas_insumos' => $request->notas_insumos,
        ]);

        $serviciosSync = [];
        foreach ($request->servicios as $s) {
            $serviciosSync[$s['id']] = ['precio' => $s['precio']];
        }
        $orden->servicios()->sync($serviciosSync);

        return redirect()->route('ordenes.index')->with('success', 'Orden actualizada');
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
    
}
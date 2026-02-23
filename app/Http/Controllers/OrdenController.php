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
        $ordenes=Orden::with(['cliente', 'vehiculo','servicios'])->get();
        $clientes = Cliente::with('vehiculos')->get();
        $servicios = Servicio::all();
        $vehiculos = Vehiculo::all();

        return view('ordenes.index', compact('ordenes', 'clientes', 'servicios', 'vehiculos'));
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
        ]);
        
        $orden = Orden::create([
            'cliente_id'  => $request->cliente_id,
            'vehiculo_id' => $request->vehiculo_id,
            'fecha'       => $request->fecha,
            'total'       => $request->total,
            'estado'      => 'Pendiente',
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
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
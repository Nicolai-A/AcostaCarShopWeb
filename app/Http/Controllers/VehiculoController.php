<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Cliente;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function index(Request $request)
{
    $query = Vehiculo::with('cliente');

    if ($request->filled('buscar')) {
        $query->where(function($q) use ($request) {
            $q->where('marca','like','%'.$request->buscar.'%')
              ->orWhere('modelo','like','%'.$request->buscar.'%')
              ->orWhere('placa','like','%'.$request->buscar.'%')
              ->orWhereHas('cliente', function($q2) use ($request){
                  $q2->where('nombre','like','%'.$request->buscar.'%')
                     ->orWhere('apellido','like','%'.$request->buscar.'%');
              });
        });
    }

    $vehiculos = $query->latest()->paginate(5)->withQueryString();
    $eliminados = Vehiculo::onlyTrashed()->with('cliente')->get();
    $clientes = Cliente::all();

    return view('vehiculos.index', compact('vehiculos','clientes','eliminados'));
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:50',
            'anio' => 'required|digits:4|integer|min:1600|max:' . date('Y'),
            'placa' => 'required|string|max:20|unique:vehiculos',
            'color' => 'nullable|string|max:30',
        ]);

        Vehiculo::create($validated);

        return redirect()->route('vehiculos.index')
            ->with('success','Vehículo registrado correctamente');
    }

    public function update(Request $request, Vehiculo $vehiculo)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:50',
            'anio' => 'required|digits:4|integer|min:1900|max:' . date('Y'),
            'placa' => 'required|string|max:20|unique:vehiculos,placa,' . $vehiculo->id,
            'color' => 'nullable|string|max:30',
        ]);

        $vehiculo->update($validated);

        return redirect()->route('vehiculos.index')
            ->with('success','Vehículo actualizado correctamente');
    }

    public function destroy(Vehiculo $vehiculo)
    {
        $vehiculo->delete();

        return redirect()->route('vehiculos.index')
            ->with('success','Vehículo eliminado correctamente');
    }

    public function restore($id)
    {
        Vehiculo::withTrashed()->findOrFail($id)->restore();
        return back()->with('success','Vehículo restaurado');
    }

    public function forceDelete($id)
    {
        Vehiculo::withTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success','Vehículo eliminado definitivamente');
    }
}


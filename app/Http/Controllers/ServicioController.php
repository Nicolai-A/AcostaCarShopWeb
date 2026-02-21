<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    public function index()
    {
        $servicios = Servicio::latest()->paginate(10);
        $eliminados = Servicio::onlyTrashed()->get();

        return view('servicios.index', compact('servicios', 'eliminados'));
    }

    public function create()
    {
        return view('servicios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|min:3|max:100',
            'descripcion' => 'required|min:3|max:100',
            'precio' => 'required|numeric|min:0'
        ]);

        Servicio::create($request->all());

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio creado correctamente');
    }

    public function edit(Servicio $servicio)
    {
        return view('servicios.edit', compact('servicio'));
    }

    public function update(Request $request, Servicio $servicio)
    {
        $request->validate([
            'nombre' => 'required|min:3|max:100',
            'descripcion' => 'required|min:3|max:100',
            'precio' => 'required|numeric|min:0'
        ]);

        $servicio->update($request->all());

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio actualizado correctamente');
    }

    public function destroy(Servicio $servicio)
    {
        $servicio->delete();

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio eliminado correctamente');
    }

    public function restore($id)
    {
        Servicio::onlyTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Servicio restaurado');
    }

    public function forceDelete($id)
    {
        Servicio::onlyTrashed()->findOrFail($id)->forceDelete();

        return back()->with('success', 'Servicio eliminado definitivamente');
    }
}
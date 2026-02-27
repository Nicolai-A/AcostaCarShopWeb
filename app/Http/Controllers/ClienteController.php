<?php

namespace App\Http\Controllers;
use App\Models\Cliente;
use Illuminate\Http\Request;

    class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    // 🔎 CLIENTES ACTIVOS con conteo de vehículos
    $query = Cliente::with([
    'vehiculos' => function($q){
            $q->latest();
        }
    ])->withCount('vehiculos');

        if ($request->filled('buscar')) {
        $query->where(function($q) use ($request) {
            $q->where('nombre', 'like', '%' . $request->buscar . '%')
              ->orWhere('apellido', 'like', '%' . $request->buscar . '%')
              ->orWhere('email', 'like', '%' . $request->buscar . '%');
        });
    }

        $clientes = $query->latest()
                      ->paginate(5)
                      ->withQueryString();

    // 🔎 ELIMINADOS
        $eliminados = Cliente::onlyTrashed()
                        ->withCount('vehiculos')
                        ->latest()
                        ->get();
        
    return view('clientes.index', compact('clientes', 'eliminados'));
}



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clientes.create');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => [
                'required',         
                'string',
                'min:2',
                'max:50',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/'
            ],

            'apellido' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/'
            ],

            'cedula' => [
                'required',
                'string', 
                'digits_between:10,13', 
                'unique:clientes,cedula'
            ],

            'telefono' => [
                'required',
                'digits_between:7,15'
            ],

            'email' => [
                'required',
                'email:rfc,dns',
                'max:100'
            ],

            'direccion' => [
                'nullable',
                'string',
                'min:2',
                'max:150'
            ],
        ], [
            // MENSAJES PERSONALIZADOS
            'cedula.required' => 'La cédula o RUC es obligatorio.',
            'cedula.digits_between' => 'La cédula debe tener 10 dígitos y el RUC 13.',
            'cedula.unique' => 'Esta cédula ya está registrada.',

            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.regex' => 'El nombre solo puede contener letras.',

            'apellido.required' => 'El apellido es obligatorio.',
            'apellido.regex' => 'El apellido solo puede contener letras.',

            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.digits_between' => 'El teléfono debe tener entre 7 y 15 números.',

            'email.required' => 'El email es obligatorio.',
            'email.email' => 'Debe ingresar un email válido.',

            'direccion.required' => 'La dirección es obligatoria.',
            'direccion.min' => 'La dirección debe tener mínimo 3 caracteres.'
        ]);

        Cliente::create($validated);

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
    return view('clientes.edit', compact('cliente'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre' => 'required|min:3',
            'apellido' => 'required|min:3',
            'cedula' => 'required|digits_between:10,13|unique:clientes,cedula,'.$cliente->id, // Ignora el ID actual
            'email' => 'nullable|email',
        ]);

        $cliente->update($request->all());

        return redirect()->route('clientes.index')
                        ->with('success', 'Cliente actualizado correctamente');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')
                        ->with('success', 'Cliente eliminado correctamente');
    }
public function restore($id)
{
    Cliente::onlyTrashed()->findOrFail($id)->restore();
    return redirect()->route('clientes.index')
                     ->with('success', 'Cliente restaurado correctamente');
}

public function forceDelete($id)
{
    Cliente::onlyTrashed()->findOrFail($id)->forceDelete();
    return redirect()->route('clientes.index')
                     ->with('success', 'Cliente eliminado definitivamente');
}












}




<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Mostrar el inventario
     */
    public function index()
    {
        $productos = Producto::latest()->paginate(10);
        return view('productos.index', compact('productos'));
    }

    /**
     * Guardar un nuevo producto
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'stock' => 'required|integer|min:0',
            'precio_venta' => 'required|numeric|min:0',
        ]);

        Producto::create($validated);

        return redirect()->route('productos.index')
                        ->with('success', 'Producto agregado al inventario correctamente.');
    }

    /**
     * Actualizar producto existente
     */
    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'stock' => 'required|integer|min:0',
            'precio_venta' => 'required|numeric|min:0',
        ]);

        $producto->update($validated);

        return redirect()->route('productos.index')
                        ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Eliminar producto (opcional)
     */
    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('productos.index')
                        ->with('success', 'Producto eliminado.');
    }
}

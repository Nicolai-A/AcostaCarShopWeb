@extends('layouts.app')

@section('content')
<div x-data="{ openCreate: false, openEdit: null, search: '' }">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Inventario</h1>
        <button @click="openCreate = true" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl">
            + Nuevo Producto
        </button>
    </div>

    <div class="mb-4">
        <input type="text" x-model="search" placeholder="Buscar producto..." class="w-full md:w-1/3 border rounded-xl px-4 py-2">
    </div>

    <div class="bg-white rounded-2xl shadow border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                <tr>
                    <th class="px-6 py-4 text-left">Nombre</th>
                    <th class="px-6 py-4 text-center">Stock</th>
                    <th class="px-6 py-4 text-center">Precio Venta</th>
                    <th class="px-6 py-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($productos as $producto)
                <tr class="hover:bg-gray-50" x-show="'{{ strtolower($producto->nombre) }}'.includes(search.toLowerCase())">
                    <td class="px-6 py-4 font-medium">{{ $producto->nombre }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-full {{ $producto->stock <= 3 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ $producto->stock }} unidades
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center font-bold text-blue-600">
                        ${{ number_format($producto->precio_venta, 2) }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button @click="openEdit = {{ $producto->id }}" class="text-yellow-500 hover:text-yellow-700 text-xl">✏️</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-6 text-gray-400">No hay productos en inventario</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div x-show="openCreate" class="fixed inset-0 flex items-center justify-center z-50">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="openCreate=false"></div>
        <div class="bg-white rounded-2xl p-8 w-full max-w-md z-50">
            <h2 class="text-xl font-semibold mb-6">Agregar Producto</h2>
            <form action="{{ route('productos.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="text-xs text-gray-500">Nombre del producto</label>
                        <input name="nombre" class="w-full border rounded-xl px-4 py-2" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-gray-500">Stock Inicial</label>
                            <input type="number" name="stock" class="w-full border rounded-xl px-4 py-2" required>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Precio Venta</label>
                            <input type="number" step="0.01" name="precio_venta" class="w-full border rounded-xl px-4 py-2" required>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-4 mt-6">
                    <button type="button" @click="openCreate=false" class="px-4 py-2 border rounded-xl">Cancelar</button>
                    <button class="bg-blue-600 text-white px-6 py-2 rounded-xl">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    @foreach($productos as $producto)
    <div x-show="openEdit === {{ $producto->id }}" class="fixed inset-0 flex items-center justify-center z-50">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="openEdit=null"></div>
        <div class="bg-white rounded-2xl p-8 w-full max-w-md z-50">
            <h2 class="text-xl font-semibold mb-6">Editar Producto</h2>
            <form action="{{ route('productos.update', $producto->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="space-y-4">
                    <input name="nombre" value="{{ $producto->nombre }}" class="w-full border rounded-xl px-4 py-2">
                    <div class="grid grid-cols-2 gap-4">
                        <input type="number" name="stock" value="{{ $producto->stock }}" class="w-full border rounded-xl px-4 py-2">
                        <input type="number" step="0.01" name="precio_venta" value="{{ $producto->precio_venta }}" class="w-full border rounded-xl px-4 py-2">
                    </div>
                </div>
                <div class="flex justify-end gap-4 mt-6">
                    <button type="button" @click="openEdit=null" class="px-4 py-2 border rounded-xl">Cancelar</button>
                    <button class="bg-blue-500 text-white px-6 py-2 rounded-xl">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endsection

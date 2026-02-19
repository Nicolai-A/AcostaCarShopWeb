@extends('layouts.app')

@section('content')

<h1 class="text-2xl font-semibold mb-6">Editar Cliente</h1>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-2xl">

    <form action="{{ route('clientes.update', $cliente) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-2 gap-6">

            <div>
                <label>Nombre</label>
                <input type="text" name="nombre"
                       value="{{ $cliente->nombre }}"
                       class="w-full mt-2 border rounded-xl px-3 py-2">
            </div>

            <div>
                <label>Apellido</label>
                <input type="text" name="apellido"
                       value="{{ $cliente->apellido }}"
                       class="w-full mt-2 border rounded-xl px-3 py-2">
            </div>

            <div>
                <label>Teléfono</label>
                <input type="text" name="telefono"
                       value="{{ $cliente->telefono }}"
                       class="w-full mt-2 border rounded-xl px-3 py-2">
            </div>

            <div>
                <label>Email</label>
                <input type="email" name="email"
                       value="{{ $cliente->email }}"
                       class="w-full mt-2 border rounded-xl px-3 py-2">
            </div>

            <div class="col-span-2">
                <label>Dirección</label>
                <input type="text" name="direccion"
                       value="{{ $cliente->direccion }}"
                       class="w-full mt-2 border rounded-xl px-3 py-2">
            </div>

        </div>

        <div class="mt-6">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl">
                Actualizar Cliente
            </button>
        </div>

    </form>

</div>

@endsection

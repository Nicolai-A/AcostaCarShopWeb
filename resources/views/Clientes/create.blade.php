@extends('layouts.app')

@section('content')

<h1 class="text-2xl font-semibold mb-6">Nuevo Cliente</h1>

@if($errors->any())
    <div class="bg-red-100 text-red-700 px-4 py-3 rounded-xl mb-4">
        Por favor corrige los errores del formulario.
    </div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-2xl">

    <form action="{{ route('clientes.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-2 gap-6">

            <!-- Nombre -->
            <div>
                <label class="text-sm text-gray-600">Nombre</label>
                <input type="text" 
                       name="nombre" 
                       value="{{ old('nombre') }}"
                       class="w-full mt-2 border rounded-xl px-3 py-2 
                       @error('nombre') border-red-500 @enderror">

                @error('nombre')
                    <div class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Apellido -->
            <div>
                <label class="text-sm text-gray-600">Apellido</label>
                <input type="text" 
                       name="apellido"
                       value="{{ old('apellido') }}"
                       class="w-full mt-2 border rounded-xl px-3 py-2 
                       @error('apellido') border-red-500 @enderror">

                @error('apellido')
                    <div class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Teléfono -->
            <div>
                <label class="text-sm text-gray-600">Teléfono</label>
                <input type="text" 
                       name="telefono"
                       value="{{ old('telefono') }}"
                       class="w-full mt-2 border rounded-xl px-3 py-2 
                       @error('telefono') border-red-500 @enderror">

                @error('telefono')
                    <div class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="text-sm text-gray-600">Email</label>
                <input type="email" 
                       name="email"
                       value="{{ old('email') }}"
                       class="w-full mt-2 border rounded-xl px-3 py-2 
                       @error('email') border-red-500 @enderror">

                @error('email')
                    <div class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Dirección -->
            <div class="col-span-2">
                <label class="text-sm text-gray-600">Dirección</label>
                <input type="text" 
                       name="direccion"
                       value="{{ old('direccion') }}"
                       class="w-full mt-2 border rounded-xl px-3 py-2 
                       @error('direccion') border-red-500 @enderror">

                @error('direccion')
                    <div class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>

        </div>

        <div class="mt-6">
            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-xl">
                Guardar Cliente
            </button>
        </div>

    </form>

</div>

@endsection


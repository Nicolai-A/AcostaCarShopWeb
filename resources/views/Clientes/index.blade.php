@extends('layouts.app')

@section('content')

<div x-data="{ 
    openCreate: {{ $errors->any() ? 'true' : 'false' }},
    openEdit: null,
    openDelete: null,
    openTrash: false
}">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Clientes</h1>

        <div class="space-x-3">
            <button @click="openTrash = true"
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-xl">
                Ver Eliminados
            </button>

            <button @click="openCreate = true"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl">
                + Nuevo Cliente
            </button>
        </div>
    </div>

    <!-- TABLA -->
    <div class="bg-white rounded-2xl shadow border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                <tr>
                    <th class="px-6 py-4">Nombre</th>
                    <th class="px-6 py-4">Teléfono</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Dirección</th>
                    <th class="px-6 py-4 text-center">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($clientes as $cliente)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        {{ $cliente->nombre }} {{ $cliente->apellido }}
                    </td>
                    <td class="px-6 py-4">{{ $cliente->telefono }}</td>
                    <td class="px-6 py-4">{{ $cliente->email }}</td>
                    <td class="px-6 py-4">{{ $cliente->direccion }}</td>

                    <td class="px-6 py-4 text-center space-x-3">

                        <!-- EDITAR -->
                        <button @click="openEdit = {{ $cliente->id }}"
                            class="text-yellow-500 hover:text-yellow-700">
                            ✏️
                        </button>

                        <!-- ELIMINAR -->
                        <button @click="openDelete = {{ $cliente->id }}"
                            class="text-red-600 hover:text-red-800">
                            🗑️
                        </button>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-6 text-gray-400">
                        No hay clientes registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINACIÓN -->
    <div class="mt-6">
        {{ $clientes->links() }}
    </div>


    <!-- ===================== MODAL CREAR ===================== -->
    <div x-show="openCreate" class="fixed inset-0 flex items-center justify-center z-50">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            @click="openCreate=false"></div>

        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg z-50">
            <h2 class="text-xl font-semibold mb-6">Nuevo Cliente</h2>

            <form action="{{ route('clientes.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <input name="nombre" placeholder="Nombre" class="w-full border rounded-xl px-4 py-2">
                    <input name="apellido" placeholder="Apellido" class="w-full border rounded-xl px-4 py-2">
                    <input name="telefono" placeholder="Celular" class="w-full border rounded-xl px-4 py-2">
                    <input name="email" placeholder="Email" class="w-full border rounded-xl px-4 py-2">
                    <input name="direccion" placeholder="Dirección" class="w-full border rounded-xl px-4 py-2">
                </div>

                <div class="flex justify-end gap-4 mt-6">
                    <button type="button" @click="openCreate=false"
                        class="px-4 py-2 border rounded-xl">Cancelar</button>

                    <button class="bg-blue-600 text-white px-6 py-2 rounded-xl">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- ===================== MODAL EDITAR ===================== -->
    @foreach($clientes as $cliente)
    <div x-show="openEdit === {{ $cliente->id }}"
        class="fixed inset-0 flex items-center justify-center z-50">

        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            @click="openEdit=null"></div>

        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg z-50">
            <h2 class="text-xl font-semibold mb-6">Editar Cliente</h2>

            <form action="{{ route('clientes.update',$cliente->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <input name="nombre" value="{{ $cliente->nombre }}" class="w-full border rounded-xl px-4 py-2">
                    <input name="apellido" value="{{ $cliente->apellido }}" class="w-full border rounded-xl px-4 py-2">
                    <input name="telefono" value="{{ $cliente->telefono }}" class="w-full border rounded-xl px-4 py-2">
                    <input name="email" value="{{ $cliente->email }}" class="w-full border rounded-xl px-4 py-2">
                    <input name="direccion" value="{{ $cliente->direccion }}" class="w-full border rounded-xl px-4 py-2">
                </div>

                <div class="flex justify-end gap-4 mt-6">
                    <button type="button" @click="openEdit=null"
                        class="px-4 py-2 border rounded-xl">Cancelar</button>

                    <button class="bg-yellow-500 text-white px-6 py-2 rounded-xl">
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endforeach


    <!-- ===================== MODAL ELIMINAR ===================== -->
    @foreach($clientes as $cliente)
    <div x-show="openDelete === {{ $cliente->id }}"
        class="fixed inset-0 flex items-center justify-center z-50">

        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            @click="openDelete=null"></div>

        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md z-50 text-center">
            <h2 class="text-lg font-semibold mb-6">
                ¿Eliminar este cliente?
            </h2>

            <form action="{{ route('clientes.destroy',$cliente->id) }}" method="POST">
                @csrf
                @method('DELETE')

                <div class="flex justify-center gap-4">
                    <button type="button" @click="openDelete=null"
                        class="px-4 py-2 border rounded-xl">Cancelar</button>

                    <button class="bg-red-600 text-white px-6 py-2 rounded-xl">
                        Eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endforeach


    <!-- ===================== MODAL PAPELERA ===================== -->
    <div x-show="openTrash"
        class="fixed inset-0 flex items-center justify-center z-50">

        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            @click="openTrash=false"></div>

        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-3xl z-50">
            <h2 class="text-xl font-semibold mb-6">Clientes Eliminados</h2>

            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-2">Nombre</th>
                        <th class="px-4 py-2 text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($eliminados as $cliente)
                    <tr class="border-b">
                        <td class="px-4 py-2">
                            {{ $cliente->nombre }} {{ $cliente->apellido }}
                        </td>
                        <td class="px-4 py-2 text-center space-x-3">

                            <!-- RESTAURAR -->
                            <form action="{{ route('clientes.restore',$cliente->id) }}"
                                method="POST" class="inline">
                                @csrf
                                <button class="bg-green-600 text-white px-3 py-1 rounded-lg">
                                    Restaurar
                                </button>
                            </form>

                            <!-- ELIMINAR DEFINITIVO -->
                            <form action="{{ route('clientes.forceDelete',$cliente->id) }}"
                                method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-600 text-white px-3 py-1 rounded-lg">
                                    Eliminar Definitivo
                                </button>
                            </form>

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center py-6 text-gray-400">
                            No hay clientes eliminados
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="flex justify-end mt-6">
                <button @click="openTrash=false"
                    class="px-4 py-2 border rounded-xl">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

</div>

@endsection
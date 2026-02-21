@extends('layouts.app')

@section('content')

<div x-data="{ 
    openCreate: {{ $errors->any() ? 'true' : 'false' }},
    openEdit: null,
    openDelete: null,
    openTrash: false,
    search: '',
    searchTrash: ''
}">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Vehículos</h1>

        <div class="space-x-3">
            <button @click="openTrash = true"
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-xl">
                Ver Eliminados
            </button>

            <button @click="openCreate = true"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl">
                + Nuevo Vehículo
            </button>
        </div>
    </div>

    <!-- BUSCADOR -->
    <div class="mb-4">
        <input type="text"
            x-model="search"
            placeholder="Buscar vehículo..."
            class="w-full md:w-1/3 border rounded-xl px-4 py-2">
    </div>

    <!-- TABLA -->
    <div class="bg-white rounded-2xl shadow border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                <tr>
                    <th class="px-6 py-4">Cliente</th>
                    <th class="px-6 py-4">Marca</th>
                    <th class="px-6 py-4">Modelo</th>
                    <th class="px-6 py-4">Placa</th>
                    <th class="px-4 py-2">Color</th>
                    <th class="px-6 py-4 text-center">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($vehiculos as $vehiculo)
                <tr class="hover:bg-gray-50"
                    x-show="('{{ strtolower(
                        $vehiculo->marca.' '.
                        $vehiculo->modelo.' '.
                        $vehiculo->placa.' '.
                        optional($vehiculo->cliente)->nombre.' '.
                        optional($vehiculo->cliente)->apellido
                    ) }}').includes(search.toLowerCase())"
                >
                    <td class="px-6 py-4">
                        {{ $vehiculo->cliente->nombre ?? '' }}
                        {{ $vehiculo->cliente->apellido ?? '' }}
                    </td>
                    <td class="px-6 py-4">{{ $vehiculo->marca }}</td>
                    <td class="px-6 py-4">{{ $vehiculo->modelo }}</td>
                    <td class="px-6 py-4">{{ $vehiculo->placa }}</td>
                    <td class="px-4 py-2">{{ $vehiculo->color }}</td>

                    <td class="px-6 py-4 text-center space-x-3">
                        <button @click="openEdit = {{ $vehiculo->id }}"
                            class="text-yellow-500 hover:text-yellow-700">
                            ✏️
                        </button>

                        <button @click="openDelete = {{ $vehiculo->id }}"
                            class="text-red-600 hover:text-red-800">
                            🗑️
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-6 text-gray-400">
                        No hay vehículos registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINACIÓN -->
    <div class="mt-6">
        {{ $vehiculos->links() }}
    </div>


    <!-- ===================== MODAL CREAR ===================== -->
    <div x-show="openCreate"
        class="fixed inset-0 flex items-center justify-center z-50">

        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            @click="openCreate=false"></div>

        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg z-50">
            <h2 class="text-xl font-semibold mb-6">Nuevo Vehículo</h2>

            <form action="{{ route('vehiculos.store') }}" method="POST">
                @csrf

                <div class="space-y-4">

                    <select name="cliente_id"
                        class="w-full border rounded-xl px-4 py-2">
                        <option value="">Seleccione Cliente</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">
                                {{ $cliente->nombre }} {{ $cliente->apellido }}
                            </option>
                        @endforeach
                    </select>

                    <input name="marca" placeholder="Marca"
                        class="w-full border rounded-xl px-4 py-2">

                    <input name="modelo" placeholder="Modelo"
                        class="w-full border rounded-xl px-4 py-2">

                    <input name="anio" placeholder="Año"
                        class="w-full border rounded-xl px-4 py-2">

                    <input name="placa" placeholder="Placa"
                        class="w-full border rounded-xl px-4 py-2">

                    <input name="color" placeholder="Color"
                        class="w-full border rounded-xl px-4 py-2">

                </div>

                <div class="flex justify-end gap-4 mt-6">
                    <button type="button"
                        @click="openCreate=false"
                        class="px-4 py-2 border rounded-xl">
                        Cancelar
                    </button>

                    <button
                        class="bg-blue-600 text-white px-6 py-2 rounded-xl">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- ===================== MODAL EDITAR ===================== -->
    @foreach($vehiculos as $vehiculo)
    <div x-show="openEdit === {{ $vehiculo->id }}"
        class="fixed inset-0 flex items-center justify-center z-50">

        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            @click="openEdit=null"></div>

        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg z-50">
            <h2 class="text-xl font-semibold mb-6">Editar Vehículo</h2>

            <form action="{{ route('vehiculos.update',$vehiculo->id) }}"
                method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">

                    <select name="cliente_id"
                        class="w-full border rounded-xl px-4 py-2">
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}"
                                {{ $vehiculo->cliente_id == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }} {{ $cliente->apellido }}
                            </option>
                        @endforeach
                    </select>

                    <input name="marca"
                        value="{{ $vehiculo->marca }}"
                        class="w-full border rounded-xl px-4 py-2">

                    <input name="modelo"
                        value="{{ $vehiculo->modelo }}"
                        class="w-full border rounded-xl px-4 py-2">

                    <input name="anio"
                        value="{{ $vehiculo->anio }}"
                        class="w-full border rounded-xl px-4 py-2">

                    <input name="placa"
                        value="{{ $vehiculo->placa }}"
                        class="w-full border rounded-xl px-4 py-2">

                    <input name="color"
                        value="{{ $vehiculo->color }}"
                        class="w-full border rounded-xl px-4 py-2">

                </div>

                <div class="flex justify-end gap-4 mt-6">
                    <button type="button"
                        @click="openEdit=null"
                        class="px-4 py-2 border rounded-xl">
                        Cancelar
                    </button>

                    <button
                        class="bg-yellow-500 text-white px-6 py-2 rounded-xl">
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endforeach


    <!-- ===================== MODAL ELIMINAR ===================== -->
    @foreach($vehiculos as $vehiculo)
    <div x-show="openDelete === {{ $vehiculo->id }}"
        class="fixed inset-0 flex items-center justify-center z-50">

        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            @click="openDelete=null"></div>

        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md z-50 text-center">
            <h2 class="text-lg font-semibold mb-6">
                ¿Eliminar este vehículo?
            </h2>

            <form action="{{ route('vehiculos.destroy',$vehiculo->id) }}"
                method="POST">
                @csrf
                @method('DELETE')

                <div class="flex justify-center gap-4">
                    <button type="button"
                        @click="openDelete=null"
                        class="px-4 py-2 border rounded-xl">
                        Cancelar
                    </button>

                    <button
                        class="bg-red-600 text-white px-6 py-2 rounded-xl">
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
            <h2 class="text-xl font-semibold mb-6">Vehículos Eliminados</h2>

            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-2">Cliente</th>
                        <th class="px-4 py-2">Marca</th>
                        <th class="px-4 py-2 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eliminados as $vehiculo)
                    <tr class="border-b">
                        <td class="px-4 py-2">
                            {{ $vehiculo->cliente->nombre ?? '' }}
                        </td>
                        <td class="px-4 py-2">
                            {{ $vehiculo->marca }}
                        </td>
                        <td class="px-4 py-2 text-center space-x-3">

                            <form action="{{ route('vehiculos.restore',$vehiculo->id) }}"
                                method="POST" class="inline">
                                @csrf
                                <button
                                    class="bg-green-600 text-white px-3 py-1 rounded-lg">
                                    Restaurar
                                </button>
                            </form>

                            <form action="{{ route('vehiculos.forceDelete',$vehiculo->id) }}"
                                method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button
                                    class="bg-red-600 text-white px-3 py-1 rounded-lg">
                                    Eliminar Definitivo
                                </button>
                            </form>

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3"
                            class="text-center py-6 text-gray-400">
                            No hay vehículos eliminados
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
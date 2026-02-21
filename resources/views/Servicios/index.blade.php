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
        <h1 class="text-2xl font-semibold">Servicios</h1>

        <div class="space-x-3">
            <button @click="openTrash = true"
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-xl">
                Ver Eliminados
            </button>

            <button @click="openCreate = true"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl">
                + Nuevo Servicio
            </button>
        </div>
    </div>

    <!-- BUSCADOR -->
    <div class="mb-4">
        <input type="text"
            x-model="search"
            placeholder="Buscar servicio..."
            class="w-full md:w-1/3 border rounded-xl px-4 py-2">
    </div>

    <!-- TABLA -->
    <div class="bg-white rounded-2xl shadow border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                <tr>
                    <th class="px-6 py-4">Nombre</th>
                    <th class="px-6 py-4">Descripción</th>
                    <th class="px-6 py-4">Precio</th>
                    <th class="px-4 py-2">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($servicios as $servicio)
                <tr class ="hover:bg-gray-50 text-center"
                    x-show="('{{ strtolower(
                        $servicio -> nombre
                    ) }}').includes(search.toLowerCase())"
                    >
                    <td class="px-6 py-4">{{ $servicio -> nombre}}</td>
                    <td class="px-6 py-4">{{ $servicio -> descripcion}}</td>
                    <td class="px-6 py-4">{{ $servicio -> precio}}</td>

                    <td class="px-6 py-4 text-center space-x-3">
                        <button @click="openEdit = {{ $servicio -> id }}" class="text-yellow-500 hover:text-yellow-700">
                        ✏️
                        </button>

                        <button @click="openDelete = {{ $servicio -> id }}" class="text-red-600 hover:text-red-800">
                            🗑️
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-6 text-gray-400">
                        No hay servicios registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINACIÓN -->
    <div class="mt-6">
        {{ $servicios->links() }}
    </div>


    <!-- ===================== MODAL CREAR ===================== -->
    <div x-show="openCreate"
        class="fixed inset-0 flex items-center justify-center z-50">

        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            @click="openCreate=false"></div>

        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg z-50">
            <h2 class="text-xl font-semibold mb-6">Nuevo Servicio</h2>

            <form action="{{ route('servicios.store') }}" method="POST">
                @csrf

                <div class="space-y-4">

                    <input name="nombre" placeholder="Nombre"
                        class="w-full border rounded-xl px-4 py-2">

                    <textarea name="descripcion" rows="3" cols="33" placeholder="Descripción"
                        class="w-full border rounded-xl px-4 py-2"></textarea>

                    <input name="precio" placeholder="Precio"
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
    @foreach($servicios as $servicio)
    <div x-show = "openEdit === {{ $servicio -> id }}" class="fixed inset-0 flex items-center justify-center z-50">

        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            @click="openEdit=null"></div>

        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg z-50">
            <h2 class="text-xl font-semibold mb-6">Editar Servicio</h2>

            <form action=" {{ route('servicios.update',$servicio->id) }}"

                method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">

                    <input name="nombre"value="{{ $servicio -> nombre }}"class="w-full border rounded-xl px-4 py-2">
                    <input name="descripcion"value="{{ $servicio -> descripcion }}"class="w-full border rounded-xl px-4 py-2">
                    <input name="precio"value="{{ $servicio -> precio }}"class="w-full border rounded-xl px-4 py-2">
    
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
    @foreach($servicios as $servicio)
    <div x-show="openDelete === {{ $servicio->id }}"class="fixed inset-0 flex items-center justify-center z-50">

        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            @click="openDelete=null"></div>

        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md z-50 text-center">
            <h2 class="text-lg font-semibold mb-6">
                ¿Eliminar este servicio?
            </h2>

            <form action="{{ route('servicios.destroy',$servicio->id) }}"
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
            <h2 class="text-xl font-semibold mb-6">Servicios Eliminados</h2>

            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-2">Nombre</th>
                        <th class="px-4 py-2">Descripcion</th>
                        <th class="px-4 py-2 text-center">Precio</th>
                        <th class="px-4 py-2 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eliminados as $servicio)
                    <tr class="border-b">
                        <td class="px-4 py-2">
                            {{ $servicio->nombre ?? '' }}
                        </td>
                        <td class="px-4 py-2">
                            {{ $servicio->descripcion ?? '' }}
                        </td>
                        <td class="px-4 py-2">
                            {{ $servicio->precio ?? '' }}
                        </td>
                        <td class="px-4 py-2 text-center space-x-3">

                            <form action="{{ route('servicios.restore',$servicio->id) }}"
                                method="POST" class="inline">
                                @csrf
                                <button
                                    class="bg-green-600 text-white px-3 py-1 rounded-lg">
                                    Restaurar
                                </button>
                            </form> 

                            <form action="{{ route('servicios.forceDelete',$servicio->id) }}"
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
                            No hay servicios eliminados
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
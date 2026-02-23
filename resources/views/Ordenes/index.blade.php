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
        <h1 class="text-2xl font-semibold">Lista de ordenes</h1>

        <div class="space-x-3">
            <button @click="openTrash = true"
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-xl">
                Ver Eliminados
            </button>

            <button @click="openCreate = true"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl">
                + Nueva orden
            </button>
        </div>
    </div>

    <!-- BUSCADOR -->
    <div class="mb-4">
        <input type="text"
            x-model="search"
            placeholder="Buscar orden..."
            class="w-full md:w-1/3 border rounded-xl px-4 py-2">
    </div>

    

    <!-- PAGINACIÓN -->
    <div class="mt-6">
    </div>


    <!-- ===================== MODAL CREAR ===================== -->
    <div x-show="openCreate"
        class="fixed inset-0 flex items-center justify-center z-50">

        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            @click="openCreate=false"></div>

        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-3xl z-50" x-data="ordenApp()">
            <h2 class="text-xl font-semibold mb-6">Nuevo Vehículo</h2>

            <form action="{{ route('ordenes.store') }}" method="POST">
                @csrf

                <div class="bg-white rounded-2xl shadow p-8 space-y-6">

                    <!-- FILA 1: CLIENTE Y VEHICULO -->
                    <div class="flex gap-3 w-full mb-5">

                        <!-- Seleccionar Cliente -->
                        <div class="flex-1 ">
                            <label class="block text-sm mb-1">Cliente</label>
                            <select name="cliente_id" x-model="cliente_id"
                                    @change="filtrarVehiculos"
                                    class="w-full border rounded-xl px-4 py-2">
                                <option value="">Seleccionar cliente...</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}">
                                        {{ $cliente->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Seleccionar Vehículo -->
                        <div class="flex-1">
                            <label class="block text-sm mb-1">Vehículo</label>
                            <select x-model="vehiculo_id" name="vehiculo_id" class="w-full border rounded-xl px-4 py-2">
                                <option value="">Seleccionar vehículo...</option>
                                <template x-for="v in vehiculosFiltrados" :key="v.id">
                                    <option :value="v.id" x-text="v.marca + ' - ' + v.placa"></option>
                                </template>
                            </select>
                        </div>

                    </div>

                    <!-- FILA 2: FECHA Y SERVICIO -->
                    <div class="flex gap-3 mb-5 w-full">

                        <!-- Fecha -->
                        <div class="flex-1">
                            <label class="block text-sm mb-1">Fecha</label>
                            <input type="date" name="fecha"
                                x-model="fecha"
                                class="w-full border rounded-xl px-4 py-2">
                        </div>

                        <!-- Seleccionar Servicio -->
                        <div class="flex-1">
                            <label class="block text-sm mb-1">Agregar Servicio</label>
                            <select @change="agregarServicio($event)"
                                    class="w-full border rounded-xl px-4 py-2">
                                <option value="">Seleccionar servicio...</option>
                                @foreach($servicios as $servicio)
                                    <option value="{{ $servicio->id }}"
                                            data-precio="{{ $servicio->precio }}"
                                            data-nombre="{{ $servicio->nombre }}">
                                        {{ $servicio->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <!-- SERVICIOS AGREGADOS -->
                    <div class="space-y-2">
                        <template x-for="(servicio, index) in serviciosSeleccionados" :key="index">
                            <div class="flex justify-between items-center bg-blue-50 px-4 py-3 rounded-xl">
                                <div>
                                    <p class="font-medium" x-text="servicio.nombre"></p>
                                    <p class="text-sm text-gray-600">
                                        $ <span x-text="servicio.precio"></span>
                                    </p>
                                    <input type="hidden" :name="'servicios['+index+'][id]'" :value="servicio.id">
                                    <input type="hidden" :name="'servicios['+index+'][precio]'" :value="servicio.precio">
                                </div>

                                <button @click.prevent="eliminarServicio(index)" type="button
                                        class="text-red-500 hover:text-red-700">
                                    🗑
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- TOTAL -->
                    <div class="flex justify-between items-center text-lg font-semibold mt-6">
                        <span>Total Automático:</span>
                        <span class="text-blue-600">
                            $ <span x-text="total"></span>
                        </span>
                        <input type="hidden" name="total" :value="total">
                    </div>
                </div>
                
                <div class="flex justify-end gap-4 mt-6">
                    <button type="button"
                        @click="openCreate=false"
                        class="px-4 py-2 border rounded-xl">
                        Cancelar
                    </button>
                    <!-- BOTON -->
                        <button :disabled="serviciosSeleccionados.length === 0"
                            class=" bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-semibold">
                            Crear Orden de Trabajo
                        </button>
                </div>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4 w-full">
        @foreach($ordenes as $orden)
        <div class="bg-gray-50 rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex justify-between mb-3">
                <div class="bg-blue-100 p-2 rounded-lg">
                    <span class="text-blue-600 text-sm">{{$orden->estado}}</span>
                </div>
                <div class="grid grid-cols-2 gap-4 ">
                    <button class="text-yellow-500 hover:text-yellow-700 text-sm">
                        ✏️
                    </button>
                    <button class="text-red-600 hover:text-red-800 text-sm">
                        🗑️
                    </button>
                </div>
            </div>
            <h3 class="w-fit text-lg font-semibold ">{{ $orden->cliente->nombre }}</h3>            
            <p class="text-xs text-gray-500 mb-2">{{ $orden->vehiculo->marca }} - {{ $orden->vehiculo->placa }}</p>
            <span class="text-blue-600 text-2xl font-bold">${{ number_format($orden->total, 2) }}</span>
        </div>
        @endforeach
    </div>
    


    <!-- ===================== MODAL EDITAR ===================== -->

    <!-- <div 
        class="fixed inset-0 flex items-center justify-center z-50">

        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            @click="openEdit=null"></div>

        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg z-50">
            <h2 class="text-xl font-semibold mb-6">Editar Vehículo</h2>

            <form "
                method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">

                    <select name="cliente_id"
                        class="w-full border rounded-xl px-4 py-2">
                    </select>

                    <input name="marca"
                        class="w-full border rounded-xl px-4 py-2">

                    <input name="modelo"
                        class="w-full border rounded-xl px-4 py-2">

                    <input name="anio"
                        class="w-full border rounded-xl px-4 py-2">

                    <input name="placa"
                        class="w-full border rounded-xl px-4 py-2">

                    <input name="color"
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
    </div> -->


    <!-- ===================== MODAL ELIMINAR ===================== -->

    <!-- <div 
        class="fixed inset-0 flex items-center justify-center z-50">

        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            @click="openDelete=null"></div>

        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md z-50 text-center">
            <h2 class="text-lg font-semibold mb-6">
                ¿Eliminar esta orden?
            </h2>

            <form 
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
    </div> -->


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
                    <tr class="border-b">
                        <td class="px-4 py-2">
                        </td>
                        <td class="px-4 py-2">
                        </td>
                        <td class="px-4 py-2 text-center space-x-3">

                            <!-- <form 
                                method="POST" class="inline">
                                @csrf
                                <button
                                    class="bg-green-600 text-white px-3 py-1 rounded-lg">
                                    Restaurar
                                </button>
                            </form> -->

                            <!-- <form 
                                method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button
                                    class="bg-red-600 text-white px-3 py-1 rounded-lg">
                                    Eliminar Definitivo
                                </button>
                            </form> -->

                        </td>
                    </tr>
                    
                    <tr>
                        <td colspan="3"
                            class="text-center py-6 text-gray-400">
                            No hay ordenes eliminadas
                        </td>
                    </tr>
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
    @section('scripts')
    <script>
    function ordenApp() {
        return {
            cliente_id: '',
            vehiculo_id: '',
            fecha: new Date().toLocaleDateString('sv-SE'),
            serviciosSeleccionados: [],
            vehiculos: @json($vehiculos),
            vehiculosFiltrados: [],
            total: 0,

            filtrarVehiculos() {
                this.vehiculosFiltrados = this.vehiculos
                    .filter(v => v.cliente_id == this.cliente_id);
            },

            agregarServicio(event) {
                let option = event.target.selectedOptions[0];

                if (!option.value) return;

                let id = option.value;

                if (this.serviciosSeleccionados.some(s => s.id == id))
                    return;

                this.serviciosSeleccionados.push({
                    id: id,
                    nombre: option.dataset.nombre,
                    precio: parseFloat(option.dataset.precio)
                });

                this.calcularTotal();
            },

            eliminarServicio(index) {
                this.serviciosSeleccionados.splice(index,1);
                this.calcularTotal();
            },

            calcularTotal() {
                this.total = this.serviciosSeleccionados
                    .reduce((sum,s) => sum + s.precio,0);
            }
        }
    }
    </script>
    @endsection
@endsection
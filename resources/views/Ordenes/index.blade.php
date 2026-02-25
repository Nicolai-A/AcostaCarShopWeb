@extends('layouts.app')

@section('content')

<div x-data="{ 
    ...ordenApp(), 
    openCreate: {{ $errors->any() ? 'true' : 'false' }},
    openEdit: null,
    openDelete: null,
    openTrash: false,
    search: '',
    filterDate: '',
}" x-cloak>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4">
            <strong>Error:</strong> {{ session('error') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Gestión de Órdenes</h1>
        <div class="space-x-3">
            <button @click="openTrash = true" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-xl transition-colors">
                Ver Eliminados
            </button>
            <button @click="openCreate = true" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl transition-colors">
                + Nueva orden
            </button>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-4 mb-6">
        <div class="flex-1">
            <label class="block text-xs text-gray-500 mb-1 ml-1">Buscar por cliente o placa</label>
            <input type="text" x-model="search" placeholder="Ej: Juan Pérez o ABC-123..." 
                class="w-full border rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div class="w-full md:w-48">
            <label class="block text-xs text-gray-500 mb-1 ml-1">Filtrar por fecha</label>
            <input type="date" x-model="filterDate" 
                class="w-full border rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div class="flex items-end">
            <button @click="search = ''; filterDate = ''" class="text-sm text-blue-600 hover:underline mb-2">
                Limpiar filtros
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 w-full">
        @foreach($ordenes as $orden)
            <div x-show="cumpleFiltros('{{ strtolower($orden->cliente?->nombre ?? 'Sin Cliente') }}', '{{ strtolower($orden->vehiculo?->placa ?? 'Sin Placa') }}', '{{ \Carbon\Carbon::parse($orden->fecha)->format('Y-m-d') }}')"
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                
                <div class="flex justify-between mb-3">
                    <div class="bg-blue-100 px-3 py-1 rounded-lg">
                        <span class="text-blue-600 text-xs font-bold uppercase">{{$orden->estado}}</span>
                    </div>
                    <div class="flex gap-2">
                        
                        <button @click="prepararEdicion({{ $orden->toJson() }})" class="text-yellow-500 hover:bg-yellow-50 p-1 rounded">✏️</button>
                        <button @click="openDelete = {{ $orden->id }}" class="text-red-600 hover:bg-red-50 p-1 rounded">🗑️</button>
                    </div>
                </div>
                

                <h3 class="text-lg font-bold text-gray-800">{{ $orden->cliente->nombre }}</h3>            
                <p class="text-xs text-gray-500 mb-1 font-medium">{{ $orden->vehiculo->marca }} - {{ $orden->vehiculo->placa }}</p>
                <p class="text-xs text-gray-400 mb-4">📅 {{ \Carbon\Carbon::parse($orden->fecha)->format('d/m/Y') }}</p>

                <div class="bg-gray-50 rounded-lg p-3 mb-4 border border-gray-100">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Gasto en Repuestos:</span>
                        <span class="text-sm font-bold text-red-500">-${{ number_format($orden->costo_insumos, 2) }}</span>
                    </div>
                    
                    @if($orden->notas_insumos)
                        <p class="text-[11px] text-gray-600 italic line-clamp-2">
                            <span class="font-bold text-gray-400">📝</span> {{ $orden->notas_insumos }}
                        </p>
                    @else
                        <p class="text-[11px] text-gray-300 italic">Sin notas de repuestos</p>
                    @endif
                </div>

               <div class="border-t border-gray-100 pt-4">
    <div class="flex justify-between items-center mb-4">
        <div>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Cobro Total:</p>
            <span class="text-2xl font-black text-blue-600">${{ number_format($orden->total, 2) }}</span>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-2">
        <a href="{{ route('ordenes.pdf', $orden->id) }}" 
           target="_blank"
           class="flex items-center justify-center gap-1 bg-gray-800 hover:bg-black text-white text-[10px] font-bold py-2 rounded-lg transition-colors uppercase">
           <span>📄</span> PDF
        </a>

        <form action="{{ route('ordenes.enviarEmail', $orden->id) }}" method="POST">
            @csrf
            <button type="submit" 
                    onclick="this.innerHTML='Enviando...'; this.classList.add('opacity-50')"
                    class="w-full flex items-center justify-center gap-1 border-2 border-blue-500 text-blue-500 hover:bg-blue-50 font-bold text-[10px] py-[6px] rounded-lg uppercase transition-colors">
                <span>✉️</span> Email
            </button>
        </form>
    </div>
</div>
            </div>
        @endforeach
    </div>

    <div x-show="openCreate" class="fixed inset-0 flex items-center justify-center z-50">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="openCreate=false"></div>
        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-3xl z-50 overflow-y-auto max-h-[90vh]">
            <h2 class="text-xl font-semibold mb-6">Nueva Orden de Trabajo</h2>
            <form action="{{ route('ordenes.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div class="flex gap-3">
                        <div class="flex-1">
                            <label class="block text-sm mb-1">Cliente</label>
                            <select name="cliente_id" x-model="cliente_id" @change="filtrarVehiculos" class="w-full border rounded-xl px-4 py-2">
                                <option value="">Seleccionar cliente...</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
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
                    <div class="flex gap-3">
                        <div class="flex-1">
                            <label class="block text-sm mb-1">Fecha de la Orden</label>
                            <input type="date" name="fecha" x-model="fecha" class="w-full border rounded-xl px-4 py-2">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm mb-1">Añadir Servicio</label>
                            <select @change="agregarServicio($event)" class="w-full border rounded-xl px-4 py-2">
                                <option value="">Seleccionar servicio...</option>
                                @foreach($servicios as $servicio)
                                    <option value="{{ $servicio->id }}" data-precio="{{ $servicio->precio }}" data-nombre="{{ $servicio->nombre }}">
                                        {{ $servicio->nombre }} (${{ $servicio->precio }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="space-y-2 border-t pt-4">
                        <template x-for="(servicio, index) in serviciosSeleccionados" :key="index">
                            <div class="flex justify-between items-center bg-blue-50 px-4 py-2 rounded-xl">
                                <span x-text="servicio.nombre" class="text-sm"></span>
                                <div class="flex items-center gap-4">
                                    <span class="text-sm font-bold">$<span x-text="servicio.precio"></span></span>
                                    <button @click.prevent="eliminarServicio(index)" class="text-red-500">🗑</button>
                                </div>
                                <input type="hidden" :name="'servicios['+index+'][id]'" :value="servicio.id">
                                <input type="hidden" :name="'servicios['+index+'][precio]'" :value="servicio.precio">
                            </div>
                        </template>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t pt-4">
                        <div>
                            <label class="block text-sm font-medium text-red-600 mb-1">Gasto en Repuestos ($)</label>
                            <input type="number" name="costo_insumos" x-model.number="costo_insumos" step="0.01"
                                class="w-full border rounded-xl px-4 py-2 focus:ring-2 focus:ring-red-500 outline-none" 
                                placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Detalle de Repuestos</label>
                            <textarea name="notas_insumos" x-model="notas_insumos" rows="1"
                                class="w-full border rounded-xl px-4 py-2 outline-none focus:ring-2 focus:ring-blue-500" 
                                placeholder="Ej: Filtro de aceite, pastillas..."></textarea>
                        </div>
                    </div>
                    <div class="text-right font-bold text-2xl text-blue-600 pt-4">
                        Total: $<span x-text="total"></span>
                        <input type="hidden" name="total" :value="total">
                    </div>
                </div>
                <div class="flex justify-end gap-4 mt-8">
                    <button type="button" @click="openCreate=false" class="px-4 py-2 border rounded-xl">Cancelar</button>
                    <button type="submit" 
                            :disabled="total <= 0"
                            :class="total <= 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                            class="text-white px-8 py-2 rounded-xl font-bold transition-colors">
                        Crear Orden
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openEdit" class="fixed inset-0 flex items-center justify-center z-50">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="openEdit = null"></div>
        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-3xl z-50 overflow-y-auto max-h-[90vh]">
            <h2 class="text-xl font-semibold mb-6">Editar Orden de Trabajo</h2>
            <form :action="'{{ route('ordenes.index') }}/' + openEdit" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <div class="flex gap-3">
                        <div class="flex-1">
                            <label class="block text-sm mb-1">Cliente (No editable)</label>
                            <select x-model="cliente_id" class="w-full border rounded-xl px-4 py-2 bg-gray-100" disabled>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="cliente_id" :value="cliente_id">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm mb-1">Vehículo</label>
                            <select name="vehiculo_id" x-model="vehiculo_id" class="w-full border rounded-xl px-4 py-2">
                                <template x-for="v in vehiculosFiltrados" :key="v.id">
                                    <option :value="v.id" x-text="v.marca + ' - ' + v.placa"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="flex-1">
                            <label class="block text-sm mb-1">Fecha</label>
                            <input type="date" name="fecha" x-model="fecha" class="w-full border rounded-xl px-4 py-2">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm mb-1">Añadir más Servicios</label>
                            <select @change="agregarServicio($event)" class="w-full border rounded-xl px-4 py-2">
                                <option value="">Seleccionar servicio...</option>
                                @foreach($servicios as $servicio)
                                    <option value="{{ $servicio->id }}" data-precio="{{ $servicio->precio }}" data-nombre="{{ $servicio->nombre }}">
                                        {{ $servicio->nombre }} (${{ $servicio->precio }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="space-y-2 border-t pt-4">
                        <template x-for="(servicio, index) in serviciosSeleccionados" :key="index">
                            <div class="flex justify-between items-center bg-yellow-50 px-4 py-2 rounded-xl">
                                <span x-text="servicio.nombre" class="text-sm"></span>
                                <div class="flex items-center gap-4">
                                    <span class="text-sm font-bold">$<span x-text="servicio.precio"></span></span>
                                    <button @click.prevent="eliminarServicio(index)" class="text-red-500">🗑</button>
                                </div>
                                <input type="hidden" :name="'servicios['+index+'][id]'" :value="servicio.id">
                                <input type="hidden" :name="'servicios['+index+'][precio]'" :value="servicio.precio">
                            </div>
                        </template>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t pt-4">
                        <div>
                            <label class="block text-sm font-medium text-red-600 mb-1">Gasto en Repuestos ($)</label>
                            <input type="number" name="costo_insumos" x-model.number="costo_insumos" step="0.01"
                                class="w-full border rounded-xl px-4 py-2 focus:ring-2 focus:ring-red-500 outline-none" 
                                placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Detalle de Repuestos</label>
                            <textarea name="notas_insumos" x-model="notas_insumos" rows="1"
                                class="w-full border rounded-xl px-4 py-2 outline-none focus:ring-2 focus:ring-blue-500" 
                                placeholder="Ej: Filtro de aceite, pastillas..."></textarea>
                        </div>
                    </div>
                    <div class="text-right font-bold text-2xl text-blue-600 pt-4">
                        Total Actualizado: $<span x-text="total"></span>
                        <input type="hidden" name="total" :value="total">
                    </div>
                </div>
                <div class="flex justify-end gap-4 mt-8">
                    <button type="button" @click="openEdit = null" class="px-4 py-2 border rounded-xl">Cancelar</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2 rounded-xl font-bold transition-colors">Actualizar Orden</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openDelete" class="fixed inset-0 flex items-center justify-center z-50" x-cloak>
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="openDelete = null"></div>
        <div class="bg-white rounded-2xl p-8 w-full max-w-md z-50 text-center">
            <div class="text-red-500 mb-4 text-5xl">⚠️</div>
            <h2 class="text-lg font-semibold mb-2">¿Enviar a la papelera?</h2>
            <p class="text-gray-500 text-sm mb-6">La orden podrá ser restaurada desde la sección de eliminados.</p>
            <form :action="'{{ route('ordenes.index') }}/' + openDelete" method="POST">
                    @csrf
                    @method('DELETE')
                <div class="flex justify-center gap-4">
                    <button type="button" @click="openDelete = null" class="flex-1 px-4 py-2 border rounded-xl">Cancelar</button>
                    <button type="submit" class="flex-1 bg-red-600 text-white px-6 py-2 rounded-xl font-bold">Eliminar</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openTrash" class="fixed inset-0 flex items-center justify-center z-50" x-cloak>
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="openTrash=false"></div>
        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-4xl z-50">
            <h2 class="text-xl font-semibold mb-6">Órdenes Eliminadas</h2>
            <div class="overflow-y-auto max-h-[60vh]">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-2 text-left">Cliente</th>
                            <th class="px-4 py-2 text-left">Vehículo</th>
                            <th class="px-4 py-2 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ordenesEliminadas as $orden)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $orden->cliente->nombre ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $orden->vehiculo->placa ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-center space-x-2">
                                <form action="{{ route('ordenes.restore', $orden->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="bg-green-600 text-white px-3 py-1 rounded-lg text-xs font-bold">Restaurar</button>
                                </form>
                                <form action="{{ route('ordenes.forceDelete', $orden->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-600 text-white px-3 py-1 rounded-lg text-xs font-bold">Eliminar Definitivo</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-6 text-gray-400">No hay órdenes eliminadas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mt-6">
                <button @click="openTrash=false" class="px-4 py-2 border rounded-xl">Cerrar</button>
            </div>
        </div>
    </div>

    <div class="bg-white border border-blue-100 rounded-2xl p-6 mb-6 shadow-sm">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="border-r border-gray-100 pr-6">
            <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Ingresos Totales (Ventas):</p>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-blue-600">$<span x-text="formatearMoneda(totalVentasBrutas())"></span></span>
            </div>
            <p class="text-xs text-gray-400 mt-1">Dinero total recibido de clientes</p>
        </div>

        <div class="pl-0 md:pl-6 text-right md:text-left">
            <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Utilidad Real (Ganancia):</p>
            <div class="flex items-baseline gap-2 md:justify-start justify-end">
                <span class="text-3xl font-black text-green-600">$<span x-text="formatearMoneda(gananciasTotales())"></span></span>
            </div>
            <p class="text-xs text-green-500 mt-1 font-medium">Lo que queda tras restar repuestos</p>
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
        costo_insumos: 0,
        notas_insumos: '',


        gananciasTotales() {
            let sumaNeto = 0;
            const todasLasOrdenes = @json($ordenes);
            
            todasLasOrdenes.forEach(orden => {
                const nombreC = (orden.cliente?.nombre || '').toLowerCase();
                const placaV = (orden.vehiculo?.placa || '').toLowerCase();
                let fechaO = orden.fecha;
                if (fechaO && fechaO.includes('T')) fechaO = fechaO.split('T')[0];

                if (this.cumpleFiltros(nombreC, placaV, fechaO)) {
                    // CÁLCULO: Ingreso Total - Costo de Insumos
                    let ingreso = parseFloat(orden.total || 0);
                    let gasto = parseFloat(orden.costo_insumos || 0);
                    sumaNeto += (ingreso - gasto);
                }
            });
            return sumaNeto;
        },
        formatearMoneda(valor) {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(valor);
        },
        cumpleFiltros(cliente, placa, fechaOrden) {
            const textoBusqueda = this.search.toLowerCase();
            const coincideTexto = cliente.includes(textoBusqueda) || placa.includes(textoBusqueda);
            
            // Limpiamos la fecha de la orden por si acaso
            const fechaLimpia = (fechaOrden || '').split(' ')[0].split('T')[0];
            
            const coincideFecha = this.filterDate === '' || fechaLimpia === this.filterDate;
            
            return coincideTexto && coincideFecha;
        },

        filtrarVehiculos() {
            this.vehiculosFiltrados = this.vehiculos.filter(v => v.cliente_id == this.cliente_id);
        },

        prepararEdicion(orden) {
            this.cliente_id = orden.cliente_id;
            this.filtrarVehiculos();
            this.$nextTick(() => { this.vehiculo_id = orden.vehiculo_id; });
            this.fecha = orden.fecha;
            
            // CARGAR ESTOS CAMPOS NUEVOS:
            this.costo_insumos = orden.costo_insumos || 0;
            this.notas_insumos = orden.notas_insumos || '';
            
            this.serviciosSeleccionados = orden.servicios.map(s => ({
                id: s.id,
                nombre: s.nombre,
                precio: parseFloat(s.pivot ? s.pivot.precio : s.precio)
            }));
            this.calcularTotal();
            this.openEdit = orden.id;
        },

        agregarServicio(event) {
            let option = event.target.selectedOptions[0];
            if (!option.value) return;
            if (this.serviciosSeleccionados.some(s => s.id == option.value)) return;
            this.serviciosSeleccionados.push({
                id: option.value,
                nombre: option.dataset.nombre,
                precio: parseFloat(option.dataset.precio)
            });
            this.calcularTotal();
            event.target.value = '';
        },

        eliminarServicio(index) {
            this.serviciosSeleccionados.splice(index, 1);
            this.calcularTotal();
        },

        calcularTotal() {
            this.total = this.serviciosSeleccionados.reduce((sum, s) => sum + s.precio, 0);
        },
        // Añade esto dentro de tu return en ordenApp()
        totalVentasBrutas() {
            let sumaBruta = 0;
            const todasLasOrdenes = @json($ordenes);
            todasLasOrdenes.forEach(orden => {
                const nombreC = (orden.cliente?.nombre || '').toLowerCase();
                const placaV = (orden.vehiculo?.placa || '').toLowerCase();
                let fechaO = orden.fecha;
                if (fechaO && fechaO.includes('T')) fechaO = fechaO.split('T')[0];

                if (this.cumpleFiltros(nombreC, placaV, fechaO)) {
                    sumaBruta += parseFloat(orden.total || 0);
                }
            });
            return sumaBruta;
        }
    }
}
</script>
@endsection
@endsection
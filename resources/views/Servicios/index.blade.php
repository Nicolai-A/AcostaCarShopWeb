@extends('layouts.app')

@section('content')

<div class="container">

    {{-- Título y botón --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestión de Servicios</h2>
        <a href="{{ route('servicios.create') }}" class="btn btn-primary">
            + Nuevo Servicio
        </a>
    </div>

    {{-- Mensaje éxito --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabla Servicios --}}
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-dark text-white">
            Servicios Activos
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Duración</th>
                        <th>Estado</th>
                        <th width="180">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($servicios as $servicio)
                        <tr>
                            <td>{{ $servicio->id }}</td>
                            <td>{{ $servicio->nombre }}</td>
                            <td>{{ $servicio->descripcion ?? '-' }}</td>
                            <td>
                                ${{ number_format($servicio->precio, 2) }}
                            </td>
                            <td>
                                {{ $servicio->duracion_estimada 
                                    ? $servicio->duracion_estimada . ' min'
                                    : '-' }}
                            </td>
                            <td>
                                @if($servicio->activo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('servicios.edit', $servicio) }}" 
                                   class="btn btn-sm btn-warning">
                                    Editar
                                </a>

                                <form action="{{ route('servicios.destroy', $servicio) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('¿Eliminar este servicio?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                No hay servicios registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Paginación --}}
            <div class="mt-3">
                {{ $servicios->links() }}
            </div>

        </div>
    </div>

    {{-- Servicios Eliminados --}}
    @if($eliminados->count())
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                Servicios Eliminados
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th width="200">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($eliminados as $servicio)
                            <tr>
                                <td>{{ $servicio->id }}</td>
                                <td>{{ $servicio->nombre }}</td>
                                <td>${{ number_format($servicio->precio, 2) }}</td>
                                <td>

                                    {{-- Restaurar --}}
                                    <form action="{{ route('servicios.restore', $servicio->id) }}" 
                                          method="POST" 
                                          class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-success">
                                            Restaurar
                                        </button>
                                    </form>

                                    {{-- Eliminar definitivo --}}
                                    <form action="{{ route('servicios.forceDelete', $servicio->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('¿Eliminar definitivamente este servicio?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-dark">
                                            Eliminar definitivo
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

@endsection

@extends('layouts.app')

@section('title', 'Matriz de Priorización')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 text-dark">
                <i class="fas fa-chart-bar"></i> Matriz de Priorización del Universo de Auditoría
            </h1>
            <p class="text-muted small">RF 3.1 - Priorización de procesos a auditar</p>
        </div>
        <div class="col-md-4 text-end">
            @can('create-matriz')
            <a href="{{ route('matriz-priorizacion.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Matriz
            </a>
            @endcan
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o código"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="vigencia" class="form-select">
                        <option value="">Todas las vigencias</option>
                        @foreach ($vigencias as $v)
                            <option value="{{ $v }}" {{ request('vigencia') == $v ? 'selected' : '' }}>
                                {{ $v }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="borrador" {{ request('estado') == 'borrador' ? 'selected' : '' }}>Borrador
                        </option>
                        <option value="validado" {{ request('estado') == 'validado' ? 'selected' : '' }}>Validado
                        </option>
                        <option value="aprobado" {{ request('estado') == 'aprobado' ? 'selected' : '' }}>Aprobado
                        </option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('matriz-priorizacion.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-redo"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Matrices -->
    @if ($matrices->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Vigencia</th>
                        <th>Procesos</th>
                        <th>A Auditar</th>
                        <th>Estado</th>
                        <th>Elaborado por</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($matrices as $matriz)
                        <tr>
                            <td>
                                <strong>{{ $matriz->codigo }}</strong>
                            </td>
                            <td>{{ $matriz->nombre }}</td>
                            <td>{{ $matriz->vigencia }}</td>
                            <td>
                                <span class="badge bg-info">{{ $matriz->detalles->count() }}</span>
                            </td>
                            <td>
                                <span class="badge bg-warning">{{ $matriz->procesosAuditar() }}</span>
                            </td>
                            <td>
                                @if ($matriz->estado == 'borrador')
                                    <span class="badge bg-secondary">Borrador</span>
                                @elseif ($matriz->estado == 'validado')
                                    <span class="badge bg-warning">Validado</span>
                                @else
                                    <span class="badge bg-success">Aprobado</span>
                                @endif
                            </td>
                            <td>{{ $matriz->elaboradoPor->name }}</td>
                            <td>
                                <a href="{{ route('matriz-priorizacion.show', $matriz) }}" 
                                    class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if ($matriz->puedeSerEditada())
                                    <a href="{{ route('matriz-priorizacion.edit', $matriz) }}" 
                                        class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                @if (auth()->user()->role == 'super_administrador' && $matriz->estado == 'borrador')
                                    <form action="{{ route('matriz-priorizacion.destroy', $matriz) }}" method="POST" 
                                        class="d-inline" onsubmit="return confirm('¿Eliminar matriz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="d-flex justify-content-center mt-4">
            {{ $matrices->links() }}
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No hay matrices de priorización registradas
        </div>
    @endif
</div>
@endsection

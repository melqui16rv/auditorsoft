@extends('layouts.app')

@section('title', 'Seguimientos - Tarea #' . $tarea->id)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('paa.index') }}">PAA</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('paa.show', $paa) }}">{{ $paa->codigo }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('paa.tareas.show', [$paa, $tarea]) }}">Tarea #{{ $tarea->id }}</a></li>
                    <li class="breadcrumb-item active">Seguimientos</li>
                </ol>
            </nav>

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3><i class="bi bi-list-check"></i> Seguimientos y Puntos de Control</h3>
                    <p class="text-muted mb-0">{{ $tarea->descripcion_tarea }}</p>
                </div>
                <div>
                    @if($tarea->estado != 'anulado')
                    <a href="{{ route('paa.seguimientos.create', [$paa, $tarea]) }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Nuevo Seguimiento
                    </a>
                    @endif
                    <a href="{{ route('paa.tareas.show', [$paa, $tarea]) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a la Tarea
                    </a>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h4>{{ $seguimientos->total() }}</h4>
                            <small class="text-muted">Total Seguimientos</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body text-center">
                            <h4>{{ $estadisticas['pendientes'] }}</h4>
                            <small>Pendientes</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h4>{{ $estadisticas['realizados'] }}</h4>
                            <small>Realizados</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h4>{{ $estadisticas['porcentaje'] }}%</h4>
                            <small>Cumplimiento</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-funnel"></i> Filtros</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('paa.seguimientos.index', [$paa, $tarea]) }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select name="estado" id="estado" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="realizado" {{ request('estado') == 'realizado' ? 'selected' : '' }}>Realizado</option>
                                    <option value="anulado" {{ request('estado') == 'anulado' ? 'selected' : '' }}>Anulado</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="evaluacion" class="form-label">Evaluación</label>
                                <select name="evaluacion" id="evaluacion" class="form-select">
                                    <option value="">Todas</option>
                                    <option value="pendiente" {{ request('evaluacion') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="bien" {{ request('evaluacion') == 'bien' ? 'selected' : '' }}>Bien</option>
                                    <option value="mal" {{ request('evaluacion') == 'mal' ? 'selected' : '' }}>Mal</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="ente_control_id" class="form-label">Ente de Control</label>
                                <select name="ente_control_id" id="ente_control_id" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($entesControl as $ente)
                                        <option value="{{ $ente->id }}" {{ request('ente_control_id') == $ente->id ? 'selected' : '' }}>
                                            {{ $ente->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Filtrar
                                </button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <a href="{{ route('paa.seguimientos.index', [$paa, $tarea]) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Listado de Seguimientos -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul"></i> 
                        Puntos de Control Registrados
                        <span class="badge bg-light text-dark float-end">{{ $seguimientos->total() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($seguimientos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="30%">Descripción</th>
                                    <th width="12%">Fecha</th>
                                    <th width="15%">Ente Control</th>
                                    <th width="10%">Estado</th>
                                    <th width="10%">Evaluación</th>
                                    <th width="8%">Evidencias</th>
                                    <th width="10%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($seguimientos as $seguimiento)
                                <tr>
                                    <td>{{ $seguimiento->id }}</td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 300px;" title="{{ $seguimiento->descripcion_punto_control }}">
                                            {{ $seguimiento->descripcion_punto_control }}
                                        </div>
                                        @if($seguimiento->observaciones)
                                        <small class="text-muted">
                                            <i class="bi bi-chat-left-text"></i> Con observaciones
                                        </small>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ \Carbon\Carbon::parse($seguimiento->fecha_seguimiento)->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $seguimiento->enteControl->nombre ?? 'N/A' }}</small>
                                    </td>
                                    <td>{!! $seguimiento->estado_badge !!}</td>
                                    <td>{!! $seguimiento->evaluacion_badge !!}</td>
                                    <td class="text-center">
                                        @if($seguimiento->evidencias->count() > 0)
                                        <span class="badge bg-success">
                                            <i class="bi bi-file-earmark-check"></i> {{ $seguimiento->evidencias->count() }}
                                        </span>
                                        @else
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-dash"></i> 0
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('paa.seguimientos.show', [$paa, $tarea, $seguimiento]) }}" 
                                               class="btn btn-info" 
                                               title="Ver">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($seguimiento->estado != 'realizado')
                                            <a href="{{ route('paa.seguimientos.edit', [$paa, $tarea, $seguimiento]) }}" 
                                               class="btn btn-warning" 
                                               title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @endif
                                            @if(auth()->user()->role == 'super_administrador')
                                            <form action="{{ route('paa.seguimientos.destroy', [$paa, $tarea, $seguimiento]) }}" 
                                                  method="POST" 
                                                  style="display: inline;"
                                                  onsubmit="return confirm('¿Eliminar este seguimiento?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($seguimientos->hasPages())
                    <div class="mt-3">
                        {{ $seguimientos->appends(request()->query())->links() }}
                    </div>
                    @endif
                    @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        No hay seguimientos registrados para esta tarea.
                        @if($tarea->estado != 'anulado')
                            <a href="{{ route('paa.seguimientos.create', [$paa, $tarea]) }}" class="alert-link">Crear el primer seguimiento</a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Tareas del PAA - ' . $paa->codigo)

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
                    <li class="breadcrumb-item active">Tareas</li>
                </ol>
            </nav>

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3>
                        <i class="bi bi-list-task"></i>
                        Tareas del PAA {{ $paa->vigencia }}
                    </h3>
                    <p class="text-muted mb-0">{{ $paa->codigo }} - {{ $paa->nombre_entidad }}</p>
                </div>
                <div>
                    @if($paa->estado == 'borrador' || $paa->estado == 'aprobado')
                    <a href="{{ route('paa.tareas.create', $paa) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Nueva Tarea
                    </a>
                    @endif
                    <a href="{{ route('paa.show', $paa) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al PAA
                    </a>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-funnel"></i> Filtros de Búsqueda</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('paa.tareas.index', $paa) }}" method="GET" id="formFiltros">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="rol_oci_id" class="form-label">Rol OCI</label>
                                <select name="rol_oci_id" id="rol_oci_id" class="form-select">
                                    <option value="">Todos los roles</option>
                                    @foreach($rolesOci as $rol)
                                        <option value="{{ $rol->id }}" {{ request('rol_oci_id') == $rol->id ? 'selected' : '' }}>
                                            {{ $rol->nombre_rol }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="estado" class="form-label">Estado</label>
                                <select name="estado" id="estado" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="en_proceso" {{ request('estado') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                                    <option value="realizado" {{ request('estado') == 'realizado' ? 'selected' : '' }}>Realizado</option>
                                    <option value="anulado" {{ request('estado') == 'anulado' ? 'selected' : '' }}>Anulado</option>
                                    <option value="vencido" {{ request('estado') == 'vencido' ? 'selected' : '' }}>Vencido</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="evaluacion" class="form-label">Evaluación</label>
                                <select name="evaluacion" id="evaluacion" class="form-select">
                                    <option value="">Todas</option>
                                    <option value="pendiente" {{ request('evaluacion') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="bien" {{ request('evaluacion') == 'bien' ? 'selected' : '' }}>Bien</option>
                                    <option value="mal" {{ request('evaluacion') == 'mal' ? 'selected' : '' }}>Mal</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="responsable_id" class="form-label">Responsable</label>
                                <select name="responsable_id" id="responsable_id" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($responsables as $responsable)
                                        <option value="{{ $responsable->id }}" {{ request('responsable_id') == $responsable->id ? 'selected' : '' }}>
                                            {{ $responsable->name }}
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
                                <a href="{{ route('paa.tareas.index', $paa) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Limpiar Filtros
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Estadísticas Rápidas -->
            <div class="row mb-3">
                <div class="col-md-2">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h4>{{ $tareas->total() }}</h4>
                            <small class="text-muted">Total Tareas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-secondary text-white">
                        <div class="card-body text-center">
                            <h4>{{ $estadisticas['pendientes'] ?? 0 }}</h4>
                            <small>Pendientes</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-warning text-dark">
                        <div class="card-body text-center">
                            <h4>{{ $estadisticas['en_proceso'] ?? 0 }}</h4>
                            <small>En Proceso</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h4>{{ $estadisticas['realizadas'] ?? 0 }}</h4>
                            <small>Realizadas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h4>{{ $estadisticas['anuladas'] ?? 0 }}</h4>
                            <small>Anuladas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h4>{{ $estadisticas['porcentaje'] ?? 0 }}%</h4>
                            <small>Cumplimiento</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tareas por Rol OCI (Accordion) -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-list-check"></i> 
                        Tareas Organizadas por Rol OCI
                        <span class="badge bg-light text-dark float-end">{{ $tareas->total() }} tareas</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($tareas->count() > 0)
                    <div class="accordion" id="accordionRoles">
                        @foreach($tareasPorRol as $rolId => $tareasRol)
                        @php
                            $rol = $rolesOci->firstWhere('id', $rolId);
                            $totalTareasRol = $tareasRol->count();
                            $realizadasRol = $tareasRol->where('estado', 'realizado')->count();
                            $porcentajeRol = $totalTareasRol > 0 ? round(($realizadasRol / $totalTareasRol) * 100) : 0;
                        @endphp
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $rolId }}">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse{{ $rolId }}">
                                    <strong>{{ $rol->nombre_rol }}</strong>
                                    <span class="ms-2 badge bg-primary">{{ $totalTareasRol }} tareas</span>
                                    <span class="ms-2">
                                        <div class="progress" style="width: 150px; height: 20px;">
                                            <div class="progress-bar bg-success" 
                                                 role="progressbar" 
                                                 style="width: {{ $porcentajeRol }}%">
                                                {{ $porcentajeRol }}%
                                            </div>
                                        </div>
                                    </span>
                                </button>
                            </h2>
                            <div id="collapse{{ $rolId }}" 
                                 class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                                 data-bs-parent="#accordionRoles">
                                <div class="accordion-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="30%">Descripción</th>
                                                    <th width="15%">Responsable</th>
                                                    <th width="10%">Inicio</th>
                                                    <th width="10%">Fin</th>
                                                    <th width="10%">Estado</th>
                                                    <th width="10%">Evaluación</th>
                                                    <th width="10%">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($tareasRol as $tarea)
                                                <tr>
                                                    <td>{{ $tarea->id }}</td>
                                                    <td>
                                                        <div class="text-truncate" style="max-width: 250px;" title="{{ $tarea->descripcion_tarea }}">
                                                            {{ $tarea->descripcion_tarea }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <small>
                                                            <i class="bi bi-person-circle"></i> {{ $tarea->responsable->name }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <small>{{ \Carbon\Carbon::parse($tarea->fecha_inicio_planeada)->format('d/m/Y') }}</small>
                                                    </td>
                                                    <td>
                                                        <small>{{ \Carbon\Carbon::parse($tarea->fecha_fin_planeada)->format('d/m/Y') }}</small>
                                                    </td>
                                                    <td>{!! $tarea->estado_badge !!}</td>
                                                    <td>{!! $tarea->evaluacion_badge !!}</td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <a href="{{ route('paa.tareas.show', [$paa, $tarea]) }}" 
                                                               class="btn btn-info btn-sm" 
                                                               title="Ver detalle">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                            @if(in_array($tarea->estado, ['pendiente', 'en_proceso']))
                                                            <a href="{{ route('paa.tareas.edit', [$paa, $tarea]) }}" 
                                                               class="btn btn-warning btn-sm" 
                                                               title="Editar">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            @endif
                                                            @if(auth()->user()->role == 'super_administrador')
                                                            <form action="{{ route('paa.tareas.destroy', [$paa, $tarea]) }}" 
                                                                  method="POST" 
                                                                  style="display: inline;"
                                                                  onsubmit="return confirm('¿Está seguro de eliminar esta tarea?')">
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
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        No se encontraron tareas con los filtros aplicados.
                        @if($paa->estado == 'borrador' || $paa->estado == 'aprobado')
                            <a href="{{ route('paa.tareas.create', $paa) }}" class="alert-link">Crear la primera tarea</a>
                        @endif
                    </div>
                    @endif

                    <!-- Paginación -->
                    @if($tareas->hasPages())
                    <div class="mt-3">
                        {{ $tareas->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit filtros al cambiar
    const filtros = document.querySelectorAll('#formFiltros select');
    filtros.forEach(filtro => {
        filtro.addEventListener('change', function() {
            // Opcional: auto-submit
            // document.getElementById('formFiltros').submit();
        });
    });
});
</script>
@endpush
@endsection

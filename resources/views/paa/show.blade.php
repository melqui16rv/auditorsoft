@extends('layouts.app')

@section('title', 'Detalle del PAA - ' . $paa->codigo)

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('page-title', 'Detalle del PAA')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('paa.index') }}">PAA</a></li>
                    <li class="breadcrumb-item active">{{ $paa->codigo }}</li>
                </ol>
            </nav>

            <!-- Card Principal -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-0">
                                <i class="bi bi-file-earmark-text"></i>
                                Plan Anual de Auditoría - {{ $paa->codigo }}
                            </h4>
                            <p class="mb-0 mt-1">
                                <small>{{ $paa->nombre_entidad }} | Vigencia {{ $paa->vigencia }}</small>
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            {!! $paa->estado_badge !!}
                            <br>
                            <small class="text-white-50">FR-GCE-001</small>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Mensajes de sesión -->
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <!-- Botones de Acción Rápida -->
                    <div class="mb-3">
                        <div class="btn-group" role="group">
                            @if($paa->puedeSerEditado())
                            <a href="{{ route('paa.edit', $paa) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            @endif

                            @if($paa->estado == 'borrador' && in_array(auth()->user()->role, ['jefe_auditor', 'super_administrador']))
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#aprobarModal">
                                <i class="bi bi-check-circle"></i> Aprobar
                            </button>
                            @endif

                            @if($paa->estado == 'en_ejecucion' && in_array(auth()->user()->role, ['jefe_auditor', 'super_administrador']))
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#finalizarModal">
                                <i class="bi bi-flag"></i> Finalizar
                            </button>
                            @endif

                            @if(in_array($paa->estado, ['borrador', 'en_ejecucion']) && in_array(auth()->user()->role, ['jefe_auditor', 'super_administrador']))
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#anularModal">
                                <i class="bi bi-x-circle"></i> Anular
                            </button>
                            @endif

                            <a href="{{ route('paa.pdf', $paa) }}" class="btn btn-dark btn-sm" target="_blank">
                                <i class="bi bi-file-pdf"></i> Exportar PDF
                            </a>
                        </div>
                    </div>

                    <!-- Tabs de Navegación -->
                    <ul class="nav nav-tabs" id="paaTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button">
                                <i class="bi bi-info-circle"></i> Información General
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tareas-tab" data-bs-toggle="tab" data-bs-target="#tareas" type="button">
                                <i class="bi bi-list-task"></i> Tareas por Rol OCI
                                <span class="badge bg-secondary">{{ $paa->tareas->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="estadisticas-tab" data-bs-toggle="tab" data-bs-target="#estadisticas" type="button">
                                <i class="bi bi-graph-up"></i> Estadísticas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="auditoria-tab" data-bs-toggle="tab" data-bs-target="#auditoria" type="button">
                                <i class="bi bi-clock-history"></i> Auditoría
                            </button>
                        </li>
                    </ul>

                    <!-- Contenido de Tabs -->
                    <div class="tab-content border border-top-0 p-3" id="paaTabContent">
                        <!-- Tab 1: Información General -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="mb-3">Datos del PAA</h5>
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width: 30%">Código de Registro:</th>
                                                <td><strong>{{ $paa->codigo }}</strong></td>
                                            </tr>
                                            <tr>
                                                <th>Vigencia:</th>
                                                <td><span class="badge bg-secondary">{{ $paa->vigencia }}</span></td>
                                            </tr>
                                            <tr>
                                                <th>Entidad:</th>
                                                <td>{{ $paa->nombre_entidad }}</td>
                                            </tr>
                                            <tr>
                                                <th>Municipio:</th>
                                                <td>
                                                    @if($paa->municipio)
                                                        {{ $paa->municipio->nombre }}, {{ $paa->municipio->departamento }}
                                                    @else
                                                        <em class="text-muted">No especificado</em>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Fecha de Elaboración:</th>
                                                <td>{{ $paa->fecha_elaboracion->format('d/m/Y') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Jefe OCI Responsable:</th>
                                                <td>
                                                    <i class="bi bi-person-badge"></i> {{ $paa->elaboradoPor->name }}
                                                    <br><small class="text-muted">{{ $paa->elaboradoPor->email }}</small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Estado:</th>
                                                <td>{!! $paa->estado_badge !!}</td>
                                            </tr>
                                            @if($paa->fecha_aprobacion)
                                            <tr>
                                                <th>Fecha de Aprobación:</th>
                                                <td>
                                                    {{ $paa->fecha_aprobacion->format('d/m/Y') }}
                                                    <br><small class="text-muted">Por: {{ $paa->aprobadoPor->name ?? 'N/A' }}</small>
                                                </td>
                                            </tr>
                                            @endif
                                            @if($paa->observaciones)
                                            <tr>
                                                <th>Observaciones:</th>
                                                <td>{{ $paa->observaciones }}</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-md-4">
                                    @if($paa->imagen_institucional)
                                    <h5 class="mb-3">Logo Institucional</h5>
                                    <div class="border rounded p-3 text-center bg-light">
                                        <img src="{{ asset('storage/' . $paa->imagen_institucional) }}" 
                                             alt="Logo" 
                                             class="img-fluid" 
                                             style="max-height: 200px;">
                                    </div>
                                    @endif

                                    <h5 class="mb-3 mt-4">Cumplimiento General</h5>
                                    <div class="text-center">
                                        <div class="display-4 mb-2">
                                            <strong class="text-{{ $porcentajeCumplimiento >= 80 ? 'success' : ($porcentajeCumplimiento >= 50 ? 'warning' : 'danger') }}">
                                                {{ number_format($porcentajeCumplimiento, 1) }}%
                                            </strong>
                                        </div>
                                        <div class="progress" style="height: 30px;">
                                            <div class="progress-bar bg-{{ $porcentajeCumplimiento >= 80 ? 'success' : ($porcentajeCumplimiento >= 50 ? 'warning' : 'danger') }}" 
                                                 style="width: {{ $porcentajeCumplimiento }}%">
                                                {{ number_format($porcentajeCumplimiento, 1) }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Tareas por Rol OCI -->
                        <div class="tab-pane fade" id="tareas" role="tabpanel">
                            <h5 class="mb-3">
                                <i class="bi bi-list-task"></i> Tareas Organizadas por Rol OCI
                                <a href="{{ route('paa.tareas.create', $paa) }}" class="btn btn-primary btn-sm float-end">
                                    <i class="bi bi-plus-circle"></i> Nueva Tarea
                                </a>
                            </h5>

                            @if($paa->tareas->isEmpty())
                            <div class="alert alert-warning text-center py-5">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <p class="mt-3">No hay tareas registradas para este PAA</p>
                                <a href="{{ route('paa.tareas.create', $paa) }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Crear Primera Tarea
                                </a>
                            </div>
                            @else
                            <!-- Acordeón por Rol OCI -->
                            <div class="accordion" id="accordionRoles">
                                @foreach($cumplimientoPorRol as $rolId => $datosRol)
                                @php
                                    $nombreRol = $datosRol['nombre'];
                                    $porcentaje = $datosRol['porcentaje'];
                                    $totalTareas = $datosRol['tareas_total'];
                                    $tareasRealizadas = $datosRol['tareas_realizadas'];
                                    $tareasDelRol = $paa->tareas->where('rol_oci_id', $rolId);
                                    $accordionId = 'rol_' . $rolId;
                                @endphp
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#{{ $accordionId }}">
                                            <strong>{{ $nombreRol }}</strong>
                                            <span class="ms-auto me-3">
                                                <span class="badge bg-secondary">{{ $totalTareas }} tareas</span>
                                                <span class="badge bg-{{ $porcentaje >= 80 ? 'success' : ($porcentaje >= 50 ? 'warning' : 'danger') }}">
                                                    {{ number_format($porcentaje, 1) }}%
                                                </span>
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="{{ $accordionId }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#accordionRoles">
                                        <div class="accordion-body">
                                            <table class="table table-sm table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Descripción</th>
                                                        <th>Responsable</th>
                                                        <th>Fechas</th>
                                                        <th>Estado</th>
                                                        <th>Evaluación</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($tareasDelRol as $tarea)
                                                    <tr>
                                                        <td>{{ Str::limit($tarea->descripcion_tarea, 50) }}</td>
                                                        <td>
                                                            <small>
                                                                <i class="bi bi-person"></i>
                                                                {{ $tarea->responsable->name ?? 'Sin asignar' }}
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <small>
                                                                {{ $tarea->fecha_inicio_planeada->format('d/m/Y') }}
                                                                <br>
                                                                {{ $tarea->fecha_fin_planeada->format('d/m/Y') }}
                                                            </small>
                                                        </td>
                                                        <td>{!! $tarea->estado_badge !!}</td>
                                                        <td>{!! $tarea->evaluacion_badge !!}</td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <a href="{{ route('paa.tareas.show', [$paa, $tarea]) }}" class="btn btn-info btn-sm">
                                                                    <i class="bi bi-eye"></i>
                                                                </a>
                                                                <a href="{{ route('paa.tareas.edit', [$paa, $tarea]) }}" class="btn btn-warning btn-sm">
                                                                    <i class="bi bi-pencil"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        <!-- Tab 3: Estadísticas -->
                        <div class="tab-pane fade" id="estadisticas" role="tabpanel">
                            <h5 class="mb-3"><i class="bi bi-graph-up"></i> Estadísticas del PAA</h5>

                            <div class="row">
                                <!-- KPIs -->
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white mb-3">
                                        <div class="card-body text-center">
                                            <h2 class="mb-0">{{ $estadisticas['total_tareas'] }}</h2>
                                            <p class="mb-0"><small>Total de Tareas</small></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white mb-3">
                                        <div class="card-body text-center">
                                            <h2 class="mb-0">{{ $estadisticas['tareas_realizadas'] }}</h2>
                                            <p class="mb-0"><small>Realizadas</small></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white mb-3">
                                        <div class="card-body text-center">
                                            <h2 class="mb-0">{{ $estadisticas['tareas_en_proceso'] }}</h2>
                                            <p class="mb-0"><small>En Proceso</small></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-secondary text-white mb-3">
                                        <div class="card-body text-center">
                                            <h2 class="mb-0">{{ $estadisticas['tareas_pendientes'] }}</h2>
                                            <p class="mb-0"><small>Pendientes</small></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Gráfico de Cumplimiento por Rol -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Cumplimiento por Rol OCI</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="chartCumplimientoRoles" height="80"></canvas>
                                </div>
                            </div>

                            <!-- Gráfico de Estados -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Distribución de Tareas por Estado</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="chartEstadosTareas" height="80"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 4: Auditoría -->
                        <div class="tab-pane fade" id="auditoria" role="tabpanel">
                            <h5 class="mb-3"><i class="bi bi-clock-history"></i> Historial de Auditoría</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <strong>Información de Creación</strong>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Creado por:</strong> {{ $paa->createdBy->name ?? 'N/A' }}</p>
                                            <p><strong>Fecha:</strong> {{ $paa->created_at->format('d/m/Y H:i:s') }}</p>
                                            <p><strong>Hace:</strong> {{ $paa->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>

                                @if($paa->updated_by)
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <strong>Última Modificación</strong>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Modificado por:</strong> {{ $paa->updatedBy->name ?? 'N/A' }}</p>
                                            <p><strong>Fecha:</strong> {{ $paa->updated_at->format('d/m/Y H:i:s') }}</p>
                                            <p><strong>Hace:</strong> {{ $paa->updated_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Metadatos FR-GCE-001 -->
                            <div class="card mt-3">
                                <div class="card-header bg-secondary text-white">
                                    <strong>Metadatos FR-GCE-001 (Decreto 648/2017)</strong>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p><strong>Versión:</strong> {{ $paa->version_formato }}</p>
                                            <p><strong>Protección:</strong> {{ $paa->proteccion }}</p>
                                            <p><strong>Medio:</strong> {{ $paa->medio_almacenamiento }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Ubicación:</strong> {{ $paa->ubicacion_logica }}</p>
                                            <p><strong>Recuperación:</strong> {{ $paa->metodo_recuperacion }}</p>
                                            <p><strong>Responsable:</strong> {{ $paa->responsable_archivo }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Permanencia:</strong> {{ $paa->permanencia }}</p>
                                            <p><strong>Disposición:</strong> {{ $paa->disposicion_final }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Aprobar -->
<div class="modal fade" id="aprobarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('paa.aprobar', $paa) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Aprobar PAA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de aprobar este Plan Anual de Auditoría?</p>
                    <p>El PAA pasará al estado <strong>"En Ejecución"</strong> y se registrará la fecha de aprobación.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Aprobar PAA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Finalizar -->
<div class="modal fade" id="finalizarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('paa.finalizar', $paa) }}" method="POST">
                @csrf
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Finalizar PAA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de finalizar este Plan Anual de Auditoría?</p>
                    <p>El PAA pasará al estado <strong>"Finalizado"</strong> y no podrá modificarse posteriormente.</p>
                    <p><strong>Cumplimiento actual:</strong> {{ number_format($porcentajeCumplimiento, 1) }}%</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">
                        <i class="bi bi-flag"></i> Finalizar PAA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Anular -->
<div class="modal fade" id="anularModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('paa.anular', $paa) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Anular PAA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de anular este Plan Anual de Auditoría?</p>
                    <div class="mb-3">
                        <label for="motivo" class="form-label">
                            Motivo de Anulación <span class="text-danger">*</span>
                        </label>
                        <textarea name="motivo" 
                                  id="motivo" 
                                  class="form-control" 
                                  rows="3" 
                                  required 
                                  minlength="10"
                                  placeholder="Ingrese el motivo de la anulación (mínimo 10 caracteres)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> Anular PAA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Cumplimiento por Rol
    const ctxRoles = document.getElementById('chartCumplimientoRoles');
    if (ctxRoles) {
        new Chart(ctxRoles, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($cumplimientoPorRol, 'nombre')) !!},
                datasets: [{
                    label: 'Cumplimiento (%)',
                    data: {!! json_encode(array_column($cumplimientoPorRol, 'porcentaje')) !!},
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 159, 64, 0.6)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    }

    // Gráfico de Estados de Tareas
    const ctxEstados = document.getElementById('chartEstadosTareas');
    if (ctxEstados) {
        new Chart(ctxEstados, {
            type: 'doughnut',
            data: {
                labels: ['Realizadas', 'En Proceso', 'Pendientes', 'Vencidas', 'Anuladas'],
                datasets: [{
                    data: [
                        {{ $estadisticas['tareas_realizadas'] }},
                        {{ $estadisticas['tareas_en_proceso'] }},
                        {{ $estadisticas['tareas_pendientes'] }},
                        {{ $estadisticas['tareas_vencidas'] }},
                        {{ $estadisticas['tareas_anuladas'] }}
                    ],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(108, 117, 125, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(23, 162, 184, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection

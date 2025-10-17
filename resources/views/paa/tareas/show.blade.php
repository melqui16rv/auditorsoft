@extends('layouts.app')

@section('title', 'Detalle de Tarea - ' . $tarea->nombre_rol_oci)

@section('sidebar')
    @include('partials.sidebar')
@endsection

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
                    <li class="breadcrumb-item active">Tarea #{{ $tarea->id }}</li>
                </ol>
            </nav>

            <!-- Header con Acciones -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>
                    <i class="bi bi-list-task"></i>
                    Detalle de la Tarea
                </h3>
                <div>
                    @if($tarea->estado == 'pendiente')
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalIniciar">
                            <i class="bi bi-play-circle"></i> Iniciar Tarea
                        </button>
                    @endif

                    @if($tarea->estado == 'en_proceso')
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCompletar">
                            <i class="bi bi-check-circle"></i> Completar Tarea
                        </button>
                    @endif

                    @if($tarea->estado != 'realizado' && $tarea->estado != 'anulado')
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalAnular">
                            <i class="bi bi-x-circle"></i> Anular Tarea
                        </button>
                    @endif

                    @if(in_array($tarea->estado, ['pendiente', 'en_proceso']))
                        <a href="{{ route('paa.tareas.edit', [$paa, $tarea]) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                    @endif

                    <a href="{{ route('paa.show', $paa) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al PAA
                    </a>
                </div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs" id="tareaTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button">
                        <i class="bi bi-info-circle"></i> Información General
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="seguimientos-tab" data-bs-toggle="tab" data-bs-target="#seguimientos" type="button">
                        <i class="bi bi-list-check"></i> Seguimientos
                        <span class="badge bg-primary">{{ $totalSeguimientos }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="evidencias-tab" data-bs-toggle="tab" data-bs-target="#evidencias" type="button">
                        <i class="bi bi-file-earmark-text"></i> Evidencias
                        <span class="badge bg-info">{{ $totalEvidencias }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="auditoria-tab" data-bs-toggle="tab" data-bs-target="#auditoria" type="button">
                        <i class="bi bi-clock-history"></i> Auditoría
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="tareaTabContent">
                <!-- TAB 1: Información General -->
                <div class="tab-pane fade show active" id="info" role="tabpanel">
                    <div class="card shadow-sm mt-3">
                        <div class="card-body">
                            <!-- Estado y Progreso -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <h6 class="text-muted">Estado</h6>
                                    {!! $tarea->estado_badge !!}
                                </div>
                                <div class="col-md-3">
                                    <h6 class="text-muted">Evaluación</h6>
                                    {!! $tarea->evaluacion_badge !!}
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Progreso</h6>
                                    <div class="progress h-6">
                                        <div class="progress-bar {{ $tarea->porcentaje_avance == 100 ? 'bg-success' : 'bg-info' }}"
                                             role="progressbar"
                                             data-width="{{ $tarea->porcentaje_avance }}">
                                            {{ $tarea->porcentaje_avance }}%
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Información de la Tarea -->
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Rol OCI:</th>
                                            <td>
                                                <span class="badge bg-primary">{{ $tarea->nombre_rol_oci }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Descripción:</th>
                                            <td>{{ $tarea->descripcion }}</td>
                                        </tr>
                                        <tr>
                                            <th>Responsable:</th>
                                            <td>
                                                <i class="bi bi-person-circle"></i> {{ $tarea->responsable->name ?? 'Sin asignar' }}
                                                <br><small class="text-muted">{{ $tarea->responsable ? ucfirst($tarea->responsable->role) : '' }}</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Observaciones:</th>
                                            <td>{{ $tarea->observaciones ?: 'Sin observaciones' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="50%">Inicio Planeado:</th>
                                            <td>{{ optional($tarea->fecha_inicio)->format('d/m/Y') ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Fin Planeado:</th>
                                            <td>{{ optional($tarea->fecha_fin)->format('d/m/Y') ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Duración Planeada:</th>
                                            <td>
                                                @if($tarea->fecha_inicio && $tarea->fecha_fin)
                                                    {{ abs(\Carbon\Carbon::parse($tarea->fecha_fin)->diffInDays(\Carbon\Carbon::parse($tarea->fecha_inicio))) }} días
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Información del PAA -->
                            <hr>
                            <h6 class="text-muted"><i class="bi bi-folder"></i> PAA Asociado</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Código:</strong> {{ $paa->codigo }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Vigencia:</strong> <span class="badge bg-secondary">{{ $paa->vigencia }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Estado PAA:</strong> {!! $paa->estado_badge !!}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Jefe OCI:</strong> {{ $paa->elaboradoPor->name }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: Seguimientos -->
                <div class="tab-pane fade" id="seguimientos" role="tabpanel">
                    <div class="card shadow-sm mt-3">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Puntos de Control y Seguimientos</h5>
                                @if($tarea->estado != 'realizada' && $tarea->estado != 'anulada')
                                <a href="#" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus-circle"></i> Nuevo Seguimiento
                                </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @if($seguimientos->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Descripción</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                            <th>Evaluación</th>
                                            <th>Ente de Control</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($seguimientos as $seguimiento)
                                        <tr>
                                            <td>{{ $seguimiento->id }}</td>
                                            <td>{{ Str::limit($seguimiento->observaciones, 50) }}</td>
                                            <td>{{ $seguimiento->fecha_realizacion ? \Carbon\Carbon::parse($seguimiento->fecha_realizacion)->format('d/m/Y') : 'Pendiente' }}</td>
                                            <td>
                                                @if($seguimiento->fecha_realizacion)
                                                    <span class="badge bg-success">Realizado</span>
                                                @else
                                                    <span class="badge bg-warning">Pendiente</span>
                                                @endif
                                            </td>
                                            <td>-</td>
                                            <td>{{ $seguimiento->enteControl->nombre ?? 'N/A' }}</td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-info" title="Ver">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Estadísticas de Seguimientos -->
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h3>{{ $totalSeguimientos }}</h3>
                                            <small class="text-muted">Total Seguimientos</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ $seguimientosPendientes }}</h3>
                                            <small>Pendientes</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ $seguimientosRealizados }}</h3>
                                            <small>Realizados</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-dark">
                                        <div class="card-body text-center">
                                            <h3>{{ $porcentajeSeguimientos }}%</h3>
                                            <small>Completado</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                No hay seguimientos registrados para esta tarea.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- TAB 3: Evidencias -->
                <div class="tab-pane fade" id="evidencias" role="tabpanel">
                    <div class="card shadow-sm mt-3">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Evidencias Documentales</h5>
                                @if($tarea->estado != 'anulado')
                                <button class="btn btn-sm btn-success">
                                    <i class="bi bi-cloud-upload"></i> Subir Evidencia
                                </button>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @if($evidencias->count() > 0)
                            <div class="row">
                                @foreach($evidencias as $evidencia)
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="bi bi-file-earmark-{{ $evidencia->icono_tipo }}"></i>
                                                {{ $evidencia->nombre_archivo }}
                                            </h6>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    Tipo: {{ strtoupper($evidencia->tipo_archivo) }}<br>
                                                    Tamaño: {{ number_format($evidencia->tamano_kb, 2) }} KB<br>
                                                    Subido: {{ $evidencia->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </p>
                                            <div class="d-flex justify-content-between">
                                                <a href="#" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-download"></i> Descargar
                                                </a>
                                                @if(auth()->user()->role == 'super_administrador')
                                                <button class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                No hay evidencias adjuntas a esta tarea.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- TAB 4: Auditoría -->
                <div class="tab-pane fade" id="auditoria" role="tabpanel">
                    <div class="card shadow-sm mt-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Trazabilidad de la Tarea</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <!-- Creación -->
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">
                                            <i class="bi bi-plus-circle"></i> Tarea Creada
                                        </h6>
                                        <p class="mb-0">
                                            <strong>Usuario:</strong> {{ $tarea->createdBy->name ?? 'N/A' }}<br>
                                            <strong>Fecha:</strong> {{ $tarea->created_at->format('d/m/Y H:i:s') }}<br>
                                            <strong>Rol:</strong> {{ ucfirst($tarea->createdBy->role ?? 'N/A') }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Última Actualización -->
                                @if($tarea->updated_at != $tarea->created_at)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-warning"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">
                                            <i class="bi bi-pencil"></i> Última Actualización
                                        </h6>
                                        <p class="mb-0">
                                            <strong>Usuario:</strong> {{ $tarea->updatedBy->name ?? 'N/A' }}<br>
                                            <strong>Fecha:</strong> {{ $tarea->updated_at->format('d/m/Y H:i:s') }}<br>
                                            <strong>Rol:</strong> {{ ucfirst($tarea->updatedBy->role ?? 'N/A') }}
                                        </p>
                                    </div>
                                </div>
                                @endif

                                <!-- Eliminación (si aplica) -->
                                @if($tarea->deleted_at)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-danger"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">
                                            <i class="bi bi-trash"></i> Tarea Eliminada
                                        </h6>
                                        <p class="mb-0">
                                            <strong>Usuario:</strong> {{ $tarea->deletedBy->name ?? 'N/A' }}<br>
                                            <strong>Fecha:</strong> {{ $tarea->deleted_at->format('d/m/Y H:i:s') }}<br>
                                            <strong>Rol:</strong> {{ ucfirst($tarea->deletedBy->role ?? 'N/A') }}
                                        </p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Iniciar Tarea -->
<div class="modal fade" id="modalIniciar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('paa.tareas.iniciar', [$paa, $tarea]) }}" method="POST">
                @csrf
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Iniciar Tarea</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de iniciar esta tarea?</p>
                    <p>El estado cambiará a <strong>"En Proceso"</strong> y se registrará la fecha de inicio real.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">Iniciar Tarea</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Completar Tarea -->
<div class="modal fade" id="modalCompletar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('paa.tareas.completar', [$paa, $tarea]) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Completar Tarea</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de completar esta tarea?</p>

                    <div class="mb-3">
                        <label for="evaluacion" class="form-label">Evaluación de la Tarea <span class="text-danger">*</span></label>
                        <select name="evaluacion" id="evaluacion" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <option value="bien">Bien - Tarea ejecutada satisfactoriamente</option>
                            <option value="mal">Mal - Tarea con inconvenientes o incompleta</option>
                        </select>
                    </div>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        El estado cambiará a <strong>"Realizado"</strong> y se registrará la fecha de finalización.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Completar Tarea</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Anular Tarea -->
<div class="modal fade" id="modalAnular" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('paa.tareas.anular', [$paa, $tarea]) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Anular Tarea</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>¡Atención!</strong> Esta acción anulará la tarea permanentemente.
                    </div>

                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo de Anulación <span class="text-danger">*</span></label>
                        <textarea name="motivo"
                                  id="motivo"
                                  class="form-control"
                                  rows="3"
                                  required
                                  minlength="10"
                                  placeholder="Describa el motivo de la anulación (mínimo 10 caracteres)..."></textarea>
                        <small class="text-muted">Mínimo 10 caracteres</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Anular Tarea</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
    border-left: 2px solid #dee2e6;
}

.timeline-item:last-child {
    border-left: 2px solid transparent;
}

.timeline-marker {
    position: absolute;
    left: -6px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-content {
    padding-left: 20px;
}
</style>
@endpush
@endsection

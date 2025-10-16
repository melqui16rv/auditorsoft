@extends('layouts.app')

@section('title', 'Seguimiento #' . $seguimiento->id)

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
                    <li class="breadcrumb-item active">Seguimiento #{{ $seguimiento->id }}</li>
                </ol>
            </nav>

            <!-- Header con Acciones -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3><i class="bi bi-list-check"></i> Detalle del Seguimiento</h3>
                <div>
                    @if($seguimiento->estado == 'pendiente')
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRealizar">
                        <i class="bi bi-check-circle"></i> Marcar Realizado
                    </button>
                    @endif
                    
                    @if($seguimiento->estado != 'anulado' && $seguimiento->estado != 'realizado')
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalAnular">
                        <i class="bi bi-x-circle"></i> Anular
                    </button>
                    @endif
                    
                    @if($seguimiento->estado != 'realizado')
                    <a href="{{ route('paa.seguimientos.edit', [$paa, $tarea, $seguimiento]) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    @endif
                    
                    <a href="{{ route('paa.seguimientos.index', [$paa, $tarea]) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs" id="seguimientoTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button">
                        <i class="bi bi-info-circle"></i> Información
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="evidencias-tab" data-bs-toggle="tab" data-bs-target="#evidencias" type="button">
                        <i class="bi bi-file-earmark-text"></i> Evidencias
                        <span class="badge bg-primary">{{ $totalEvidencias }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="auditoria-tab" data-bs-toggle="tab" data-bs-target="#auditoria" type="button">
                        <i class="bi bi-clock-history"></i> Auditoría
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="seguimientoTabContent">
                <!-- TAB 1: Información -->
                <div class="tab-pane fade show active" id="info" role="tabpanel">
                    <div class="card shadow-sm mt-3">
                        <div class="card-body">
                            <!-- Estado y Evaluación -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Estado</h6>
                                    {!! $seguimiento->estado_badge !!}
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Evaluación</h6>
                                    {!! $seguimiento->evaluacion_badge !!}
                                </div>
                            </div>

                            <hr>

                            <!-- Información del Seguimiento -->
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Punto de Control:</th>
                                            <td>{{ $seguimiento->descripcion_punto_control }}</td>
                                        </tr>
                                        <tr>
                                            <th>Fecha Seguimiento:</th>
                                            <td>{{ \Carbon\Carbon::parse($seguimiento->fecha_seguimiento)->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Ente de Control:</th>
                                            <td>
                                                <span class="badge bg-secondary">{{ $seguimiento->enteControl->nombre }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Observaciones:</th>
                                            <td>{{ $seguimiento->observaciones ?: 'Sin observaciones' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <!-- Información de la Tarea -->
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title"><i class="bi bi-list-task"></i> Tarea Asociada</h6>
                                            <p class="mb-1">
                                                <strong>Descripción:</strong><br>
                                                <small>{{ $tarea->descripcion_tarea }}</small>
                                            </p>
                                            <p class="mb-1">
                                                <strong>Rol OCI:</strong> {{ $tarea->rolOci->nombre_rol }}
                                            </p>
                                            <p class="mb-1">
                                                <strong>Responsable:</strong> {{ $tarea->responsable->name }}
                                            </p>
                                            <p class="mb-0">
                                                <strong>Estado:</strong> {!! $tarea->estado_badge !!}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Información del PAA -->
                                    <div class="card bg-light mt-3">
                                        <div class="card-body">
                                            <h6 class="card-title"><i class="bi bi-folder"></i> PAA</h6>
                                            <p class="mb-1">
                                                <strong>Código:</strong> {{ $paa->codigo }}
                                            </p>
                                            <p class="mb-1">
                                                <strong>Vigencia:</strong> <span class="badge bg-secondary">{{ $paa->vigencia }}</span>
                                            </p>
                                            <p class="mb-0">
                                                <strong>Entidad:</strong> {{ $paa->nombre_entidad }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: Evidencias -->
                <div class="tab-pane fade" id="evidencias" role="tabpanel">
                    <div class="card shadow-sm mt-3">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Evidencias Documentales</h5>
                                @if(in_array(auth()->user()->role, ['jefe_auditor', 'auditor', 'super_administrador']) && !$seguimiento->deleted_at)
                                <button class="btn btn-sm btn-success" 
                                        onclick="openUploadEvidenciaModal('App\\Models\\PAASeguimiento', {{ $seguimiento->id }}, 'Seguimiento #{{ $seguimiento->id }} - {{ $seguimiento->tarea->nombre }}')">
                                    <i class="bi bi-cloud-upload"></i> Subir Evidencia
                                </button>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @if($seguimiento->evidencias->count() > 0)
                            <div class="row">
                                @foreach($seguimiento->evidencias as $evidencia)
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="text-center mb-2">
                                                {!! $evidencia->getIconHtml() !!}
                                            </div>
                                            <h6 class="card-title text-truncate" title="{{ $evidencia->nombre_archivo }}">
                                                {{ $evidencia->nombre_archivo }}
                                            </h6>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <strong>Tipo:</strong> {{ strtoupper($evidencia->tipo_archivo) }}<br>
                                                    <strong>Tamaño:</strong> {{ number_format($evidencia->tamano_kb, 2) }} KB<br>
                                                    <strong>Subido:</strong> {{ $evidencia->created_at->format('d/m/Y H:i') }}<br>
                                                    @if($evidencia->descripcion)
                                                    <strong>Descripción:</strong> {{ Str::limit($evidencia->descripcion, 50) }}
                                                    @endif
                                                </small>
                                            </p>
                                            <div class="d-flex justify-content-between gap-1 mt-auto">
                                                <a href="{{ route('evidencias.show', $evidencia->id) }}" 
                                                   class="btn btn-sm btn-info flex-fill"
                                                   title="Ver detalle">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('evidencias.download', $evidencia->id) }}" 
                                                   class="btn btn-sm btn-primary flex-fill"
                                                   title="Descargar">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                @if(auth()->user()->role === 'super_administrador')
                                                <form action="{{ route('evidencias.destroy', $evidencia->id) }}" 
                                                      method="POST" 
                                                      class="d-inline flex-fill"
                                                      onsubmit="return confirm('¿Está seguro de eliminar esta evidencia?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-danger w-100"
                                                            title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
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
                                No hay evidencias adjuntas a este seguimiento.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- TAB 3: Auditoría -->
                <div class="tab-pane fade" id="auditoria" role="tabpanel">
                    <div class="card shadow-sm mt-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Trazabilidad</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <!-- Creación -->
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">
                                            <i class="bi bi-plus-circle"></i> Seguimiento Creado
                                        </h6>
                                        <p class="mb-0">
                                            <strong>Usuario:</strong> {{ $seguimiento->createdBy->name ?? 'N/A' }}<br>
                                            <strong>Fecha:</strong> {{ $seguimiento->created_at->format('d/m/Y H:i:s') }}<br>
                                            <strong>Rol:</strong> {{ ucfirst($seguimiento->createdBy->role ?? 'N/A') }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Última Actualización -->
                                @if($seguimiento->updated_at != $seguimiento->created_at)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-warning"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">
                                            <i class="bi bi-pencil"></i> Última Actualización
                                        </h6>
                                        <p class="mb-0">
                                            <strong>Usuario:</strong> {{ $seguimiento->updatedBy->name ?? 'N/A' }}<br>
                                            <strong>Fecha:</strong> {{ $seguimiento->updated_at->format('d/m/Y H:i:s') }}<br>
                                            <strong>Rol:</strong> {{ ucfirst($seguimiento->updatedBy->role ?? 'N/A') }}
                                        </p>
                                    </div>
                                </div>
                                @endif

                                <!-- Eliminación -->
                                @if($seguimiento->deleted_at)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-danger"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">
                                            <i class="bi bi-trash"></i> Seguimiento Eliminado
                                        </h6>
                                        <p class="mb-0">
                                            <strong>Usuario:</strong> {{ $seguimiento->deletedBy->name ?? 'N/A' }}<br>
                                            <strong>Fecha:</strong> {{ $seguimiento->deleted_at->format('d/m/Y H:i:s') }}<br>
                                            <strong>Rol:</strong> {{ ucfirst($seguimiento->deletedBy->role ?? 'N/A') }}
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

<!-- Modal Realizar -->
<div class="modal fade" id="modalRealizar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('paa.seguimientos.realizar', [$paa, $tarea, $seguimiento]) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Marcar como Realizado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de marcar este seguimiento como realizado?</p>
                    <p>El estado cambiará a <strong>"Realizado"</strong>.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Anular -->
<div class="modal fade" id="modalAnular" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('paa.seguimientos.anular', [$paa, $tarea, $seguimiento]) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Anular Seguimiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>¡Atención!</strong> Esta acción anulará el seguimiento.
                    </div>
                    
                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo de Anulación <span class="text-danger">*</span></label>
                        <textarea name="motivo" 
                                  id="motivo" 
                                  class="form-control" 
                                  rows="3" 
                                  required 
                                  minlength="10"
                                  placeholder="Describa el motivo (mínimo 10 caracteres)..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Anular Seguimiento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Incluir modal de carga de evidencias -->
@include('evidencias.upload-modal')

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

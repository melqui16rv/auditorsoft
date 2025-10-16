@extends('layouts.app')

@section('title', 'Detalle de Evidencia')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            @if($evidencia->evidenciable_type === 'App\Models\PAASeguimiento')
                <li class="breadcrumb-item"><a href="{{ route('paa.index') }}">PAA</a></li>
                <li class="breadcrumb-item"><a href="{{ route('paa.show', $evidencia->evidenciable->tarea->paa_id) }}">
                    {{ $evidencia->evidenciable->tarea->paa->codigo }}
                </a></li>
                <li class="breadcrumb-item"><a href="{{ route('paa.tareas.show', [$evidencia->evidenciable->tarea->paa_id, $evidencia->evidenciable->tarea_id]) }}">
                    {{ $evidencia->evidenciable->tarea->nombre }}
                </a></li>
                <li class="breadcrumb-item"><a href="{{ route('paa.tareas.seguimientos.show', [$evidencia->evidenciable->tarea->paa_id, $evidencia->evidenciable->tarea_id, $evidencia->evidenciable_id]) }}">
                    Seguimiento #{{ $evidencia->evidenciable->id }}
                </a></li>
            @elseif($evidencia->evidenciable_type === 'App\Models\PAATarea')
                <li class="breadcrumb-item"><a href="{{ route('paa.index') }}">PAA</a></li>
                <li class="breadcrumb-item"><a href="{{ route('paa.show', $evidencia->evidenciable->paa_id) }}">
                    {{ $evidencia->evidenciable->paa->codigo }}
                </a></li>
                <li class="breadcrumb-item"><a href="{{ route('paa.tareas.show', [$evidencia->evidenciable->paa_id, $evidencia->evidenciable_id]) }}">
                    {{ $evidencia->evidenciable->nombre }}
                </a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">Evidencia: {{ $evidencia->nombre_archivo }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Información del archivo -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file me-2"></i>Información del Archivo
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-2 text-center">
                            {!! $evidencia->getIconHtml() !!}
                        </div>
                        <div class="col-md-10">
                            <h4 class="mb-3">{{ $evidencia->nombre_archivo }}</h4>
                            <dl class="row mb-0">
                                <dt class="col-sm-3">Tipo de Archivo:</dt>
                                <dd class="col-sm-9">
                                    <span class="badge bg-secondary">{{ strtoupper($evidencia->tipo_archivo) }}</span>
                                </dd>

                                <dt class="col-sm-3">Tamaño:</dt>
                                <dd class="col-sm-9">{{ number_format($evidencia->tamano_kb, 2) }} KB</dd>

                                <dt class="col-sm-3">Fecha de Subida:</dt>
                                <dd class="col-sm-9">{{ $evidencia->created_at->format('d/m/Y H:i:s') }}</dd>

                                @if($evidencia->descripcion)
                                    <dt class="col-sm-3">Descripción:</dt>
                                    <dd class="col-sm-9">{{ $evidencia->descripcion }}</dd>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Vista previa de imágenes -->
                    @if(in_array($evidencia->tipo_archivo, ['jpg', 'jpeg', 'png']))
                        <div class="text-center mb-4">
                            <img src="{{ asset('storage/' . $evidencia->ruta_archivo) }}" 
                                 alt="{{ $evidencia->nombre_archivo }}" 
                                 class="img-fluid rounded shadow-sm" 
                                 style="max-height: 500px;">
                        </div>
                    @endif

                    <!-- Acciones -->
                    <div class="d-flex gap-2">
                        <a href="{{ route('evidencias.download', $evidencia->id) }}" 
                           class="btn btn-success">
                            <i class="fas fa-download me-1"></i>Descargar
                        </a>
                        
                        @if(auth()->user()->role === 'super_administrador')
                            <form action="{{ route('evidencias.destroy', $evidencia->id) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirm('¿Está seguro de eliminar esta evidencia? Esta acción no se puede deshacer.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash me-1"></i>Eliminar
                                </button>
                            </form>
                        @endif

                        <a href="javascript:history.back()" class="btn btn-secondary ms-auto">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel lateral -->
        <div class="col-lg-4">
            <!-- Elemento Asociado -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-link me-2"></i>Elemento Asociado
                    </h6>
                </div>
                <div class="card-body">
                    @if($evidencia->evidenciable_type === 'App\Models\PAASeguimiento')
                        <h6 class="text-muted mb-2">Seguimiento</h6>
                        <p class="mb-2">
                            <strong>ID:</strong> {{ $evidencia->evidenciable->id }}<br>
                            <strong>Tarea:</strong> {{ $evidencia->evidenciable->tarea->nombre }}<br>
                            <strong>Ente de Control:</strong> {{ $evidencia->evidenciable->enteControl->nombre ?? 'N/A' }}<br>
                            <strong>Estado:</strong> 
                            @if($evidencia->evidenciable->fecha_realizacion)
                                <span class="badge bg-success">Realizado</span>
                            @elseif($evidencia->evidenciable->deleted_at)
                                <span class="badge bg-danger">Anulado</span>
                            @else
                                <span class="badge bg-warning">Pendiente</span>
                            @endif
                        </p>
                        <a href="{{ route('paa.tareas.seguimientos.show', [$evidencia->evidenciable->tarea->paa_id, $evidencia->evidenciable->tarea_id, $evidencia->evidenciable_id]) }}" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i>Ver Seguimiento
                        </a>
                    @elseif($evidencia->evidenciable_type === 'App\Models\PAATarea')
                        <h6 class="text-muted mb-2">Tarea</h6>
                        <p class="mb-2">
                            <strong>Nombre:</strong> {{ $evidencia->evidenciable->nombre }}<br>
                            <strong>PAA:</strong> {{ $evidencia->evidenciable->paa->codigo }}<br>
                            <strong>Tipo:</strong> {{ $evidencia->evidenciable->tipo }}<br>
                            <strong>Estado:</strong> 
                            <span class="badge bg-{{ $evidencia->evidenciable->estado === 'realizada' ? 'success' : ($evidencia->evidenciable->estado === 'en_proceso' ? 'warning' : 'secondary') }}">
                                {{ ucfirst(str_replace('_', ' ', $evidencia->evidenciable->estado)) }}
                            </span>
                        </p>
                        <a href="{{ route('paa.tareas.show', [$evidencia->evidenciable->paa_id, $evidencia->evidenciable_id]) }}" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i>Ver Tarea
                        </a>
                    @endif
                </div>
            </div>

            <!-- Información de Auditoría -->
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-history me-2"></i>Auditoría
                    </h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0 small">
                        <dt class="col-sm-5">Subido por:</dt>
                        <dd class="col-sm-7">
                            {{ $evidencia->creator->name ?? 'N/A' }}<br>
                            <small class="text-muted">{{ $evidencia->created_at->format('d/m/Y H:i') }}</small>
                        </dd>

                        @if($evidencia->deleted_at)
                            <dt class="col-sm-5">Eliminado por:</dt>
                            <dd class="col-sm-7">
                                {{ $evidencia->deleter->name ?? 'N/A' }}<br>
                                <small class="text-muted">{{ $evidencia->deleted_at->format('d/m/Y H:i') }}</small>
                            </dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.file-icon {
    font-size: 4rem;
}
</style>
@endpush

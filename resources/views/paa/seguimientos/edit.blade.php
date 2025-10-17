@extends('layouts.app')

@section('title', 'Editar Seguimiento #' . $seguimiento->id)

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
                    <li class="breadcrumb-item"><a href="{{ route('paa.seguimientos.show', [$paa, $tarea, $seguimiento]) }}">Seguimiento #{{ $seguimiento->id }}</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </nav>

            <!-- Card Principal -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square"></i>
                        Editar Seguimiento
                    </h4>
                </div>

                <div class="card-body">
                    <!-- Estado Actual -->
                    <div class="alert alert-light border">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Estado:</strong> {!! $seguimiento->estado_badge !!}
                            </div>
                            <div class="col-md-6">
                                <strong>Evaluación:</strong> {!! $seguimiento->evaluacion_badge !!}
                            </div>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <form action="{{ route('paa.seguimientos.update', [$paa, $tarea, $seguimiento]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <!-- Descripción -->
                                <div class="mb-3">
                                    <label for="observaciones" class="form-label">
                                        Observaciones <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="observaciones" 
                                              id="observaciones" 
                                              class="form-control @error('observaciones') is-invalid @enderror"
                                              rows="4"
                                              minlength="10"
                                              maxlength="1000"
                                              {{ $seguimiento->fecha_realizacion ? 'disabled' : '' }}>{{ old('observaciones', $seguimiento->observaciones) }}</textarea>
                                    @error('observaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        <span id="charCount">{{ strlen($seguimiento->observaciones) }}</span>/1000
                                    </small>
                                </div>

                                <!-- Fecha -->
                                <div class="mb-3">
                                    <label for="fecha_realizacion" class="form-label">
                                        Fecha de Realización <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           name="fecha_realizacion" 
                                           id="fecha_realizacion" 
                                           class="form-control @error('fecha_realizacion') is-invalid @enderror"
                                           value="{{ old('fecha_realizacion', optional($seguimiento->fecha_realizacion)->format('Y-m-d')) }}" 
                                           {{ $seguimiento->fecha_realizacion ? 'disabled' : '' }}>
                                    @error('fecha_realizacion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Ente de Control -->
                                <div class="mb-3">
                                    <label for="ente_control_id" class="form-label">
                                        Ente de Control <span class="text-danger">*</span>
                                    </label>
                                    <select name="ente_control_id" 
                                            id="ente_control_id" 
                                            class="form-select @error('ente_control_id') is-invalid @enderror"
                                            {{ $seguimiento->estado == 'realizado' ? 'disabled' : '' }}>
                                        @foreach($entesControl as $ente)
                                            <option value="{{ $ente->id }}" {{ old('ente_control_id', $seguimiento->ente_control_id) == $ente->id ? 'selected' : '' }}>
                                                {{ $ente->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('ente_control_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Estado (Solo Super Admin) -->
                                @if(auth()->user()->role == 'super_administrador')
                                <div class="mb-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select name="estado" id="estado" class="form-select">
                                        <option value="pendiente" {{ old('estado', $seguimiento->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="realizado" {{ old('estado', $seguimiento->estado) == 'realizado' ? 'selected' : '' }}>Realizado</option>
                                        <option value="anulado" {{ old('estado', $seguimiento->estado) == 'anulado' ? 'selected' : '' }}>Anulado</option>
                                    </select>
                                </div>
                                @endif

                                <!-- Evaluación -->
                                <div class="mb-3">
                                    <label for="evaluacion" class="form-label">Evaluación</label>
                                    <select name="evaluacion" id="evaluacion" class="form-select">
                                        <option value="pendiente" {{ old('evaluacion', $seguimiento->evaluacion) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="bien" {{ old('evaluacion', $seguimiento->evaluacion) == 'bien' ? 'selected' : '' }}>Bien</option>
                                        <option value="mal" {{ old('evaluacion', $seguimiento->evaluacion) == 'mal' ? 'selected' : '' }}>Mal</option>
                                    </select>
                                </div>

                                <!-- Observaciones -->
                                <div class="mb-3">
                                    <label for="observaciones" class="form-label">Observaciones</label>
                                    <textarea name="observaciones" 
                                              id="observaciones" 
                                              class="form-control"
                                              rows="4"
                                              maxlength="2000">{{ old('observaciones', $seguimiento->observaciones) }}</textarea>
                                    <small class="text-muted">
                                        <span id="obsCount">{{ strlen($seguimiento->observaciones ?? '') }}</span>/2000
                                    </small>
                                </div>

                                <!-- Auditoría -->
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6><i class="bi bi-clock-history"></i> Auditoría</h6>
                                        <p class="mb-1">
                                            <strong>Creado:</strong> {{ $seguimiento->createdBy->name ?? 'N/A' }}<br>
                                            <small class="text-muted">{{ $seguimiento->created_at->format('d/m/Y H:i') }}</small>
                                        </p>
                                        @if($seguimiento->updated_at != $seguimiento->created_at)
                                        <p class="mb-0">
                                            <strong>Modificado:</strong> {{ $seguimiento->updatedBy->name ?? 'N/A' }}<br>
                                            <small class="text-muted">{{ $seguimiento->updated_at->format('d/m/Y H:i') }}</small>
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('paa.seguimientos.show', [$paa, $tarea, $seguimiento]) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                            @if($seguimiento->estado != 'realizado')
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save"></i> Actualizar
                            </button>
                            @else
                            <button type="button" class="btn btn-warning" disabled>
                                <i class="bi bi-lock"></i> Realizado
                            </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const obs = document.getElementById('observaciones');
    
    if (obs && !obs.disabled) {
        obs.addEventListener('input', () => {
            document.getElementById('charCount').textContent = obs.value.length;
        });
    }
});
</script>
@endpush
@endsection

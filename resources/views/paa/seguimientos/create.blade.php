@extends('layouts.app')

@section('title', 'Crear Seguimiento - Tarea #' . $tarea->id)

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
                    <li class="breadcrumb-item active">Nuevo Seguimiento</li>
                </ol>
            </nav>

            <!-- Card Principal -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-list-check"></i>
                        Nuevo Punto de Control / Seguimiento
                    </h4>
                    <p class="mb-0 mt-1">
                        <small>{{ $paa->codigo }} - Tarea: {{ Str::limit($tarea->descripcion_tarea, 50) }}</small>
                    </p>
                </div>

                <div class="card-body">
                    <!-- Información de la Tarea -->
                    <div class="alert alert-light border" role="alert">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Rol OCI:</strong> {{ $tarea->rolOci->nombre_rol }}
                            </div>
                            <div class="col-md-4">
                                <strong>Responsable:</strong> {{ $tarea->responsable->name }}
                            </div>
                            <div class="col-md-4">
                                <strong>Estado Tarea:</strong> {!! $tarea->estado_badge !!}
                            </div>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <form action="{{ route('paa.seguimientos.store', [$paa, $tarea]) }}" method="POST" id="formCreateSeguimiento">
                        @csrf

                        <div class="row">
                            <!-- Columna Izquierda -->
                            <div class="col-md-6">
                                <!-- Descripción del Punto de Control -->
                                <div class="mb-3">
                                    <label for="descripcion_punto_control" class="form-label">
                                        Descripción del Punto de Control <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="descripcion_punto_control" 
                                              id="descripcion_punto_control" 
                                              class="form-control @error('descripcion_punto_control') is-invalid @enderror"
                                              rows="4"
                                              required
                                              minlength="10"
                                              maxlength="1000"
                                              placeholder="Describa el punto de control o seguimiento a realizar...">{{ old('descripcion_punto_control') }}</textarea>
                                    @error('descripcion_punto_control')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <span id="charCount">0</span>/1000 caracteres (mínimo 10)
                                    </small>
                                </div>

                                <!-- Fecha de Seguimiento -->
                                <div class="mb-3">
                                    <label for="fecha_seguimiento" class="form-label">
                                        Fecha del Seguimiento <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           name="fecha_seguimiento" 
                                           id="fecha_seguimiento" 
                                           class="form-control @error('fecha_seguimiento') is-invalid @enderror"
                                           value="{{ old('fecha_seguimiento', date('Y-m-d')) }}" 
                                           min="{{ date('Y-m-d') }}"
                                           required>
                                    @error('fecha_seguimiento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Fecha en que se realizará el seguimiento</small>
                                </div>

                                <!-- Ente de Control -->
                                <div class="mb-3">
                                    <label for="ente_control_id" class="form-label">
                                        Ente de Control <span class="text-danger">*</span>
                                    </label>
                                    <select name="ente_control_id" 
                                            id="ente_control_id" 
                                            class="form-select @error('ente_control_id') is-invalid @enderror" 
                                            required>
                                        <option value="">Seleccione el ente de control...</option>
                                        @foreach($entesControl as $ente)
                                            <option value="{{ $ente->id }}" {{ old('ente_control_id') == $ente->id ? 'selected' : '' }}>
                                                {{ $ente->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('ente_control_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Ente de control asociado (Contraloría, Procuraduría, etc.)</small>
                                </div>
                            </div>

                            <!-- Columna Derecha -->
                            <div class="col-md-6">
                                <!-- Estado -->
                                <div class="mb-3">
                                    <label for="estado" class="form-label">
                                        Estado Inicial
                                    </label>
                                    <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror">
                                        <option value="pendiente" {{ old('estado', 'pendiente') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="realizado" {{ old('estado') == 'realizado' ? 'selected' : '' }}>Realizado</option>
                                    </select>
                                    @error('estado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Por defecto se creará como "Pendiente"</small>
                                </div>

                                <!-- Evaluación -->
                                <div class="mb-3">
                                    <label for="evaluacion" class="form-label">
                                        Evaluación Inicial
                                    </label>
                                    <select name="evaluacion" id="evaluacion" class="form-select @error('evaluacion') is-invalid @enderror">
                                        <option value="pendiente" {{ old('evaluacion', 'pendiente') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="bien" {{ old('evaluacion') == 'bien' ? 'selected' : '' }}>Bien</option>
                                        <option value="mal" {{ old('evaluacion') == 'mal' ? 'selected' : '' }}>Mal</option>
                                    </select>
                                    @error('evaluacion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Evaluación del punto de control</small>
                                </div>

                                <!-- Observaciones -->
                                <div class="mb-3">
                                    <label for="observaciones" class="form-label">
                                        Observaciones
                                    </label>
                                    <textarea name="observaciones" 
                                              id="observaciones" 
                                              class="form-control @error('observaciones') is-invalid @enderror"
                                              rows="4"
                                              maxlength="2000"
                                              placeholder="Observaciones, hallazgos o notas adicionales...">{{ old('observaciones') }}</textarea>
                                    @error('observaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <span id="obsCharCount">0</span>/2000 caracteres
                                    </small>
                                </div>

                                <!-- Card Informativo -->
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="bi bi-info-circle"></i> Información</h6>
                                        <p class="mb-1"><small><strong>Tarea:</strong> {{ $tarea->descripcion_tarea }}</small></p>
                                        <p class="mb-0"><small><strong>PAA:</strong> {{ $paa->codigo }} - {{ $paa->vigencia }}</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información Adicional -->
                        <div class="card mt-3 bg-light">
                            <div class="card-body">
                                <h6 class="card-title"><i class="bi bi-lightbulb"></i> Puntos de Control</h6>
                                <p class="mb-0">
                                    Los puntos de control permiten hacer seguimiento detallado del cumplimiento de cada tarea.
                                    Puede adjuntar evidencias documentales a cada seguimiento.
                                </p>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('paa.tareas.show', [$paa, $tarea]) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save"></i> Crear Seguimiento
                            </button>
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
    // Contador de caracteres para descripción
    const descripcion = document.getElementById('descripcion_punto_control');
    const charCount = document.getElementById('charCount');
    
    descripcion.addEventListener('input', function() {
        charCount.textContent = this.value.length;
        if (this.value.length < 10) {
            charCount.classList.add('text-danger');
        } else {
            charCount.classList.remove('text-danger');
        }
    });

    // Contador de caracteres para observaciones
    const observaciones = document.getElementById('observaciones');
    const obsCharCount = document.getElementById('obsCharCount');
    
    if (observaciones) {
        observaciones.addEventListener('input', function() {
            obsCharCount.textContent = this.value.length;
        });
    }

    // Validación del formulario
    document.getElementById('formCreateSeguimiento').addEventListener('submit', function(e) {
        const desc = document.getElementById('descripcion_punto_control').value;
        const fecha = document.getElementById('fecha_seguimiento').value;
        const ente = document.getElementById('ente_control_id').value;

        if (!desc || !fecha || !ente) {
            e.preventDefault();
            alert('Por favor complete todos los campos obligatorios marcados con *');
            return false;
        }

        if (desc.length < 10) {
            e.preventDefault();
            alert('La descripción debe tener al menos 10 caracteres.');
            return false;
        }
    });

    // Inicializar contadores
    charCount.textContent = descripcion.value.length;
    if (observaciones) {
        obsCharCount.textContent = observaciones.value.length;
    }
});
</script>
@endpush
@endsection

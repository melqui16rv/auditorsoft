@extends('layouts.app')

@section('title', 'Editar Tarea - ' . $tarea->descripcion)

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
                    <li class="breadcrumb-item"><a href="{{ route('paa.tareas.show', [$paa, $tarea]) }}">Tarea #{{ $tarea->id }}</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </nav>

            <!-- Card Principal -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square"></i>
                        Editar Tarea
                    </h4>
                    <p class="mb-0 mt-1">
                        <small>{{ $paa->codigo }} - Tarea #{{ $tarea->id }}</small>
                    </p>
                </div>

                <div class="card-body">
                    <!-- Estado Actual -->
                    <div class="alert alert-light border" role="alert">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Estado Actual:</strong> {!! $tarea->estado_badge !!}
                            </div>
                            <div class="col-md-4">
                                <strong>Evaluación:</strong> {!! $tarea->evaluacion_badge !!}
                            </div>
                            <div class="col-md-4">
                                <strong>Progreso:</strong> 
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar {{ $tarea->porcentaje_avance == 100 ? 'bg-success' : 'bg-info' }}" 
                                         role="progressbar" 
                                         style="width: {{ $tarea->porcentaje_avance }}%">
                                        {{ $tarea->porcentaje_avance }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <form action="{{ route('paa.tareas.update', [$paa, $tarea]) }}" method="POST" id="formEditTarea">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Columna Izquierda -->
                            <div class="col-md-6">
                                <!-- Rol OCI -->
                                <div class="mb-3">
                                    <label for="rol_oci" class="form-label">
                                        Rol OCI <span class="text-danger">*</span>
                                    </label>
                                    <select name="rol_oci" 
                                            id="rol_oci" 
                                            class="form-select @error('rol_oci') is-invalid @enderror" 
                                            {{ $tarea->estado == 'realizada' ? 'disabled' : '' }}>
                                        <option value="">Seleccione el rol OCI...</option>
                                        <option value="fomento_cultura" {{ (old('rol_oci', $tarea->rol_oci) == 'fomento_cultura') ? 'selected' : '' }}>
                                            Fomento de la Cultura del Control
                                        </option>
                                        <option value="apoyo_fortalecimiento" {{ (old('rol_oci', $tarea->rol_oci) == 'apoyo_fortalecimiento') ? 'selected' : '' }}>
                                            Apoyo al Fortalecimiento
                                        </option>
                                        <option value="investigaciones" {{ (old('rol_oci', $tarea->rol_oci) == 'investigaciones') ? 'selected' : '' }}>
                                            Investigaciones
                                        </option>
                                        <option value="evaluacion_control" {{ (old('rol_oci', $tarea->rol_oci) == 'evaluacion_control') ? 'selected' : '' }}>
                                            Evaluación de Control
                                        </option>
                                        <option value="evaluacion_gestion" {{ (old('rol_oci', $tarea->rol_oci) == 'evaluacion_gestion') ? 'selected' : '' }}>
                                            Evaluación de Gestión
                                        </option>
                                    </select>
                                    @error('rol_oci')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Descripción de la Tarea -->
                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">
                                        Descripción de la Tarea <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="descripcion" 
                                              id="descripcion" 
                                              class="form-control @error('descripcion') is-invalid @enderror"
                                              rows="4"
                                              minlength="10"
                                              maxlength="1000"
                                              {{ $tarea->estado == 'realizada' ? 'disabled' : '' }}>{{ old('descripcion', $tarea->descripcion) }}</textarea>
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <span id="charCount">{{ strlen($tarea->descripcion) }}</span>/1000 caracteres
                                    </small>
                                </div>

                                <!-- Fechas Planeadas -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="fecha_inicio" class="form-label">
                                                Inicio <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" 
                                                   name="fecha_inicio" 
                                                   id="fecha_inicio" 
                                                   class="form-control @error('fecha_inicio') is-invalid @enderror"
                                                   value="{{ old('fecha_inicio', optional($tarea->fecha_inicio)->format('Y-m-d')) }}" 
                                                   {{ $tarea->estado == 'realizada' ? 'disabled' : '' }}>
                                            @error('fecha_inicio')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="fecha_fin" class="form-label">
                                                Fin <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" 
                                                   name="fecha_fin" 
                                                   id="fecha_fin" 
                                                   class="form-control @error('fecha_fin') is-invalid @enderror"
                                                   value="{{ old('fecha_fin', optional($tarea->fecha_fin)->format('Y-m-d')) }}" 
                                                   {{ $tarea->estado == 'realizada' ? 'disabled' : '' }}>
                                            @error('fecha_fin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                            <!-- Columna Derecha -->
                            <div class="col-md-6">
                                <!-- Responsable -->
                                <div class="mb-3">
                                    <label for="auditor_responsable_id" class="form-label">
                                        Responsable <span class="text-danger">*</span>
                                    </label>
                                    <select name="auditor_responsable_id" 
                                            id="auditor_responsable_id" 
                                            class="form-select @error('auditor_responsable_id') is-invalid @enderror" 
                                            {{ $tarea->estado == 'realizada' ? 'disabled' : '' }}>
                                        @foreach($responsables as $responsable)
                                            <option value="{{ $responsable->id }}" {{ (old('auditor_responsable_id', $tarea->auditor_responsable_id) == $responsable->id) ? 'selected' : '' }}>
                                                {{ $responsable->name }} - {{ ucfirst($responsable->role) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('auditor_responsable_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Estado (si es Super Admin) -->
                                @if(auth()->user()->role == 'super_administrador')
                                <div class="mb-3">
                                    <label for="estado" class="form-label">
                                        Estado
                                    </label>
                                    <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror">
                                        <option value="pendiente" {{ old('estado', $tarea->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="en_proceso" {{ old('estado', $tarea->estado) == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                                        <option value="realizada" {{ old('estado', $tarea->estado) == 'realizada' ? 'selected' : '' }}>Realizada</option>
                                        <option value="anulada" {{ old('estado', $tarea->estado) == 'anulada' ? 'selected' : '' }}>Anulada</option>
                                    </select>
                                    @error('estado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('paa.tareas.show', [$paa, $tarea]) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                            @if($tarea->estado != 'realizada')
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save"></i> Actualizar Tarea
                            </button>
                            @else
                            <button type="button" class="btn btn-warning" disabled>
                                <i class="bi bi-lock"></i> Tarea Finalizada
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
    // Contador de caracteres
    const descripcion = document.getElementById('descripcion');
    const charCount = document.getElementById('charCount');
    
    if (descripcion && !descripcion.disabled) {
        descripcion.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }

    const observaciones = document.getElementById('observaciones');
    const obsCharCount = document.getElementById('obsCharCount');
    
    if (observaciones) {
        observaciones.addEventListener('input', function() {
            obsCharCount.textContent = this.value.length;
        });
    }

    // Validación de fechas
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');

    if (fechaInicio && !fechaInicio.disabled) {
        fechaInicio.addEventListener('change', function() {
            if (fechaFin.value && fechaFin.value <= this.value) {
                alert('La fecha de fin debe ser posterior a la fecha de inicio.');
            }
        });
    }

    if (fechaFin && !fechaFin.disabled) {
        fechaFin.addEventListener('change', function() {
            if (fechaInicio.value && this.value <= fechaInicio.value) {
                alert('La fecha de fin debe ser posterior a la fecha de inicio.');
            }
        });
    }
});
</script>
@endpush
@endsection

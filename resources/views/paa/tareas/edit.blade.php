@extends('layouts.app')

@section('title', 'Editar Tarea - ' . $tarea->descripcion_tarea)

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
                                    <label for="rol_oci_id" class="form-label">
                                        Rol OCI <span class="text-danger">*</span>
                                    </label>
                                    <select name="rol_oci_id" 
                                            id="rol_oci_id" 
                                            class="form-select @error('rol_oci_id') is-invalid @enderror" 
                                            {{ $tarea->estado_tarea == 'realizada' ? 'disabled' : '' }}>
                                        <option value="">Seleccione el rol OCI...</option>
                                        @foreach($rolesOci as $rol)
                                            <option value="{{ $rol->id }}" {{ (old('rol_oci_id', $tarea->rol_oci_id) == $rol->id) ? 'selected' : '' }}>
                                                {{ $rol->nombre_rol }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('rol_oci_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Descripción de la Tarea -->
                                <div class="mb-3">
                                    <label for="descripcion_tarea" class="form-label">
                                        Descripción de la Tarea <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="descripcion_tarea" 
                                              id="descripcion_tarea" 
                                              class="form-control @error('descripcion_tarea') is-invalid @enderror"
                                              rows="4"
                                              minlength="10"
                                              maxlength="1000"
                                              {{ $tarea->estado_tarea == 'realizada' ? 'disabled' : '' }}>{{ old('descripcion_tarea', $tarea->descripcion_tarea) }}</textarea>
                                    @error('descripcion_tarea')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <span id="charCount">{{ strlen($tarea->descripcion_tarea) }}</span>/1000 caracteres
                                    </small>
                                </div>

                                <!-- Fechas Planeadas -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="fecha_inicio_planeada" class="form-label">
                                                Inicio Planeado <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" 
                                                   name="fecha_inicio_planeada" 
                                                   id="fecha_inicio_planeada" 
                                                   class="form-control @error('fecha_inicio_planeada') is-invalid @enderror"
                                                   value="{{ old('fecha_inicio_planeada', $tarea->fecha_inicio_planeada) }}" 
                                                   {{ $tarea->estado_tarea == 'realizada' ? 'disabled' : '' }}>
                                            @error('fecha_inicio_planeada')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="fecha_fin_planeada" class="form-label">
                                                Fin Planeado <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" 
                                                   name="fecha_fin_planeada" 
                                                   id="fecha_fin_planeada" 
                                                   class="form-control @error('fecha_fin_planeada') is-invalid @enderror"
                                                   value="{{ old('fecha_fin_planeada', $tarea->fecha_fin_planeada) }}" 
                                                   {{ $tarea->estado_tarea == 'realizada' ? 'disabled' : '' }}>
                                            @error('fecha_fin_planeada')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Fechas Reales (si la tarea ha iniciado) -->
                                @if($tarea->estado_tarea != 'pendiente')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="fecha_inicio_real" class="form-label">
                                                Inicio Real
                                            </label>
                                            <input type="date" 
                                                   name="fecha_inicio_real" 
                                                   id="fecha_inicio_real" 
                                                   class="form-control @error('fecha_inicio_real') is-invalid @enderror"
                                                   value="{{ old('fecha_inicio_real', $tarea->fecha_inicio_real) }}" 
                                                   {{ $tarea->estado_tarea == 'realizada' ? 'disabled' : '' }}>
                                            @error('fecha_inicio_real')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="fecha_fin_real" class="form-label">
                                                Fin Real
                                            </label>
                                            <input type="date" 
                                                   name="fecha_fin_real" 
                                                   id="fecha_fin_real" 
                                                   class="form-control @error('fecha_fin_real') is-invalid @enderror"
                                                   value="{{ old('fecha_fin_real', $tarea->fecha_fin_real) }}" 
                                                   {{ $tarea->estado_tarea != 'realizada' ? 'disabled' : '' }}>
                                            @error('fecha_fin_real')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Columna Derecha -->
                            <div class="col-md-6">
                                <!-- Responsable -->
                                <div class="mb-3">
                                    <label for="responsable_id" class="form-label">
                                        Responsable <span class="text-danger">*</span>
                                    </label>
                                    <select name="responsable_id" 
                                            id="responsable_id" 
                                            class="form-select @error('responsable_id') is-invalid @enderror" 
                                            {{ $tarea->estado_tarea == 'realizada' ? 'disabled' : '' }}>
                                        @foreach($responsables as $responsable)
                                            <option value="{{ $responsable->id }}" {{ (old('responsable_id', $tarea->responsable_id) == $responsable->id) ? 'selected' : '' }}>
                                                {{ $responsable->name }} - {{ ucfirst($responsable->role) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('responsable_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Estado (si es Super Admin) -->
                                @if(auth()->user()->role == 'super_administrador')
                                <div class="mb-3">
                                    <label for="estado_tarea" class="form-label">
                                        Estado
                                    </label>
                                    <select name="estado_tarea" id="estado_tarea" class="form-select @error('estado_tarea') is-invalid @enderror">
                                        <option value="pendiente" {{ old('estado_tarea', $tarea->estado_tarea) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="en_proceso" {{ old('estado_tarea', $tarea->estado_tarea) == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                                        <option value="realizada" {{ old('estado_tarea', $tarea->estado_tarea) == 'realizada' ? 'selected' : '' }}>Realizada</option>
                                        <option value="anulada" {{ old('estado_tarea', $tarea->estado_tarea) == 'anulada' ? 'selected' : '' }}>Anulada</option>
                                        <option value="vencida" {{ old('estado_tarea', $tarea->estado_tarea) == 'vencida' ? 'selected' : '' }}>Vencida</option>
                                    </select>
                                    @error('estado_tarea')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Evaluación (si es Super Admin) -->
                                <div class="mb-3">
                                    <label for="evaluacion_general" class="form-label">
                                        Evaluación
                                    </label>
                                    <select name="evaluacion_general" id="evaluacion_general" class="form-select @error('evaluacion_general') is-invalid @enderror">
                                        <option value="pendiente" {{ old('evaluacion_general', $tarea->evaluacion_general) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="bien" {{ old('evaluacion_general', $tarea->evaluacion_general) == 'bien' ? 'selected' : '' }}>Bien</option>
                                        <option value="mal" {{ old('evaluacion_general', $tarea->evaluacion_general) == 'mal' ? 'selected' : '' }}>Mal</option>
                                    </select>
                                    @error('evaluacion_general')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endif

                                <!-- Observaciones -->
                                <div class="mb-3">
                                    <label for="observaciones" class="form-label">
                                        Observaciones
                                    </label>
                                    <textarea name="observaciones" 
                                              id="observaciones" 
                                              class="form-control @error('observaciones') is-invalid @enderror"
                                              rows="4"
                                              maxlength="2000">{{ old('observaciones', $tarea->observaciones) }}</textarea>
                                    @error('observaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <span id="obsCharCount">{{ strlen($tarea->observaciones ?? '') }}</span>/2000 caracteres
                                    </small>
                                </div>

                                <!-- Auditoría -->
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="bi bi-clock-history"></i> Auditoría</h6>
                                        <p class="mb-1">
                                            <strong>Creado por:</strong> {{ $tarea->createdBy->name ?? 'N/A' }}<br>
                                            <small class="text-muted">{{ $tarea->created_at->format('d/m/Y H:i') }}</small>
                                        </p>
                                        @if($tarea->updated_at != $tarea->created_at)
                                        <p class="mb-1">
                                            <strong>Última modificación:</strong> {{ $tarea->updatedBy->name ?? 'N/A' }}<br>
                                            <small class="text-muted">{{ $tarea->updated_at->format('d/m/Y H:i') }}</small>
                                        </p>
                                        @endif
                                        @if($tarea->deleted_at)
                                        <p class="mb-0">
                                            <strong>Eliminado por:</strong> {{ $tarea->deletedBy->name ?? 'N/A' }}<br>
                                            <small class="text-muted">{{ $tarea->deleted_at->format('d/m/Y H:i') }}</small>
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('paa.tareas.show', [$paa, $tarea]) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                            @if($tarea->estado_tarea != 'realizada')
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
    const descripcion = document.getElementById('descripcion_tarea');
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
    const fechaInicio = document.getElementById('fecha_inicio_planeada');
    const fechaFin = document.getElementById('fecha_fin_planeada');

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

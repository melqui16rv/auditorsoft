@extends('layouts.app')

@section('title', 'Crear Tarea - ' . $paa->codigo)

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
                    <li class="breadcrumb-item active">Nueva Tarea</li>
                </ol>
            </nav>

            <!-- Card Principal -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-plus-circle"></i>
                        Nueva Tarea del PAA
                    </h4>
                    <p class="mb-0 mt-1">
                        <small>{{ $paa->codigo }} - {{ $paa->nombre_entidad }}</small>
                    </p>
                </div>

                <div class="card-body">
                    <!-- Mensaje de ayuda -->
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle"></i>
                        Complete la información de la tarea. Asegúrese de asignarla al rol OCI correcto según el Decreto 648/2017.
                    </div>

                    <!-- Formulario -->
                    <form action="{{ route('paa.tareas.store', $paa) }}" method="POST" id="formCreateTarea">
                        @csrf

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
                                            required>
                                        <option value="">Seleccione el rol OCI...</option>
                                        <option value="fomento_cultura" {{ old('rol_oci') == 'fomento_cultura' ? 'selected' : '' }}>
                                            Fomento de la Cultura del Control
                                        </option>
                                        <option value="apoyo_fortalecimiento" {{ old('rol_oci') == 'apoyo_fortalecimiento' ? 'selected' : '' }}>
                                            Apoyo al Fortalecimiento
                                        </option>
                                        <option value="investigaciones" {{ old('rol_oci') == 'investigaciones' ? 'selected' : '' }}>
                                            Investigaciones
                                        </option>
                                        <option value="evaluacion_control" {{ old('rol_oci') == 'evaluacion_control' ? 'selected' : '' }}>
                                            Evaluación de Control
                                        </option>
                                        <option value="evaluacion_gestion" {{ old('rol_oci') == 'evaluacion_gestion' ? 'selected' : '' }}>
                                            Evaluación de Gestión
                                        </option>
                                    </select>
                                    @error('rol_oci')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Decreto 648/2017 - Cinco roles funcionales de la OCI</small>
                                </div>

                                <!-- Nombre de la Tarea -->
                                <div class="mb-3">
                                    <label for="nombre_tarea" class="form-label">
                                        Nombre de la Tarea <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           name="nombre_tarea" 
                                           id="nombre_tarea" 
                                           class="form-control @error('nombre_tarea') is-invalid @enderror"
                                           value="{{ old('nombre_tarea') }}" 
                                           required
                                           minlength="5"
                                           maxlength="255"
                                           placeholder="Ej: Auditoría al proceso de compras...">
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Título corto y descriptivo (5-255 caracteres)</small>
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
                                              required
                                              minlength="10"
                                              maxlength="1000"
                                              placeholder="Describa detalladamente la tarea a realizar...">{{ old('descripcion') }}</textarea>
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <span id="charCount">0</span>/1000 caracteres (mínimo 10)
                                    </small>
                                </div>

                                <!-- Fecha Inicio Planeada -->
                                <div class="mb-3">
                                    <label for="fecha_inicio" class="form-label">
                                        Fecha de Inicio <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           name="fecha_inicio" 
                                           id="fecha_inicio" 
                                           class="form-control @error('fecha_inicio') is-invalid @enderror"
                                           value="{{ old('fecha_inicio', date('Y-m-d')) }}" 
                                           min="{{ date('Y-m-d') }}"
                                           required>
                                    @error('fecha_inicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Fecha Fin Planeada -->
                                <div class="mb-3">
                                    <label for="fecha_fin" class="form-label">
                                        Fecha de Fin <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           name="fecha_fin" 
                                           id="fecha_fin" 
                                           class="form-control @error('fecha_fin') is-invalid @enderror"
                                           value="{{ old('fecha_fin') }}" 
                                           min="{{ date('Y-m-d') }}"
                                           required>
                                    @error('fecha_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Debe ser posterior a la fecha de inicio</small>
                                </div>
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
                                            required>
                                        <option value="">Seleccione el responsable...</option>
                                        @foreach($responsables as $responsable)
                                            <option value="{{ $responsable->id }}" {{ old('responsable_id') == $responsable->id ? 'selected' : '' }}>
                                                {{ $responsable->name }} - {{ ucfirst($responsable->role) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('responsable_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Auditor asignado a esta tarea</small>
                                </div>

                                <!-- Observaciones Iniciales -->
                                <div class="mb-3">
                                    <label for="observaciones" class="form-label">
                                        Observaciones Iniciales
                                    </label>
                                    <textarea name="observaciones" 
                                              id="observaciones" 
                                              class="form-control @error('observaciones') is-invalid @enderror"
                                              rows="4"
                                              maxlength="2000"
                                              placeholder="Observaciones, consideraciones o instrucciones especiales...">{{ old('observaciones') }}</textarea>
                                    @error('observaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <span id="obsCharCount">0</span>/2000 caracteres
                                    </small>
                                </div>

                                <!-- Información del PAA -->
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="bi bi-info-circle"></i> Información del PAA</h6>
                                        <p class="mb-1"><strong>Código:</strong> {{ $paa->codigo }}</p>
                                        <p class="mb-1"><strong>Vigencia:</strong> <span class="badge bg-secondary">{{ $paa->vigencia }}</span></p>
                                        <p class="mb-1"><strong>Estado:</strong> {!! $paa->estado_badge !!}</p>
                                        <p class="mb-0"><strong>Jefe OCI:</strong> {{ $paa->elaboradoPor->name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información Adicional -->
                        <div class="card mt-3 bg-light">
                            <div class="card-body">
                                <h6 class="card-title"><i class="bi bi-lightbulb"></i> Estados de la Tarea</h6>
                                <p class="mb-2">La tarea se creará en estado <strong>"Pendiente"</strong> con evaluación <strong>"Pendiente"</strong>.</p>
                                <p class="mb-0">
                                    <strong>Flujo de estados:</strong> 
                                    <span class="badge bg-secondary">Pendiente</span> → 
                                    <span class="badge bg-warning">En Proceso</span> → 
                                    <span class="badge bg-success">Realizado</span>
                                </p>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('paa.show', $paa) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Crear Tarea
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
    const descripcion = document.getElementById('descripcion');
    const charCount = document.getElementById('charCount');
    
    descripcion.addEventListener('input', function() {
        charCount.textContent = this.value.length;
        if (this.value.length < 10) {
            charCount.classList.add('text-danger');
        } else {
            charCount.classList.remove('text-danger');
        }
    });

    // Validación de fechas
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');

    fechaInicio.addEventListener('change', function() {
        fechaFin.min = this.value;
        if (fechaFin.value && fechaFin.value <= this.value) {
            fechaFin.value = '';
            alert('La fecha de fin debe ser posterior a la fecha de inicio.');
        }
    });

    fechaFin.addEventListener('change', function() {
        if (fechaInicio.value && this.value <= fechaInicio.value) {
            this.value = '';
            alert('La fecha de fin debe ser posterior a la fecha de inicio.');
        }
    });

    // Validación del formulario
    document.getElementById('formCreateTarea').addEventListener('submit', function(e) {
        const rolOci = document.getElementById('rol_oci').value;
        const nombre = document.getElementById('nombre').value;
        const desc = document.getElementById('descripcion').value;
        const responsable = document.getElementById('auditor_responsable_id').value;
        const inicio = document.getElementById('fecha_inicio').value;
        const fin = document.getElementById('fecha_fin').value;

        if (!rolOci || !nombre || !desc || !responsable || !inicio || !fin) {
            e.preventDefault();
            alert('Por favor complete todos los campos obligatorios marcados con *');
            return false;
        }

        if (nombre.length < 5) {
            e.preventDefault();
            alert('El nombre de la tarea debe tener al menos 5 caracteres.');
            return false;
        }

        if (desc.length < 10) {
            e.preventDefault();
            alert('La descripción debe tener al menos 10 caracteres.');
            return false;
        }

        if (fin <= inicio) {
            e.preventDefault();
            alert('La fecha de fin debe ser posterior a la fecha de inicio.');
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

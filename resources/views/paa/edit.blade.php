@extends('layouts.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('page-title', 'Editar Plan Anual de Auditoría')

@section('title', 'Editar Plan Anual de Auditoría')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('paa.index') }}">PAA</a></li>
                    <li class="breadcrumb-item active">Editar PAA</li>
                </ol>
            </nav>

            <!-- Card Principal -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square"></i>
                        Editar Plan Anual de Auditoría
                    </h4>
                    <p class="mb-0 mt-1">
                        <small>Formato FR-GCE-001 | Código: <strong>{{ $paa->codigo }}</strong></small>
                    </p>
                </div>

                <div class="card-body">
                    <!-- Estado actual -->
                    <div class="alert alert-{{ $paa->estado == 'borrador' ? 'warning' : 'info' }}" role="alert">
                        <i class="bi bi-info-circle"></i>
                        <strong>Estado actual:</strong> {!! $paa->estado_badge !!}
                        @if($paa->estado == 'en_ejecucion')
                            <br><small>El PAA está en ejecución. Los cambios afectarán las tareas y seguimientos asociados.</small>
                        @endif
                    </div>

                    <!-- Formulario -->
                    <form action="{{ route('paa.update', $paa) }}" method="POST" enctype="multipart/form-data" id="formEditPAA">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Columna Izquierda -->
                            <div class="col-md-6">
                                <!-- Código de Registro (no editable) -->
                                <div class="mb-3">
                                    <label class="form-label">Código de Registro</label>
                                    <input type="text" 
                                           class="form-control bg-light" 
                                           value="{{ $paa->codigo }}" 
                                           disabled>
                                    <small class="form-text text-muted">El código no puede modificarse</small>
                                </div>

                                <!-- Vigencia -->
                                <div class="mb-3">
                                    <label for="vigencia" class="form-label">
                                        Vigencia <span class="text-danger">*</span>
                                    </label>
                                    <select name="vigencia" id="vigencia" 
                                            class="form-select @error('vigencia') is-invalid @enderror" required>
                                        <option value="">Seleccione la vigencia...</option>
                                        @for ($year = 2020; $year <= 2050; $year++)
                                            <option value="{{ $year }}" {{ old('vigencia', $paa->vigencia) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('vigencia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Fecha de Elaboración -->
                                <div class="mb-3">
                                    <label for="fecha_elaboracion" class="form-label">
                                        Fecha de Elaboración <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           name="fecha_elaboracion" 
                                           id="fecha_elaboracion" 
                                           class="form-control @error('fecha_elaboracion') is-invalid @enderror"
                                           value="{{ old('fecha_elaboracion', $paa->fecha_elaboracion->format('Y-m-d')) }}" 
                                           required>
                                    @error('fecha_elaboracion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Nombre de la Entidad -->
                                <div class="mb-3">
                                    <label for="nombre_entidad" class="form-label">
                                        Nombre de la Entidad <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           name="nombre_entidad" 
                                           id="nombre_entidad" 
                                           class="form-control @error('nombre_entidad') is-invalid @enderror"
                                           value="{{ old('nombre_entidad', $paa->nombre_entidad) }}" 
                                           required>
                                    @error('nombre_entidad')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Municipio -->
                                <div class="mb-3">
                                    <label for="municipio_id" class="form-label">
                                        Municipio
                                    </label>
                                    <select name="municipio_id" 
                                            id="municipio_id" 
                                            class="form-select @error('municipio_id') is-invalid @enderror">
                                        <option value="">Seleccione un municipio...</option>
                                        @foreach($municipios as $municipio)
                                            <option value="{{ $municipio->id }}" 
                                                {{ old('municipio_id', $paa->municipio_id) == $municipio->id ? 'selected' : '' }}>
                                                {{ $municipio->nombre }} - {{ $municipio->departamento }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('municipio_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Jefe OCI -->
                                <div class="mb-3">
                                    <label class="form-label">Jefe de Control Interno</label>
                                    <input type="text" 
                                           class="form-control bg-light" 
                                           value="{{ $paa->elaboradoPor->name }}" 
                                           disabled>
                                    <small class="form-text text-muted">Responsable asignado al crear el PAA</small>
                                </div>
                            </div>

                            <!-- Columna Derecha -->
                            <div class="col-md-6">
                                <!-- Logo Institucional Actual -->
                                @if($paa->imagen_institucional)
                                <div class="mb-3">
                                    <label class="form-label">Logo Actual:</label>
                                    <div class="border rounded p-3 text-center bg-light">
                                        <img src="{{ asset('storage/' . $paa->imagen_institucional) }}" 
                                             alt="Logo Institucional" 
                                             class="img-fluid" 
                                             style="max-height: 150px;">
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="eliminar_imagen_actual" 
                                               name="eliminar_imagen_actual" 
                                               value="1">
                                        <label class="form-check-label" for="eliminar_imagen_actual">
                                            Eliminar logo actual
                                        </label>
                                    </div>
                                </div>
                                @endif

                                <!-- Nuevo Logo Institucional -->
                                <div class="mb-3">
                                    <label for="imagen_institucional" class="form-label">
                                        {{ $paa->imagen_institucional ? 'Reemplazar Logo' : 'Logo Institucional' }}
                                    </label>
                                    <input type="file" 
                                           name="imagen_institucional" 
                                           id="imagen_institucional" 
                                           class="form-control @error('imagen_institucional') is-invalid @enderror"
                                           accept="image/*">
                                    @error('imagen_institucional')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Máximo 2MB. Formatos: JPG, PNG, SVG</small>
                                </div>

                                <!-- Preview de nueva imagen -->
                                <div class="mb-3" id="imagePreviewContainer" style="display: none;">
                                    <label class="form-label">Vista Previa del Nuevo Logo:</label>
                                    <div class="border rounded p-3 text-center bg-light">
                                        <img id="imagePreview" src="" alt="Preview" class="img-fluid" style="max-height: 150px;">
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger mt-2" id="removeImage">
                                        <i class="bi bi-trash"></i> Cancelar nuevo logo
                                    </button>
                                </div>

                                <!-- Observaciones -->
                                <div class="mb-3">
                                    <label for="observaciones" class="form-label">
                                        Observaciones Generales
                                    </label>
                                    <textarea name="observaciones" 
                                              id="observaciones" 
                                              class="form-control @error('observaciones') is-invalid @enderror"
                                              rows="4">{{ old('observaciones', $paa->observaciones) }}</textarea>
                                    @error('observaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Estado (solo si está en borrador o en ejecución) -->
                                @if(in_array($paa->estado, ['borrador', 'en_ejecucion']))
                                <div class="mb-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror">
                                        <option value="borrador" {{ old('estado', $paa->estado) == 'borrador' ? 'selected' : '' }}>
                                            Borrador
                                        </option>
                                        <option value="en_ejecucion" {{ old('estado', $paa->estado) == 'en_ejecucion' ? 'selected' : '' }}>
                                            En Ejecución
                                        </option>
                                    </select>
                                    @error('estado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Use las acciones de "Aprobar" o "Finalizar" para cambios de estado formales
                                    </small>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Información de Auditoría -->
                        <div class="card mt-4 bg-light">
                            <div class="card-body">
                                <h6 class="card-title"><i class="bi bi-clock-history"></i> Información de Auditoría</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="mb-1"><strong>Creado por:</strong> {{ $paa->createdBy->name ?? 'N/A' }}</p>
                                        <p class="mb-1"><small class="text-muted">{{ $paa->created_at->format('d/m/Y H:i') }}</small></p>
                                    </div>
                                    <div class="col-md-4">
                                        @if($paa->updated_by)
                                        <p class="mb-1"><strong>Última modificación:</strong> {{ $paa->updatedBy->name ?? 'N/A' }}</p>
                                        <p class="mb-1"><small class="text-muted">{{ $paa->updated_at->format('d/m/Y H:i') }}</small></p>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        @if($paa->aprobado_por)
                                        <p class="mb-1"><strong>Aprobado por:</strong> {{ $paa->aprobadoPor->name ?? 'N/A' }}</p>
                                        <p class="mb-1"><small class="text-muted">{{ $paa->fecha_aprobacion ? $paa->fecha_aprobacion->format('d/m/Y') : 'N/A' }}</small></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('paa.show', $paa) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                            <div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-save"></i> Actualizar PAA
                                </button>
                            </div>
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
    const imageInput = document.getElementById('imagen_institucional');
    const imagePreview = document.getElementById('imagePreview');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const removeImageBtn = document.getElementById('removeImage');
    const eliminarImagenActual = document.getElementById('eliminar_imagen_actual');

    // Preview de nueva imagen
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validar tamaño (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('El archivo es demasiado grande. Máximo 2MB.');
                imageInput.value = '';
                return;
            }

            // Validar tipo
            if (!file.type.startsWith('image/')) {
                alert('El archivo debe ser una imagen.');
                imageInput.value = '';
                return;
            }

            // Mostrar preview
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreviewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);

            // Desmarcar eliminar imagen actual si selecciona nueva
            if (eliminarImagenActual) {
                eliminarImagenActual.checked = false;
            }
        }
    });

    // Eliminar preview de nueva imagen
    removeImageBtn.addEventListener('click', function() {
        imageInput.value = '';
        imagePreview.src = '';
        imagePreviewContainer.style.display = 'none';
    });

    // Validación: no puede eliminar imagen actual y subir nueva al mismo tiempo
    if (eliminarImagenActual) {
        eliminarImagenActual.addEventListener('change', function() {
            if (this.checked && imageInput.files.length > 0) {
                alert('No puede eliminar y subir una imagen al mismo tiempo. Se cancelará la nueva imagen.');
                imageInput.value = '';
                imagePreview.src = '';
                imagePreviewContainer.style.display = 'none';
            }
        });
    }

    // Validación del formulario
    document.getElementById('formEditPAA').addEventListener('submit', function(e) {
        const vigencia = document.getElementById('vigencia').value;
        const fechaElaboracion = document.getElementById('fecha_elaboracion').value;
        const nombreEntidad = document.getElementById('nombre_entidad').value;

        if (!vigencia || !fechaElaboracion || !nombreEntidad) {
            e.preventDefault();
            alert('Por favor complete todos los campos obligatorios marcados con *');
            return false;
        }
    });
});
</script>
@endpush
@endsection

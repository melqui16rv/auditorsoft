@extends('layouts.app')

@section('title', 'Crear Plan Anual de Auditoría')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('page-title', 'Crear Plan Anual de Auditoría')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('paa.index') }}">PAA</a></li>
                    <li class="breadcrumb-item active">Crear PAA</li>
                </ol>
            </nav>

            <!-- Card Principal -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-file-earmark-plus"></i>
                        Crear Plan Anual de Auditoría
                    </h4>
                    <p class="mb-0 mt-1"><small>Formato FR-GCE-001</small></p>
                </div>

                <div class="card-body">
                    <!-- Mensaje de ayuda -->
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle"></i>
                        Complete la información del Plan Anual de Auditoría. El código se generará automáticamente.
                        El PAA se creará en estado <strong>Borrador</strong> y podrá ser aprobado posteriormente.
                    </div>

                    <!-- Formulario -->
                    <form action="{{ route('paa.store') }}" method="POST" enctype="multipart/form-data" id="formCreatePAA">
                        @csrf

                        <div class="row">
                            <!-- Columna Izquierda -->
                            <div class="col-md-6">
                                <!-- Vigencia -->
                                <div class="mb-3">
                                    <label for="vigencia" class="form-label">
                                        Vigencia <span class="text-danger">*</span>
                                    </label>
                                    <select name="vigencia" id="vigencia" 
                                            class="form-select @error('vigencia') is-invalid @enderror" required>
                                        <option value="">Seleccione la vigencia...</option>
                                        @for ($year = 2020; $year <= 2050; $year++)
                                            <option value="{{ $year }}" {{ old('vigencia', $vigenciaActual) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('vigencia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Año de aplicación del PAA</small>
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
                                           value="{{ old('fecha_elaboracion', date('Y-m-d')) }}" 
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
                                           value="{{ old('nombre_entidad') }}" 
                                           placeholder="Ejemplo: Alcaldía Municipal de..."
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
                                            <option value="{{ $municipio->id }}" {{ old('municipio_id') == $municipio->id ? 'selected' : '' }}>
                                                {{ $municipio->nombre }} - {{ $municipio->departamento }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('municipio_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Ciudad o municipio donde se elabora el PAA</small>
                                </div>

                                <!-- Jefe OCI (pre-seleccionado) -->
                                <div class="mb-3">
                                    <label for="jefe_oci_id" class="form-label">
                                        Jefe de Control Interno <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           value="{{ auth()->user()->name }}" 
                                           disabled>
                                    <input type="hidden" name="jefe_oci_id" value="{{ auth()->id() }}">
                                    <small class="form-text text-muted">Responsable del PAA (usuario autenticado)</small>
                                </div>
                            </div>

                            <!-- Columna Derecha -->
                            <div class="col-md-6">
                                <!-- Logo Institucional -->
                                <div class="mb-3">
                                    <label for="imagen_institucional" class="form-label">
                                        Logo Institucional
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

                                <!-- Preview de la imagen -->
                                <div class="mb-3" id="imagePreviewContainer" style="display: none;">
                                    <label class="form-label">Vista Previa:</label>
                                    <div class="border rounded p-3 text-center bg-light">
                                        <img id="imagePreview" src="" alt="Preview" class="img-fluid" style="max-height: 200px;">
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger mt-2" id="removeImage">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </div>

                                <!-- Código Sugerido (solo informativo) -->
                                <div class="mb-3">
                                    <label class="form-label">Código de Registro (auto-generado)</label>
                                    <input type="text" 
                                           class="form-control bg-light" 
                                           value="{{ $codigoSugerido }}" 
                                           disabled>
                                    <small class="form-text text-muted">El código se asignará automáticamente al guardar</small>
                                </div>

                                <!-- Observaciones -->
                                <div class="mb-3">
                                    <label for="observaciones" class="form-label">
                                        Observaciones Generales
                                    </label>
                                    <textarea name="observaciones" 
                                              id="observaciones" 
                                              class="form-control @error('observaciones') is-invalid @enderror"
                                              rows="4"
                                              placeholder="Ingrese observaciones iniciales o contexto del PAA...">{{ old('observaciones') }}</textarea>
                                    @error('observaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Metadatos FR-GCE-001 (colapsable) -->
                        <div class="card mt-4">
                            <div class="card-header bg-secondary text-white">
                                <button class="btn btn-link text-white text-decoration-none w-100 text-start" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#metadatosCollapse">
                                    <i class="bi bi-info-circle"></i>
                                    Metadatos FR-GCE-001 (Decreto 648/2017)
                                    <i class="bi bi-chevron-down float-end"></i>
                                </button>
                            </div>
                            <div class="collapse" id="metadatosCollapse">
                                <div class="card-body bg-light">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p class="mb-2"><strong>Versión:</strong> V1</p>
                                            <p class="mb-2"><strong>Protección:</strong> Controlado</p>
                                            <p class="mb-2"><strong>Medio:</strong> Magnético</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-2"><strong>Ubicación:</strong> PC Control Interno</p>
                                            <p class="mb-2"><strong>Recuperación:</strong> Por fecha</p>
                                            <p class="mb-2"><strong>Responsable:</strong> Jefe OCI</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-2"><strong>Permanencia:</strong> Permanente</p>
                                            <p class="mb-2"><strong>Disposición:</strong> Backups</p>
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-shield-check"></i>
                                        Estos metadatos se registrarán automáticamente según el Decreto 648/2017
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('paa.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Guardar PAA
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
    const imageInput = document.getElementById('imagen_institucional');
    const imagePreview = document.getElementById('imagePreview');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const removeImageBtn = document.getElementById('removeImage');

    // Preview de imagen
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
        }
    });

    // Eliminar imagen
    removeImageBtn.addEventListener('click', function() {
        imageInput.value = '';
        imagePreview.src = '';
        imagePreviewContainer.style.display = 'none';
    });

    // Validación del formulario antes de enviar
    document.getElementById('formCreatePAA').addEventListener('submit', function(e) {
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

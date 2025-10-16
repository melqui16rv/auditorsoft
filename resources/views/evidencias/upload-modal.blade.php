<!-- Modal para subir evidencias -->
<div class="modal fade" id="uploadEvidenciaModal" tabindex="-1" aria-labelledby="uploadEvidenciaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="uploadEvidenciaModalLabel">
                    <i class="fas fa-cloud-upload-alt me-2"></i>Subir Evidencia
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadEvidenciaForm" method="POST" action="{{ route('evidencias.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="evidenciable_type" id="evidenciable_type" value="">
                <input type="hidden" name="evidenciable_id" id="evidenciable_id" value="">
                
                <div class="modal-body">
                    <!-- Información del elemento asociado -->
                    <div class="alert alert-info d-flex align-items-center mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <div>
                            <strong>Asociando evidencia a:</strong><br>
                            <span id="evidenciable_info">-</span>
                        </div>
                    </div>

                    <!-- Selector de archivo -->
                    <div class="mb-3">
                        <label for="archivo" class="form-label">
                            Archivo <span class="text-danger">*</span>
                        </label>
                        <input type="file" 
                               class="form-control @error('archivo') is-invalid @enderror" 
                               id="archivo" 
                               name="archivo" 
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                               required>
                        <div class="form-text">
                            Formatos permitidos: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG. Tamaño máximo: 10 MB
                        </div>
                        @error('archivo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="archivo-error" class="invalid-feedback" style="display:none;"></div>
                    </div>

                    <!-- Vista previa del archivo -->
                    <div id="file-preview" class="mb-3" style="display:none;">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div id="file-icon" class="me-3 fs-1"></div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1" id="file-name">-</h6>
                                        <small class="text-muted">
                                            <span id="file-type">-</span> | 
                                            <span id="file-size">-</span>
                                        </small>
                                    </div>
                                </div>
                                <!-- Vista previa de imagen -->
                                <div id="image-preview-container" class="mt-3" style="display:none;">
                                    <img id="image-preview" src="" alt="Vista previa" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                  id="descripcion" 
                                  name="descripcion" 
                                  rows="3" 
                                  maxlength="500"
                                  placeholder="Descripción breve del documento (opcional)">{{ old('descripcion') }}</textarea>
                        <div class="form-text">
                            <span id="char-count">0</span>/500 caracteres
                        </div>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Barra de progreso -->
                    <div id="upload-progress" class="mb-3" style="display:none;">
                        <div class="progress">
                            <div id="upload-progress-bar" 
                                 class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" 
                                 style="width: 0%;">
                                <span id="upload-percent">0%</span>
                            </div>
                        </div>
                        <small class="text-muted">Subiendo archivo...</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="upload-btn">
                        <i class="fas fa-upload me-1"></i>Subir Evidencia
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadModal = document.getElementById('uploadEvidenciaModal');
    const uploadForm = document.getElementById('uploadEvidenciaForm');
    const archivoInput = document.getElementById('archivo');
    const descripcionTextarea = document.getElementById('descripcion');
    const filePreview = document.getElementById('file-preview');
    const uploadProgress = document.getElementById('upload-progress');
    const uploadBtn = document.getElementById('upload-btn');
    const charCount = document.getElementById('char-count');

    // Función para abrir el modal (se llamará desde las vistas padre)
    window.openUploadEvidenciaModal = function(evidenciableType, evidenciableId, evidenciableInfo) {
        document.getElementById('evidenciable_type').value = evidenciableType;
        document.getElementById('evidenciable_id').value = evidenciableId;
        document.getElementById('evidenciable_info').textContent = evidenciableInfo;
        
        // Resetear el formulario
        uploadForm.reset();
        filePreview.style.display = 'none';
        uploadProgress.style.display = 'none';
        document.getElementById('archivo-error').style.display = 'none';
        archivoInput.classList.remove('is-invalid');
        uploadBtn.disabled = false;
        
        // Abrir el modal
        const modal = new bootstrap.Modal(uploadModal);
        modal.show();
    };

    // Validación y vista previa del archivo
    archivoInput.addEventListener('change', function() {
        const file = this.files[0];
        const errorDiv = document.getElementById('archivo-error');
        
        if (!file) {
            filePreview.style.display = 'none';
            return;
        }

        // Validar tamaño (10 MB = 10485760 bytes)
        const maxSize = 10485760;
        if (file.size > maxSize) {
            errorDiv.textContent = 'El archivo no debe superar los 10 MB';
            errorDiv.style.display = 'block';
            this.classList.add('is-invalid');
            filePreview.style.display = 'none';
            return;
        }

        // Validar extensión
        const allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];
        const extension = file.name.split('.').pop().toLowerCase();
        
        if (!allowedExtensions.includes(extension)) {
            errorDiv.textContent = 'Formato de archivo no permitido';
            errorDiv.style.display = 'block';
            this.classList.add('is-invalid');
            filePreview.style.display = 'none';
            return;
        }

        // Archivo válido
        errorDiv.style.display = 'none';
        this.classList.remove('is-invalid');

        // Mostrar información del archivo
        document.getElementById('file-name').textContent = file.name;
        document.getElementById('file-type').textContent = extension.toUpperCase();
        document.getElementById('file-size').textContent = formatFileSize(file.size);

        // Icono según el tipo
        const iconContainer = document.getElementById('file-icon');
        iconContainer.className = 'me-3 fs-1';
        
        if (extension === 'pdf') {
            iconContainer.innerHTML = '<i class="fas fa-file-pdf text-danger"></i>';
        } else if (['doc', 'docx'].includes(extension)) {
            iconContainer.innerHTML = '<i class="fas fa-file-word text-primary"></i>';
        } else if (['xls', 'xlsx'].includes(extension)) {
            iconContainer.innerHTML = '<i class="fas fa-file-excel text-success"></i>';
        } else if (['jpg', 'jpeg', 'png'].includes(extension)) {
            iconContainer.innerHTML = '<i class="fas fa-file-image text-info"></i>';
            
            // Vista previa de imagen
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('image-preview-container').style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            iconContainer.innerHTML = '<i class="fas fa-file text-secondary"></i>';
        }

        filePreview.style.display = 'block';
    });

    // Contador de caracteres para descripción
    descripcionTextarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });

    // Manejo de envío del formulario con AJAX (opcional)
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Deshabilitar botón y mostrar progreso
        uploadBtn.disabled = true;
        uploadProgress.style.display = 'block';

        // Simular progreso (en producción, usar XMLHttpRequest para progreso real)
        let progress = 0;
        const progressInterval = setInterval(function() {
            progress += 10;
            document.getElementById('upload-progress-bar').style.width = progress + '%';
            document.getElementById('upload-percent').textContent = progress + '%';
            
            if (progress >= 90) {
                clearInterval(progressInterval);
            }
        }, 100);

        // Enviar con fetch
        fetch(uploadForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            clearInterval(progressInterval);
            document.getElementById('upload-progress-bar').style.width = '100%';
            document.getElementById('upload-percent').textContent = '100%';
            
            if (data.success) {
                // Recargar la página para mostrar la nueva evidencia
                window.location.reload();
            } else {
                alert('Error al subir el archivo: ' + (data.message || 'Error desconocido'));
                uploadBtn.disabled = false;
                uploadProgress.style.display = 'none';
            }
        })
        .catch(error => {
            clearInterval(progressInterval);
            console.error('Error:', error);
            alert('Error al subir el archivo');
            uploadBtn.disabled = false;
            uploadProgress.style.display = 'none';
        });
    });

    // Función auxiliar para formatear el tamaño del archivo
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }
});
</script>
@endpush

@extends('layouts.app')

@section('title', 'Crear Usuario - AuditorSoft')
@section('page-title', 'Crear Usuario')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/users-crud.css') }}">
@endsection

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="fas fa-user-plus me-3 text-primary"></i>
                Crear Nuevo Usuario
            </h1>
            <p class="page-subtitle">Añade un nuevo usuario al sistema AuditorSoft</p>
        </div>
        <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Volver al Listado
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-user-plus me-2 text-primary"></i>
                    Información del Usuario
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.users.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    
                    <div class="row g-4">
                        <!-- Información Personal -->
                        <div class="col-12">
                            <h6 class="text-primary fw-semibold mb-3">
                                <i class="fas fa-user me-2"></i>
                                Información Personal
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <label for="name" class="form-label">
                                Nombre Completo <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">
                                Correo Electrónico <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Configuración de Acceso -->
                        <div class="col-12">
                            <hr class="my-4">
                            <h6 class="text-primary fw-semibold mb-3">
                                <i class="fas fa-key me-2"></i>
                                Configuración de Acceso
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label">
                                Contraseña <span class="text-danger">*</span>
                            </label>
                            <div class="password-input-container">
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required>
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="password-icon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Mínimo 8 caracteres</small>
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">
                                Confirmar Contraseña <span class="text-danger">*</span>
                            </label>
                            <div class="password-input-container">
                                <input type="password" 
                                       class="form-control @error('password_confirmation') is-invalid @enderror" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required>
                                <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye" id="password_confirmation-icon"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Configuración de Rol -->
                        <div class="col-12">
                            <hr class="my-4">
                            <h6 class="text-primary fw-semibold mb-3">
                                <i class="fas fa-user-tag me-2"></i>
                                Configuración de Rol y Estado
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <label for="role" class="form-label">
                                Rol del Usuario <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('role') is-invalid @enderror" 
                                    id="role" 
                                    name="role" 
                                    required>
                                <option value="">Selecciona un rol</option>
                                @foreach($roles as $value => $label)
                                    <option value="{{ $value }}" {{ old('role') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="is_active" class="form-label">Estado de la Cuenta</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <span class="fw-semibold">Usuario Activo</span>
                                    <br>
                                    <small class="text-muted">El usuario podrá acceder al sistema</small>
                                </label>
                            </div>
                        </div>

                        <!-- Información sobre Roles -->
                        <div class="col-12">
                            <div class="alert alert-info border-0" role="alert">
                                <h6 class="alert-heading">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Información sobre los Roles
                                </h6>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <strong>Auditado:</strong> Usuario que será auditado
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Auditor:</strong> Realiza auditorías
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Jefe Auditor:</strong> Supervisa auditores
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Super Administrador:</strong> Control total del sistema
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="col-12">
                            <hr class="my-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Crear Usuario
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Validación de confirmación de contraseña
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmation = this.value;
    
    if (confirmation && password !== confirmation) {
        this.setCustomValidity('Las contraseñas no coinciden');
        this.classList.add('is-invalid');
    } else {
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
    }
});

// Generar contraseña automática
function generatePassword() {
    const length = 12;
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
    let password = "";
    for (let i = 0; i < length; i++) {
        password += charset.charAt(Math.floor(Math.random() * charset.length));
    }
    
    document.getElementById('password').value = password;
    document.getElementById('password_confirmation').value = password;
    
    // Mostrar contraseña temporalmente
    document.getElementById('password').type = 'text';
    document.getElementById('password_confirmation').type = 'text';
    document.getElementById('password-icon').classList.remove('fa-eye');
    document.getElementById('password-icon').classList.add('fa-eye-slash');
    document.getElementById('password_confirmation-icon').classList.remove('fa-eye');
    document.getElementById('password_confirmation-icon').classList.add('fa-eye-slash');
}
</script>

<style>
.password-input-container {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    z-index: 10;
}

.password-toggle:hover {
    color: #495057;
}

.form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.alert-info {
    background-color: rgba(13, 202, 240, 0.1);
    border-left: 4px solid #0dcaf0;
}
</style>
@endsection

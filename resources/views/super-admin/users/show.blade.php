@extends('layouts.app')

@section('title', 'Detalles del Usuario - AuditorSoft')
@section('page-title', 'Detalles del Usuario')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/users-crud.css') }}">
@endsection

@section('sidebar')
<div class="sidebar-nav-item">
    <a class="sidebar-nav-link" href="{{ route('super-admin.dashboard') }}">
        <i class="fas fa-tachometer-alt"></i>
        <span>Dashboard</span>
    </a>
</div>
<div class="sidebar-nav-item">
    <a class="sidebar-nav-link active" href="{{ route('super-admin.users.index') }}">
        <i class="fas fa-users"></i>
        <span>Gestión de Usuarios</span>
    </a>
</div>
<div class="sidebar-nav-item">
    <a class="sidebar-nav-link" href="#">
        <i class="fas fa-building"></i>
        <span>Gestión de Empresas</span>
    </a>
</div>
<div class="sidebar-nav-item">
    <a class="sidebar-nav-link" href="#">
        <i class="fas fa-cogs"></i>
        <span>Configuración del Sistema</span>
    </a>
</div>
<div class="sidebar-nav-item">
    <a class="sidebar-nav-link" href="#">
        <i class="fas fa-chart-line"></i>
        <span>Análisis Global</span>
    </a>
</div>
<div class="sidebar-nav-item">
    <a class="sidebar-nav-link" href="#">
        <i class="fas fa-shield-alt"></i>
        <span>Seguridad y Auditoría</span>
    </a>
</div>
<div class="sidebar-nav-item">
    <a class="sidebar-nav-link" href="#">
        <i class="fas fa-database"></i>
        <span>Base de Datos</span>
    </a>
</div>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="fas fa-user me-3 text-primary"></i>
                Detalles del Usuario
            </h1>
            <p class="page-subtitle">Información completa de {{ $user->name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('super-admin.users.edit', $user) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>
                Editar Usuario
            </a>
            <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Volver al Listado
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Información Principal -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-user me-2 text-primary"></i>
                    Información Personal
                    @if($user->id === auth()->id())
                        <span class="badge bg-info ms-2">
                            <i class="fas fa-star me-1"></i>Tu cuenta
                        </span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">Nombre Completo</label>
                        <div class="form-control-plaintext h5 mb-0">{{ $user->name }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">Correo Electrónico</label>
                        <div class="form-control-plaintext">
                            <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                {{ $user->email }}
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">Rol</label>
                        <div class="form-control-plaintext">
                            @php
                                $roleColors = [
                                    'auditado' => 'bg-info',
                                    'auditor' => 'bg-success',
                                    'jefe_auditor' => 'bg-warning',
                                    'super_administrador' => 'bg-danger'
                                ];
                            @endphp
                            <span class="badge {{ $roleColors[$user->role] ?? 'bg-secondary' }} role-badge">
                                {{ $roles[$user->role] ?? $user->role }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">Estado</label>
                        <div class="form-control-plaintext">
                            @if($user->is_active)
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>Activo
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="fas fa-times-circle me-1"></i>Inactivo
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cambio de Contraseña -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-key me-2 text-warning"></i>
                    Gestión de Contraseña
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Actualiza la contraseña del usuario. Se enviará una notificación por email.</p>
                
                <form action="{{ route('super-admin.users.update-password', $user) }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">
                                Nueva Contraseña <span class="text-danger">*</span>
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

                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-outline-info" onclick="generatePassword()">
                                    <i class="fas fa-random me-2"></i>
                                    Generar Contraseña
                                </button>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-key me-2"></i>
                                    Actualizar Contraseña
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Panel de Acciones y Estadísticas -->
    <div class="col-lg-4">
        <!-- Acciones Rápidas -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-bolt me-2 text-info"></i>
                    Acciones Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('super-admin.users.edit', $user) }}" class="btn btn-outline-primary">
                        <i class="fas fa-edit me-2"></i>
                        Editar Información
                    </a>
                    
                    @if($user->id !== auth()->id())
                        <form action="{{ route('super-admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="btn btn-outline-{{ $user->is_active ? 'warning' : 'success' }} w-100"
                                    onclick="return confirm('¿Estás seguro de cambiar el estado de este usuario?')">
                                <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }} me-2"></i>
                                {{ $user->is_active ? 'Desactivar Usuario' : 'Activar Usuario' }}
                            </button>
                        </form>
                        
                        <hr class="my-3">
                        
                        <form action="{{ route('super-admin.users.destroy', $user) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-outline-danger w-100"
                                    onclick="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')">
                                <i class="fas fa-trash me-2"></i>
                                Eliminar Usuario
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información del Sistema -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-info-circle me-2 text-success"></i>
                    Información del Sistema
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold text-muted">ID del Usuario</label>
                        <div class="form-control-plaintext">
                            <code>#{{ $user->id }}</code>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold text-muted">Cuenta Creada</label>
                        <div class="form-control-plaintext">
                            {{ $user->created_at->format('d/m/Y H:i') }}
                            <br>
                            <small class="text-muted">({{ $user->created_at->diffForHumans() }})</small>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold text-muted">Última Actualización</label>
                        <div class="form-control-plaintext">
                            {{ $user->updated_at->format('d/m/Y H:i') }}
                            <br>
                            <small class="text-muted">({{ $user->updated_at->diffForHumans() }})</small>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold text-muted">Último Acceso</label>
                        <div class="form-control-plaintext">
                            @if($user->last_login)
                                {{ $user->last_login->format('d/m/Y H:i') }}
                                <br>
                                <small class="text-muted">({{ $user->last_login->diffForHumans() }})</small>
                            @else
                                <span class="text-muted">Nunca ha iniciado sesión</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas del Rol -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-chart-pie me-2 text-warning"></i>
                    Información del Rol
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center">
                    @php
                        $roleIcons = [
                            'auditado' => 'fas fa-user',
                            'auditor' => 'fas fa-search',
                            'jefe_auditor' => 'fas fa-user-tie',
                            'super_administrador' => 'fas fa-crown'
                        ];
                        $roleDescriptions = [
                            'auditado' => 'Usuario que será sometido a procesos de auditoría.',
                            'auditor' => 'Usuario encargado de realizar auditorías.',
                            'jefe_auditor' => 'Usuario que supervisa y coordina auditores.',
                            'super_administrador' => 'Usuario con control total del sistema.'
                        ];
                    @endphp
                    
                    <div class="mb-3">
                        <i class="{{ $roleIcons[$user->role] ?? 'fas fa-user' }} fa-3x text-primary"></i>
                    </div>
                    
                    <h6 class="fw-semibold">{{ $roles[$user->role] ?? $user->role }}</h6>
                    <p class="text-muted small mb-0">
                        {{ $roleDescriptions[$user->role] ?? 'Rol del sistema' }}
                    </p>
                </div>
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
    
    // Mostrar mensaje
    if (window.AuditorSoft && window.AuditorSoft.showNotification) {
        window.AuditorSoft.showNotification('Contraseña generada automáticamente', 'success');
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

.role-badge {
    padding: 0.375rem 0.875rem;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
</style>
@endsection

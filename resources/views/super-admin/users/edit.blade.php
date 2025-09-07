@extends('layouts.app')

@section('title', 'Editar Usuario - AuditorSoft')
@section('page-title', 'Editar Usuario')

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
                <i class="fas fa-user-edit me-3 text-primary"></i>
                Editar Usuario
            </h1>
            <p class="page-subtitle">Modifica la información de {{ $user->name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('super-admin.users.show', $user) }}" class="btn btn-outline-info">
                <i class="fas fa-eye me-2"></i>
                Ver Detalles
            </a>
            <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Volver al Listado
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-user-edit me-2 text-primary"></i>
                    Información del Usuario
                    @if($user->id === auth()->id())
                        <span class="badge bg-info ms-2">
                            <i class="fas fa-star me-1"></i>Tu cuenta
                        </span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.users.update', $user) }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')
                    
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
                                   value="{{ old('name', $user->name) }}" 
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
                                   value="{{ old('email', $user->email) }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
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
                                    <option value="{{ $value }}" {{ old('role', $user->role) === $value ? 'selected' : '' }}>
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
                            @if($user->id === auth()->id())
                                <div class="form-control-plaintext">
                                    <span class="badge bg-info">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Activo (Tu cuenta)
                                    </span>
                                    <br>
                                    <small class="text-muted">No puedes desactivar tu propia cuenta</small>
                                </div>
                                <input type="hidden" name="is_active" value="1">
                            @else
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1" 
                                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <span class="fw-semibold">Usuario Activo</span>
                                        <br>
                                        <small class="text-muted">El usuario podrá acceder al sistema</small>
                                    </label>
                                </div>
                            @endif
                        </div>

                        <!-- Información Adicional -->
                        <div class="col-12">
                            <hr class="my-4">
                            <h6 class="text-primary fw-semibold mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Información Adicional
                            </h6>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Cuenta Creada</label>
                            <div class="form-control-plaintext">
                                {{ $user->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Última Actualización</label>
                            <div class="form-control-plaintext">
                                {{ $user->updated_at->format('d/m/Y H:i') }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Último Acceso</label>
                            <div class="form-control-plaintext">
                                @if($user->last_login)
                                    {{ $user->last_login->format('d/m/Y H:i') }}
                                    <br>
                                    <small class="text-muted">({{ $user->last_login->diffForHumans() }})</small>
                                @else
                                    <span class="text-muted">Nunca</span>
                                @endif
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
                                    Actualizar Usuario
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
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

@extends('layouts.app')

@section('title', 'Gestión de Usuarios - AuditorSoft')
@section('page-title', 'Gestión de Usuarios')

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
                <i class="fas fa-users me-3 text-primary"></i>
                Gestión de Usuarios
            </h1>
            <p class="page-subtitle">Administra todos los usuarios del sistema AuditorSoft</p>
        </div>
        <a href="{{ route('super-admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Crear Usuario
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('super-admin.users.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Buscar</label>
                <input type="text" 
                       class="form-control" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Nombre o email">
            </div>
            <div class="col-md-3">
                <label for="role" class="form-label">Rol</label>
                <select class="form-select" id="role" name="role">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $value => $label)
                        <option value="{{ $value }}" {{ request('role') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Estado</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="fas fa-search me-1"></i>
                    Filtrar
                </button>
                <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>
                    Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2 fw-semibold">Total Usuarios</h6>
                    <h3 class="mb-1 fw-bold text-primary">{{ $users->total() }}</h3>
                    <small class="text-muted fw-medium">
                        <i class="fas fa-users me-1"></i>En el sistema
                    </small>
                </div>
                <div class="text-primary">
                    <i class="fas fa-users fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card success">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2 fw-semibold">Usuarios Activos</h6>
                    <h3 class="mb-1 fw-bold text-success">{{ $users->where('is_active', true)->count() }}</h3>
                    <small class="text-success fw-medium">
                        <i class="fas fa-check-circle me-1"></i>Habilitados
                    </small>
                </div>
                <div class="text-success">
                    <i class="fas fa-user-check fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card warning">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2 fw-semibold">Super Admins</h6>
                    <h3 class="mb-1 fw-bold text-warning">{{ $users->where('role', 'super_administrador')->count() }}</h3>
                    <small class="text-warning fw-medium">
                        <i class="fas fa-crown me-1"></i>Administradores
                    </small>
                </div>
                <div class="text-warning">
                    <i class="fas fa-user-cog fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card info">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2 fw-semibold">Últimos 30 días</h6>
                    <h3 class="mb-1 fw-bold text-info">{{ $users->where('created_at', '>=', now()->subDays(30))->count() }}</h3>
                    <small class="text-info fw-medium">
                        <i class="fas fa-calendar me-1"></i>Nuevos usuarios
                    </small>
                </div>
                <div class="text-info">
                    <i class="fas fa-user-plus fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0 d-flex align-items-center">
            <i class="fas fa-list me-2 text-primary"></i>
            Lista de Usuarios
            @if(request()->hasAny(['search', 'role', 'status']))
                <span class="badge bg-primary ms-2">Filtrado</span>
            @endif
        </h5>
    </div>
    <div class="card-body">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none text-dark fw-semibold">
                                    Nombre
                                    @if(request('sort') === 'name')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="fas fa-sort ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'email', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none text-dark fw-semibold">
                                    Email
                                    @if(request('sort') === 'email')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="fas fa-sort ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'role', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none text-dark fw-semibold">
                                    Rol
                                    @if(request('sort') === 'role')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="fas fa-sort ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'is_active', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none text-dark fw-semibold">
                                    Estado
                                    @if(request('sort') === 'is_active')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="fas fa-sort ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'last_login', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none text-dark fw-semibold">
                                    Último Acceso
                                    @if(request('sort') === 'last_login')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="fas fa-sort ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none text-dark fw-semibold">
                                    Creado
                                    @if(request('sort') === 'created_at')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="fas fa-sort ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center fw-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3">
                                            <div class="avatar-circle bg-primary text-white">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-semibold">{{ $user->name }}</h6>
                                            @if($user->id === auth()->id())
                                                <small class="text-info">
                                                    <i class="fas fa-star me-1"></i>Tú
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $user->email }}</span>
                                </td>
                                <td>
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
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Activo
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle me-1"></i>Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->last_login)
                                        <span class="text-muted">{{ $user->last_login->diffForHumans() }}</span>
                                    @else
                                        <span class="text-muted">Nunca</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted">{{ $user->created_at->format('d/m/Y') }}</span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('super-admin.users.show', $user) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('super-admin.users.edit', $user) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('super-admin.users.toggle-status', $user) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-{{ $user->is_active ? 'warning' : 'success' }}" 
                                                        title="{{ $user->is_active ? 'Desactivar' : 'Activar' }}"
                                                        onclick="return confirm('¿Estás seguro de cambiar el estado de este usuario?')">>
                                                    <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('super-admin.users.destroy', $user) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Eliminar"
                                                        onclick="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $users->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No se encontraron usuarios</h5>
                @if(request()->hasAny(['search', 'role', 'status']))
                    <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
                    <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-times me-1"></i>
                        Limpiar filtros
                    </a>
                @else
                    <p class="text-muted">Comienza creando tu primer usuario</p>
                    <a href="{{ route('super-admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Crear Usuario
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 16px;
}
</style>
@endsection

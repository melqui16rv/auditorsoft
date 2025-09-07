@extends('layouts.app')

@section('title', 'Dashboard Super Administrador - AuditorSoft')
@section('page-title', 'Dashboard Super Administrador')

@section('sidebar')
<div class="sidebar-nav-item">
    <a class="sidebar-nav-link active" href="{{ route('super-admin.dashboard') }}">
        <i class="fas fa-tachometer-alt"></i>
        <span>Dashboard</span>
    </a>
</div>
<div class="sidebar-nav-item">
    <a class="sidebar-nav-link" href="{{ route('super-admin.users.index') }}">
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
    <h1 class="page-title">
        <i class="fas fa-crown me-3 text-primary"></i>
        Dashboard del Super Administrador
    </h1>
    <p class="page-subtitle">Bienvenido, {{ auth()->user()->name }}. Control total del sistema AuditorSoft.</p>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2 fw-semibold">Total Usuarios</h6>
                    <h3 class="mb-1 fw-bold text-primary">147</h3>
                    <small class="text-success fw-medium">
                        <i class="fas fa-arrow-up me-1"></i>+12 este mes
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
                    <h6 class="text-muted mb-2 fw-semibold">Empresas Activas</h6>
                    <h3 class="mb-1 fw-bold text-success">89</h3>
                    <small class="text-success fw-medium">
                        <i class="fas fa-arrow-up me-1"></i>+5 este mes
                    </small>
                </div>
                <div class="text-success">
                    <i class="fas fa-building fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card warning">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2 fw-semibold">Auditorías Mensuales</h6>
                    <h3 class="mb-1 fw-bold text-warning">234</h3>
                    <small class="text-success fw-medium">
                        <i class="fas fa-arrow-up me-1"></i>+18% vs mes anterior
                    </small>
                </div>
                <div class="text-warning">
                    <i class="fas fa-clipboard-check fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card info">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2 fw-semibold">Ingresos del Mes</h6>
                    <h3 class="mb-1 fw-bold text-info">$45.2K</h3>
                    <small class="text-success fw-medium">
                        <i class="fas fa-arrow-up me-1"></i>+8.5% crecimiento
                    </small>
                </div>
                <div class="text-info">
                    <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="row g-4">
    <!-- System Overview -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-globe me-2 text-primary"></i>
                    Resumen Global del Sistema
                </h5>
            </div>
            <div class="card-body">
                <!-- Role Summary Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light rounded">
                            <h3 class="text-primary fw-bold mb-1">24</h3>
                            <p class="mb-0 text-muted fw-medium">Jefes Auditores</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light rounded">
                            <h3 class="text-success fw-bold mb-1">67</h3>
                            <p class="mb-0 text-muted fw-medium">Auditores</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light rounded">
                            <h3 class="text-warning fw-bold mb-1">56</h3>
                            <p class="mb-0 text-muted fw-medium">Auditados</p>
                        </div>
                    </div>
                </div>
                
                <!-- Regional Performance Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="fw-semibold">Región</th>
                                <th class="fw-semibold">Auditorías Activas</th>
                                <th class="fw-semibold">Completadas</th>
                                <th class="fw-semibold">Pendientes</th>
                                <th class="fw-semibold">Eficiencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                    <span class="fw-medium">Norte</span>
                                </td>
                                <td class="fw-medium">45</td>
                                <td class="text-muted">123</td>
                                <td class="text-muted">12</td>
                                <td>
                                    <span class="badge bg-success rounded-pill">94%</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <i class="fas fa-map-marker-alt me-2 text-success"></i>
                                    <span class="fw-medium">Centro</span>
                                </td>
                                <td class="fw-medium">67</td>
                                <td class="text-muted">156</td>
                                <td class="text-muted">8</td>
                                <td>
                                    <span class="badge bg-success rounded-pill">96%</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <i class="fas fa-map-marker-alt me-2 text-warning"></i>
                                    <span class="fw-medium">Sur</span>
                                </td>
                                <td class="fw-medium">32</td>
                                <td class="text-muted">89</td>
                                <td class="text-muted">15</td>
                                <td>
                                    <span class="badge bg-warning rounded-pill">87%</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- System Alerts -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
                    Alertas del Sistema
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-danger border-0 mb-3" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-server me-2 mt-1"></i>
                        <div>
                            <strong>Servidor:</strong><br>
                            <small>Uso de CPU al 85% - Monitorear</small>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-warning border-0 mb-3" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-database me-2 mt-1"></i>
                        <div>
                            <strong>Base de Datos:</strong><br>
                            <small>Backup programado en 2 horas</small>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info border-0 mb-3" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-users me-2 mt-1"></i>
                        <div>
                            <strong>Usuarios:</strong><br>
                            <small>5 cuentas requieren reactivación</small>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-success border-0 mb-0" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-shield-alt me-2 mt-1"></i>
                        <div>
                            <strong>Seguridad:</strong><br>
                            <small>Todos los sistemas funcionando correctamente</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Section -->
<div class="row g-4 mt-0">
    <!-- Recent Activity -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-history me-2 text-dark"></i>
                    Actividad Reciente del Sistema
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline-item mb-4">
                    <div class="timeline-icon bg-success text-white">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="ms-4">
                        <h6 class="mb-1 fw-semibold">Nuevo usuario registrado</h6>
                        <p class="text-muted mb-1">Carlos Ruiz se registró como Auditor</p>
                        <small class="text-muted">Hace 1 hora</small>
                    </div>
                </div>

                <div class="timeline-item mb-4">
                    <div class="timeline-icon bg-warning text-white">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="ms-4">
                        <h6 class="mb-1 fw-semibold">Nueva empresa registrada</h6>
                        <p class="text-muted mb-1">TechCorp S.A. completó su registro</p>
                        <small class="text-muted">Hace 3 horas</small>
                    </div>
                </div>

                <div class="timeline-item mb-0">
                    <div class="timeline-icon bg-info text-white">
                        <i class="fas fa-cog"></i>
                    </div>
                    <div class="ms-4">
                        <h6 class="mb-1 fw-semibold">Actualización del sistema</h6>
                        <p class="text-muted mb-1">Módulo de reportes actualizado a v2.1</p>
                        <small class="text-muted">Hace 6 horas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-bolt me-2 text-info"></i>
                    Acciones Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="{{ route('super-admin.users.create') }}" class="quick-action-btn">
                            <i class="fas fa-user-plus text-primary"></i>
                            <div class="fw-semibold">Crear Usuario</div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="#" class="quick-action-btn">
                            <i class="fas fa-building text-success"></i>
                            <div class="fw-semibold">Agregar Empresa</div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="#" class="quick-action-btn">
                            <i class="fas fa-chart-bar text-warning"></i>
                            <div class="fw-semibold">Generar Reporte</div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="#" class="quick-action-btn">
                            <i class="fas fa-cogs text-info"></i>
                            <div class="fw-semibold">Configuración</div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="#" class="quick-action-btn">
                            <i class="fas fa-database text-danger"></i>
                            <div class="fw-semibold">Backup Manual</div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="#" class="quick-action-btn">
                            <i class="fas fa-shield-alt text-dark"></i>
                            <div class="fw-semibold">Logs de Seguridad</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

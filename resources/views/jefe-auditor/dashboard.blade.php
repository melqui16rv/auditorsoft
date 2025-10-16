@extends('layouts.app')

@section('title', 'Dashboard Jefe Auditor - AuditorSoft')
@section('page-title', 'Dashboard Jefe Auditor')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-user-tie me-3 text-warning"></i>
        Dashboard del Jefe Auditor
    </h1>
    <p class="page-subtitle">Bienvenido, {{ auth()->user()->name }}. Supervisa y coordina todas las actividades de auditoría.</p>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2 fw-semibold">Proyectos Activos</h6>
                    <h3 class="mb-1 fw-bold text-primary">15</h3>
                    <small class="text-muted fw-medium">En ejecución</small>
                </div>
                <div class="text-primary">
                    <i class="fas fa-project-diagram fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card success">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2 fw-semibold">Auditores Disponibles</h6>
                    <h3 class="mb-1 fw-bold text-success">8</h3>
                    <small class="text-muted fw-medium">Del equipo</small>
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
                    <h6 class="text-muted mb-2 fw-semibold">Revisiones Pendientes</h6>
                    <h3 class="mb-1 fw-bold text-warning">12</h3>
                    <small class="text-muted fw-medium">Por aprobar</small>
                </div>
                <div class="text-warning">
                    <i class="fas fa-clipboard-list fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card info">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2 fw-semibold">Eficiencia del Equipo</h6>
                    <h3 class="mb-1 fw-bold text-info">92%</h3>
                    <small class="text-success fw-medium">
                        <i class="fas fa-arrow-up me-1"></i>+5% vs anterior
                    </small>
                </div>
                <div class="text-info">
                    <i class="fas fa-chart-bar fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="row g-4">
    <!-- Project Overview -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-tasks me-2 text-primary"></i>
                    Resumen de Proyectos de Auditoría
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="fw-semibold">Proyecto</th>
                                <th class="fw-semibold">Auditor Asignado</th>
                                <th class="fw-semibold">Cliente</th>
                                <th class="fw-semibold">Progreso</th>
                                <th class="fw-semibold">Estado</th>
                                <th class="fw-semibold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div>
                                        <h6 class="mb-0 fw-semibold">Auditoría Financiera Q3</h6>
                                        <small class="text-muted">AF-2025-001</small>
                                    </div>
                                </td>
                                <td class="fw-medium">Carlos Mendoza</td>
                                <td class="text-muted">Empresa ABC S.A.</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                            <div class="progress-bar bg-success" style="width: 85%"></div>
                                        </div>
                                        <small class="fw-medium">85%</small>
                                    </div>
                                </td>
                                <td><span class="badge bg-warning rounded-pill">En Proceso</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div>
                                        <h6 class="mb-0 fw-semibold">Auditoría Operacional</h6>
                                        <small class="text-muted">AO-2025-003</small>
                                    </div>
                                </td>
                                <td class="fw-medium">María González</td>
                                <td class="text-muted">Corporación XYZ</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                            <div class="progress-bar bg-danger" style="width: 45%"></div>
                                        </div>
                                        <small class="fw-medium">45%</small>
                                    </div>
                                </td>
                                <td><span class="badge bg-danger rounded-pill">Retrasado</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div>
                                        <h6 class="mb-0 fw-semibold">Auditoría de Sistemas</h6>
                                        <small class="text-muted">AS-2025-005</small>
                                    </div>
                                </td>
                                <td class="fw-medium">Luis Rodríguez</td>
                                <td class="text-muted">Servicios 123 Ltda.</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                            <div class="progress-bar bg-success" style="width: 100%"></div>
                                        </div>
                                        <small class="fw-medium">100%</small>
                                    </div>
                                </td>
                                <td><span class="badge bg-success rounded-pill">Completado</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Content -->
    <div class="col-lg-4">
        <!-- Alerts and Notifications -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                    Alertas y Notificaciones
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-danger border-0 mb-3" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-clock me-2 mt-1"></i>
                        <div>
                            <strong>Proyecto Retrasado:</strong><br>
                            <small>AO-2025-003 requiere atención inmediata</small>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-warning border-0 mb-3" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-user-clock me-2 mt-1"></i>
                        <div>
                            <strong>Asignación Pendiente:</strong><br>
                            <small>3 auditores necesitan nuevos proyectos</small>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info border-0 mb-0" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-chart-line me-2 mt-1"></i>
                        <div>
                            <strong>Reporte Mensual:</strong><br>
                            <small>Generar informe de productividad</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Status -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-users me-2 text-info"></i>
                    Estado del Equipo
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 p-2 rounded bg-light">
                    <span class="fw-medium">Carlos Mendoza</span>
                    <span class="badge bg-success rounded-pill">Disponible</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 p-2 rounded bg-light">
                    <span class="fw-medium">María González</span>
                    <span class="badge bg-warning rounded-pill">Ocupado</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 p-2 rounded bg-light">
                    <span class="fw-medium">Luis Rodríguez</span>
                    <span class="badge bg-success rounded-pill">Disponible</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 p-2 rounded bg-light">
                    <span class="fw-medium">Ana Fernández</span>
                    <span class="badge bg-danger rounded-pill">Sobrecargado</span>
                </div>
                <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                    <span class="fw-medium">Pedro Sánchez</span>
                    <span class="badge bg-secondary rounded-pill">En Capacitación</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance Charts Row -->
<div class="row g-4 mt-0">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-chart-area me-2 text-success"></i>
                    Productividad Mensual
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center p-5 text-muted">
                    <i class="fas fa-chart-line fa-3x mb-3"></i>
                    <p>Gráfico de productividad se mostrará aquí</p>
                    <small>Integración con Chart.js disponible</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-chart-pie me-2 text-dark"></i>
                    Distribución de Proyectos
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center p-5 text-muted">
                    <i class="fas fa-chart-pie fa-3x mb-3"></i>
                    <p>Gráfico de distribución se mostrará aquí</p>
                    <small>Integración con Chart.js disponible</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

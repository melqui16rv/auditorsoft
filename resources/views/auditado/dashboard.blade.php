@extends('layouts.app')

@section('title', 'Dashboard Auditado - AuditorSoft')
@section('page-title', 'Dashboard Auditado')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-user me-3 text-info"></i>
        Dashboard del Auditado
    </h1>
    <p class="page-subtitle">Bienvenido, {{ auth()->user()->name }}. Gestiona tus procesos de auditoría.</p>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2 fw-semibold">Auditorías Activas</h6>
                    <h3 class="mb-1 fw-bold text-primary">3</h3>
                    <small class="text-muted fw-medium">En proceso</small>
                </div>
                <div class="text-primary">
                    <i class="fas fa-tasks fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card warning">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2 fw-semibold">Documentos Pendientes</h6>
                    <h3 class="mb-1 fw-bold text-warning">7</h3>
                    <small class="text-muted fw-medium">Por entregar</small>
                </div>
                <div class="text-warning">
                    <i class="fas fa-file-upload fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card success">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2 fw-semibold">Documentos Aprobados</h6>
                    <h3 class="mb-1 fw-bold text-success">15</h3>
                    <small class="text-success fw-medium">
                        <i class="fas fa-arrow-up me-1"></i>Completados
                    </small>
                </div>
                <div class="text-success">
                    <i class="fas fa-check-circle fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card info">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2 fw-semibold">Mensajes No Leídos</h6>
                    <h3 class="mb-1 fw-bold text-info">2</h3>
                    <small class="text-muted fw-medium">Nuevos</small>
                </div>
                <div class="text-info">
                    <i class="fas fa-envelope fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="row g-4">
    <!-- Recent Activities -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-clock me-2 text-primary"></i>
                    Actividades Recientes
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline-item mb-4">
                    <div class="timeline-icon bg-success text-white">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="ms-4">
                        <h6 class="mb-1 fw-semibold">Documento aprobado</h6>
                        <p class="text-muted mb-1">El auditor aprobó tu reporte financiero Q3</p>
                        <small class="text-muted">Hace 2 horas</small>
                    </div>
                </div>

                <div class="timeline-item mb-4">
                    <div class="timeline-icon bg-warning text-white">
                        <i class="fas fa-upload"></i>
                    </div>
                    <div class="ms-4">
                        <h6 class="mb-1 fw-semibold">Documento requerido</h6>
                        <p class="text-muted mb-1">Se solicita el balance general actualizado</p>
                        <small class="text-muted">Hace 1 día</small>
                    </div>
                </div>

                <div class="timeline-item mb-4">
                    <div class="timeline-icon bg-info text-white">
                        <i class="fas fa-comment"></i>
                    </div>
                    <div class="ms-4">
                        <h6 class="mb-1 fw-semibold">Nuevo mensaje</h6>
                        <p class="text-muted mb-1">El jefe auditor te envió una consulta</p>
                        <small class="text-muted">Hace 2 días</small>
                    </div>
                </div>

                <div class="timeline-item mb-0">
                    <div class="timeline-icon bg-primary text-white">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="ms-4">
                        <h6 class="mb-1 fw-semibold">Reunión programada</h6>
                        <p class="text-muted mb-1">Revisión de documentos financieros programada</p>
                        <small class="text-muted">Hace 3 días</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Tasks -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                    Tareas Pendientes
                </h5>
            </div>
            <div class="card-body">
                <div class="border rounded p-3 mb-3 bg-light">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-1 fw-semibold">Balance General Q4</h6>
                            <small class="text-muted">Vence: 15/Sep/2025</small>
                        </div>
                        <span class="badge bg-danger rounded-pill">Urgente</span>
                    </div>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-danger" style="width: 25%"></div>
                    </div>
                </div>

                <div class="border rounded p-3 mb-3 bg-light">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-1 fw-semibold">Estado de Resultados</h6>
                            <small class="text-muted">Vence: 20/Sep/2025</small>
                        </div>
                        <span class="badge bg-warning rounded-pill">Pendiente</span>
                    </div>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-warning" style="width: 60%"></div>
                    </div>
                </div>

                <div class="border rounded p-3 mb-3 bg-light">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-1 fw-semibold">Flujo de Caja</h6>
                            <small class="text-muted">Vence: 25/Sep/2025</small>
                        </div>
                        <span class="badge bg-info rounded-pill">Normal</span>
                    </div>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-info" style="width: 10%"></div>
                    </div>
                </div>

                <div class="border rounded p-3 bg-light">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-1 fw-semibold">Notas a los Estados</h6>
                            <small class="text-muted">Vence: 30/Sep/2025</small>
                        </div>
                        <span class="badge bg-secondary rounded-pill">Programado</span>
                    </div>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-secondary" style="width: 5%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-4 mt-0">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-bolt me-2 text-info"></i>
                    Acciones Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="#" class="quick-action-btn">
                            <i class="fas fa-upload text-primary"></i>
                            <div class="fw-semibold">Subir Documento</div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="quick-action-btn">
                            <i class="fas fa-calendar-plus text-success"></i>
                            <div class="fw-semibold">Agendar Reunión</div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="quick-action-btn">
                            <i class="fas fa-envelope text-warning"></i>
                            <div class="fw-semibold">Enviar Mensaje</div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="quick-action-btn">
                            <i class="fas fa-chart-bar text-info"></i>
                            <div class="fw-semibold">Ver Reportes</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

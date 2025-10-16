@extends('layouts.app')

@section('title', 'Dashboard Auditor - AuditorSoft')
@section('page-title', 'Dashboard Auditor')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-search me-3 text-success"></i>
        Dashboard del Auditor
    </h1>
    <p class="page-subtitle">Bienvenido, {{ auth()->user()->name }}. Gestiona tus auditorías y procesos de revisión.</p>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2 fw-semibold">Auditorías Activas</h6>
                    <h3 class="mb-1 fw-bold text-primary">8</h3>
                    <small class="text-muted fw-medium">En progreso</small>
                </div>
                <div class="text-primary">
                    <i class="fas fa-clipboard-check fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card warning">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2 fw-semibold">Documentos por Revisar</h6>
                    <h3 class="mb-1 fw-bold text-warning">23</h3>
                    <small class="text-muted fw-medium">Pendientes</small>
                </div>
                <div class="text-warning">
                    <i class="fas fa-file-alt fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card success">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2 fw-semibold">Auditados Asignados</h6>
                    <h3 class="mb-1 fw-bold text-success">12</h3>
                    <small class="text-muted fw-medium">Empresas</small>
                </div>
                <div class="text-success">
                    <i class="fas fa-users fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card info">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2 fw-semibold">Informes Completados</h6>
                    <h3 class="mb-1 fw-bold text-info">45</h3>
                    <small class="text-success fw-medium">
                        <i class="fas fa-arrow-up me-1"></i>Este mes
                    </small>
                </div>
                <div class="text-info">
                    <i class="fas fa-check-double fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="row g-4">
    <!-- Pending Audits -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-list-ul me-2 text-primary"></i>
                    Auditorías Pendientes de Revisión
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="fw-semibold">Auditado</th>
                                <th class="fw-semibold">Tipo de Auditoría</th>
                                <th class="fw-semibold">Estado</th>
                                <th class="fw-semibold">Fecha Límite</th>
                                <th class="fw-semibold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary rounded-circle p-2 text-white me-3">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold">Empresa ABC S.A.</h6>
                                            <small class="text-muted">auditado1@empresa.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-medium">Auditoría Financiera</td>
                                <td><span class="badge bg-warning rounded-pill">En Revisión</span></td>
                                <td class="text-muted">15/Sep/2025</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> Ver
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success rounded-circle p-2 text-white me-3">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold">Corporación XYZ</h6>
                                            <small class="text-muted">auditado2@corp.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-medium">Auditoría Operacional</td>
                                <td><span class="badge bg-danger rounded-pill">Urgente</span></td>
                                <td class="text-muted">12/Sep/2025</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> Ver
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info rounded-circle p-2 text-white me-3">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold">Servicios 123 Ltda.</h6>
                                            <small class="text-muted">auditado3@servicios.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-medium">Auditoría de Sistemas</td>
                                <td><span class="badge bg-info rounded-pill">Programada</span></td>
                                <td class="text-muted">20/Sep/2025</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> Ver
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
        <!-- Daily Agenda -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-clock me-2 text-warning"></i>
                    Agenda del Día
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline-item mb-3">
                    <div class="timeline-icon bg-primary text-white">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="ms-4">
                        <h6 class="mb-1 fw-semibold">Revisión Financiera ABC</h6>
                        <p class="text-muted mb-1">09:00 - 11:00</p>
                        <span class="badge bg-primary rounded-pill">Hoy</span>
                    </div>
                </div>

                <div class="timeline-item mb-3">
                    <div class="timeline-icon bg-info text-white">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="ms-4">
                        <h6 class="mb-1 fw-semibold">Reunión con Jefe Auditor</h6>
                        <p class="text-muted mb-1">14:00 - 15:00</p>
                        <span class="badge bg-info rounded-pill">Hoy</span>
                    </div>
                </div>

                <div class="timeline-item mb-0">
                    <div class="timeline-icon bg-success text-white">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div class="ms-4">
                        <h6 class="mb-1 fw-semibold">Entrega Informe XYZ</h6>
                        <p class="text-muted mb-1">16:00</p>
                        <span class="badge bg-success rounded-pill">Hoy</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Audit Status -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="fas fa-chart-pie me-2 text-info"></i>
                    Estado de Auditorías
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-medium">Completadas</span>
                        <span class="text-success fw-bold">60%</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: 60%"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-medium">En Proceso</span>
                        <span class="text-warning fw-bold">30%</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: 30%"></div>
                    </div>
                </div>

                <div class="mb-0">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-medium">Pendientes</span>
                        <span class="text-danger fw-bold">10%</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-danger" style="width: 10%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

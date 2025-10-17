@extends('layouts.app')

@section('title', 'Programas de Auditoría')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-calendar-check text-primary"></i> Programas de Auditoría Interna
                    </h1>
                    <small class="text-muted">Gestión de programas anuales de auditoría - RF 3.3 a 3.6</small>
                </div>
                @if(auth()->user()->role === 'jefe_auditor' || auth()->user()->role === 'super_administrador')
                    <a href="{{ route('programa-auditoria.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus-circle"></i> Nuevo Programa
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Información de permisos según rol -->
    @if(auth()->user()->role === 'jefe_auditor')
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle"></i> <strong>Tu Rol:</strong> Jefe de Control Interno
            <small class="d-block mt-2">Puedes: Crear, editar y enviar a aprobación los programas en estado "Elaboración"</small>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @elseif(auth()->user()->role === 'super_administrador')
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-shield-alt"></i> <strong>Tu Rol:</strong> Super Administrador
            <small class="d-block mt-2">Puedes: Crear, editar, enviar, aprobar y eliminar todos los programas</small>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @elseif(auth()->user()->role === 'auditor')
        <div class="alert alert-secondary alert-dismissible fade show" role="alert">
            <i class="fas fa-eye"></i> <strong>Tu Rol:</strong> Auditor
            <small class="d-block mt-2">Acceso de consulta: Puedes ver los programas aprobados para ejecución</small>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @else
        <div class="alert alert-light alert-dismissible fade show" role="alert">
            <i class="fas fa-lock"></i> <strong>Acceso Limitado</strong>
            <small class="d-block mt-2">Tu rol no tiene permisos en este módulo</small>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-exclamation-circle"></i> Error</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-times-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Tabla de Programas -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> Lista de Programas
                @if($programas->count() > 0)
                    <span class="badge bg-primary">{{ $programas->total() }}</span>
                @endif
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 10%;">Código</th>
                        <th style="width: 20%;">Nombre</th>
                        <th style="width: 8%;">Vigencia</th>
                        <th style="width: 15%;">Estado</th>
                        <th style="width: 8%;">Procesos</th>
                        <th style="width: 15%;">Elaborado por</th>
                        <th style="width: 12%;">Creado</th>
                        <th style="width: 12%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($programas as $programa)
                        <tr>
                            <td>
                                <strong class="text-primary">{{ $programa->codigo }}</strong>
                            </td>
                            <td>{{ $programa->nombre }}</td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $programa->vigencia }}</span>
                            </td>
                            <td>
                                @switch($programa->estado)
                                    @case('elaboracion')
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-edit"></i> Elaboración
                                        </span>
                                    @break

                                    @case('enviado_aprobacion')
                                        <span class="badge bg-info text-white">
                                            <i class="fas fa-hourglass-half"></i> Aprobación
                                        </span>
                                    @break

                                    @case('aprobado')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle"></i> Aprobado
                                        </span>
                                    @break
                                @endswitch
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $programa->detalles->count() }}</span>
                            </td>
                            <td>
                                <small>{{ $programa->elaboradoPor->name ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <small>{{ $programa->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <!-- Ver -->
                                    <a href="{{ route('programa-auditoria.show', $programa) }}"
                                        class="btn btn-outline-info" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Editar (solo si está en elaboración) -->
                                    @if($programa->estado === 'elaboracion' && 
                                        (auth()->user()->role === 'jefe_auditor' || auth()->user()->role === 'super_administrador'))
                                        <a href="{{ route('programa-auditoria.edit', $programa) }}"
                                            class="btn btn-outline-warning" title="Editar programa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    <!-- Enviar a Aprobación -->
                                    @if($programa->estado === 'elaboracion' && auth()->user()->role === 'jefe_auditor')
                                        <form method="POST" action="{{ route('programa-auditoria.enviar', $programa) }}"
                                            style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-success btn-sm"
                                                title="Enviar a aprobación del Comité ICCCI"
                                                onclick="return confirm('¿Enviar este programa a aprobación?')">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Aprobar (solo Super Admin) -->
                                    @if($programa->estado === 'enviado_aprobacion' && auth()->user()->role === 'super_administrador')
                                        <form method="POST" action="{{ route('programa-auditoria.aprobar', $programa) }}"
                                            style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-success btn-sm"
                                                title="Aprobar por Comité ICCCI"
                                                onclick="return confirm('¿Aprobar este programa?')">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Eliminar -->
                                    @if($programa->estado === 'elaboracion' && 
                                        (auth()->user()->role === 'jefe_auditor' || auth()->user()->role === 'super_administrador'))
                                        <form method="POST" action="{{ route('programa-auditoria.destroy', $programa) }}"
                                            style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                title="Eliminar programa"
                                                onclick="return confirm('¿Eliminar este programa? Esta acción no se puede deshacer.');">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-inbox" style="font-size: 2rem; color: #ccc;"></i>
                                <p class="text-muted mt-3 mb-0">
                                    No hay programas registrados
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($programas->hasPages())
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Mostrando {{ $programas->firstItem() }} a {{ $programas->lastItem() }} de {{ $programas->total() }} programas
                    </small>
                    {{ $programas->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

    <!-- Leyenda de acceso -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body small">
                    <strong><i class="fas fa-info-circle"></i> Leyenda de acceso por rol:</strong>
                    <ul class="mb-0 mt-2">
                        <li><strong>Jefe de Control Interno:</strong> Crear, editar, enviar a aprobación y ver todos los programas</li>
                        <li><strong>Super Administrador:</strong> Control total (crear, editar, aprobar, eliminar)</li>
                        <li><strong>Auditor:</strong> Acceso de consulta a programas aprobados para ejecución</li>
                        <li><strong>Auditado:</strong> No tiene acceso a este módulo</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

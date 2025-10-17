@extends('layouts.app')

@section('title', 'Programa - ' . $programaAuditoria->codigo)

@section('content')
<div class="container-fluid">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-calendar-check text-primary"></i> {{ $programaAuditoria->codigo }}
                    </h1>
                    <small class="text-muted">{{ $programaAuditoria->nombre }} - Vigencia {{ $programaAuditoria->vigencia }}</small>
                </div>
                <div class="d-flex gap-2">
                    <!-- Editar -->
                    @if($programaAuditoria->estado === 'elaboracion' && 
                        (auth()->user()->role === 'jefe_auditor' || auth()->user()->role === 'super_administrador'))
                        <a href="{{ route('programa-auditoria.edit', $programaAuditoria) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    @endif

                    <!-- Enviar a Aprobación -->
                    @if($programaAuditoria->estado === 'elaboracion' && auth()->user()->role === 'jefe_auditor')
                        <form method="POST" action="{{ route('programa-auditoria.enviar', $programaAuditoria) }}"
                            style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success"
                                title="Enviar al Comité ICCCI para aprobación"
                                onclick="return confirm('¿Enviar este programa a aprobación del Comité ICCCI?')">
                                <i class="fas fa-paper-plane"></i> Enviar Aprobación
                            </button>
                        </form>
                    @endif

                    <!-- Aprobar -->
                    @if($programaAuditoria->estado === 'enviado_aprobacion' && auth()->user()->role === 'super_administrador')
                        <form method="POST" action="{{ route('programa-auditoria.aprobar', $programaAuditoria) }}"
                            style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success"
                                title="Aprobar en nombre del Comité ICCCI"
                                onclick="return confirm('¿Aprobar este programa?')">
                                <i class="fas fa-check-circle"></i> Aprobar
                            </button>
                        </form>
                    @endif

                    <!-- Eliminar -->
                    @if($programaAuditoria->estado === 'elaboracion' && 
                        (auth()->user()->role === 'jefe_auditor' || auth()->user()->role === 'super_administrador'))
                        <form method="POST" action="{{ route('programa-auditoria.destroy', $programaAuditoria) }}"
                            style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"
                                title="Eliminar programa"
                                onclick="return confirm('¿Eliminar este programa? Esta acción no se puede deshacer.');">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </button>
                        </form>
                    @endif

                    <!-- Volver -->
                    <a href="{{ route('programa-auditoria.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

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

    <div class="row">
        <!-- Información Principal -->
        <div class="col-lg-8">
            <!-- Información del Programa -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> Información General
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-1"><strong><i class="fas fa-heading"></i> Nombre:</strong></p>
                            <p class="text-muted">{{ $programaAuditoria->nombre }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong><i class="fas fa-calendar-alt"></i> Vigencia:</strong></p>
                            <p class="text-muted">{{ $programaAuditoria->vigencia }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-1"><strong><i class="fas fa-play"></i> Fecha Inicio:</strong></p>
                            <p class="text-muted">
                                {{ $programaAuditoria->fecha_inicio_programacion ? \Carbon\Carbon::parse($programaAuditoria->fecha_inicio_programacion)->format('d/m/Y') : 'No definida' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong><i class="fas fa-stop"></i> Fecha Fin:</strong></p>
                            <p class="text-muted">
                                {{ $programaAuditoria->fecha_fin_programacion ? \Carbon\Carbon::parse($programaAuditoria->fecha_fin_programacion)->format('d/m/Y') : 'No definida' }}
                            </p>
                        </div>
                    </div>

                    @if ($programaAuditoria->numero_auditores)
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p class="mb-1"><strong><i class="fas fa-users"></i> Número de Auditores:</strong></p>
                                <p class="text-muted">{{ $programaAuditoria->numero_auditores }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($programaAuditoria->objetivos)
                        <div class="mb-4">
                            <p class="mb-2"><strong><i class="fas fa-bullseye"></i> Objetivos:</strong></p>
                            <p class="text-muted bg-light p-3 rounded">{{ nl2br(e($programaAuditoria->objetivos)) }}</p>
                        </div>
                    @endif

                    @if ($programaAuditoria->alcance)
                        <div class="mb-0">
                            <p class="mb-2"><strong><i class="fas fa-expand"></i> Alcance:</strong></p>
                            <p class="text-muted bg-light p-3 rounded">{{ nl2br(e($programaAuditoria->alcance)) }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Procesos del Programa -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list-check"></i> Procesos Programados 
                        <span class="badge bg-light text-dark">{{ $programaAuditoria->detalles->count() }}</span>
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 25%;">Proceso / Área</th>
                                <th style="width: 15%;">Riesgo</th>
                                <th style="width: 20%;">Estado</th>
                                <th style="width: 20%;">Auditor</th>
                                <th style="width: 20%;">Fechas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($programaAuditoria->detalles as $detalle)
                                <tr>
                                    <td>
                                        <strong>{{ $detalle->proceso->nombre ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ $detalle->area->nombre ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        @switch($detalle->riesgo_nivel ?? 'medio')
                                            @case('alto')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-exclamation"></i> Alto
                                                </span>
                                            @break

                                            @case('medio')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-minus"></i> Medio
                                                </span>
                                            @break

                                            @case('bajo')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check"></i> Bajo
                                                </span>
                                            @break

                                            @default
                                                <span class="badge bg-secondary">Indefinido</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @switch($detalle->estado_auditoria ?? 'programado')
                                            @case('programado')
                                                <span class="badge bg-info">Programado</span>
                                            @break

                                            @case('en_ejecucion')
                                                <span class="badge bg-warning text-dark">En Ejecución</span>
                                            @break

                                            @case('finalizado')
                                                <span class="badge bg-success">Finalizado</span>
                                            @break

                                            @case('anulado')
                                                <span class="badge bg-secondary">Anulado</span>
                                            @break

                                            @default
                                                <span class="badge bg-light text-dark">Pendiente</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <small>
                                            {{ $detalle->auditorResponsable->name ?? '<em class="text-danger">Sin asignar</em>' }}
                                        </small>
                                    </td>
                                    <td>
                                        <small>
                                            @if ($detalle->fecha_inicio && $detalle->fecha_fin)
                                                {{ $detalle->fecha_inicio->format('d/m') }} a {{ $detalle->fecha_fin->format('d/m/Y') }}
                                            @elseif($detalle->fecha_inicio)
                                                Desde {{ $detalle->fecha_inicio->format('d/m/Y') }}
                                            @else
                                                <em class="text-muted">Por definir</em>
                                            @endif
                                        </small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="fas fa-inbox" style="font-size: 1.5rem; color: #ccc;"></i>
                                        <p class="text-muted mt-2 mb-0">Sin procesos programados</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Estado Actual -->
            <div class="card mb-3 border-left-4" style="border-left: 4px solid #0d6efd;">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-flag"></i> Estado Actual
                    </h5>
                </div>
                <div class="card-body text-center">
                    @switch($programaAuditoria->estado)
                        @case('elaboracion')
                            <span class="badge bg-warning text-dark p-3" style="font-size: 1rem;">
                                <i class="fas fa-edit"></i> En Elaboración
                            </span>
                            <p class="small text-muted mt-2 mb-0">Puede editarse e enviarse a aprobación</p>
                        @break

                        @case('enviado_aprobacion')
                            <span class="badge bg-info p-3" style="font-size: 1rem;">
                                <i class="fas fa-hourglass-half"></i> En Aprobación
                            </span>
                            <p class="small text-muted mt-2 mb-0">Esperando revisión del Comité ICCCI</p>
                        @break

                        @case('aprobado')
                            <span class="badge bg-success p-3" style="font-size: 1rem;">
                                <i class="fas fa-check-circle"></i> Aprobado
                            </span>
                            <p class="small text-muted mt-2 mb-0">Listo para ejecución de auditorías</p>
                        @break
                    @endswitch
                </div>
            </div>

            <!-- Información Técnica -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt"></i> Información del Programa
                    </h5>
                </div>
                <div class="card-body small">
                    <p class="mb-2">
                        <strong>Matriz Origen:</strong><br>
                        <code>{{ $programaAuditoria->matrizPriorizacion->codigo ?? 'N/A' }}</code>
                    </p>
                    <p class="mb-2">
                        <strong>Total Procesos:</strong><br>
                        <span class="badge bg-primary">{{ $programaAuditoria->totalProcesos() ?? 0 }}</span>
                    </p>
                    <p class="mb-0">
                        <strong>Riesgo Promedio:</strong><br>
                        <span class="badge bg-warning text-dark">{{ round($programaAuditoria->riesgoPromedio() ?? 0, 1) }}%</span>
                    </p>
                </div>
            </div>

            <!-- Responsables -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tie"></i> Responsables
                    </h5>
                </div>
                <div class="card-body small">
                    <p class="mb-2">
                        <strong>Elaborado por:</strong><br>
                        <span class="badge bg-light text-dark">{{ $programaAuditoria->elaboradoPor->name ?? 'N/A' }}</span>
                    </p>
                    @if ($programaAuditoria->aprobadoPor)
                        <p class="mb-0">
                            <strong>Aprobado por:</strong><br>
                            <span class="badge bg-success">{{ $programaAuditoria->aprobadoPor->name }}</span>
                        </p>
                    @endif
                </div>
            </div>

            <!-- Fechas de Control -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-check"></i> Fechas de Control
                    </h5>
                </div>
                <div class="card-body small">
                    <p class="mb-2">
                        <strong>Creado:</strong><br>
                        <span class="text-muted">{{ $programaAuditoria->created_at->format('d/m/Y') }}</span>
                    </p>
                    @if ($programaAuditoria->updated_at != $programaAuditoria->created_at)
                        <p class="mb-2">
                            <strong>Última Modificación:</strong><br>
                            <span class="text-muted">{{ $programaAuditoria->updated_at->format('d/m/Y') }}</span>
                        </p>
                    @endif
                    @if ($programaAuditoria->fecha_aprobacion)
                        <p class="mb-0">
                            <strong>Aprobado:</strong><br>
                            <span class="text-muted">{{ \Carbon\Carbon::parse($programaAuditoria->fecha_aprobacion)->format('d/m/Y') }}</span>
                        </p>
                    @endif
                </div>
            </div>

            <!-- Leyenda de Acceso -->
            <div class="alert alert-info small mt-3 mb-0">
                <strong><i class="fas fa-info-circle"></i> Tu Rol:</strong>
                <small class="d-block mt-1">
                    @if(auth()->user()->role === 'jefe_auditor')
                        Jefe de Control Interno - Puedes editar, enviar a aprobación
                    @elseif(auth()->user()->role === 'super_administrador')
                        Super Administrador - Control total del programa
                    @elseif(auth()->user()->role === 'auditor')
                        Auditor - Acceso de consulta
                    @else
                        {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                    @endif
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

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

    <div class="row">
        <!-- Información Principal -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> Información del Programa
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Nombre:</strong></p>
                            <p class="text-muted">{{ $programaAuditoria->nombre }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Vigencia:</strong></p>
                            <p class="text-muted">{{ $programaAuditoria->vigencia }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Fecha Inicio:</strong></p>
                            <p class="text-muted">
                                {{ $programaAuditoria->fecha_inicio_programacion ? \Carbon\Carbon::parse($programaAuditoria->fecha_inicio_programacion)->format('d/m/Y') : 'N/A' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Fecha Fin:</strong></p>
                            <p class="text-muted">
                                {{ $programaAuditoria->fecha_fin_programacion ? \Carbon\Carbon::parse($programaAuditoria->fecha_fin_programacion)->format('d/m/Y') : 'N/A' }}
                            </p>
                        </div>
                    </div>

                    @if ($programaAuditoria->numero_auditores)
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Número de Auditores:</strong></p>
                                <p class="text-muted">{{ $programaAuditoria->numero_auditores }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($programaAuditoria->objetivos)
                        <div class="mb-3">
                            <p class="mb-1"><strong>Objetivos:</strong></p>
                            <p class="text-muted">{{ nl2br($programaAuditoria->objetivos) }}</p>
                        </div>
                    @endif

                    @if ($programaAuditoria->alcance)
                        <div class="mb-3">
                            <p class="mb-1"><strong>Alcance:</strong></p>
                            <p class="text-muted">{{ nl2br($programaAuditoria->alcance) }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Procesos del Programa -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Procesos Programados ({{ $programaAuditoria->detalles->count() }})
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Proceso</th>
                                <th>Riesgo</th>
                                <th>Estado</th>
                                <th>Auditor</th>
                                <th>Fechas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($programaAuditoria->detalles as $detalle)
                                <tr>
                                    <td>
                                        <strong>{{ $detalle->proceso->nombre ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ $detalle->area->nombre ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        @switch($detalle->riesgo_nivel)
                                            @case('alto')
                                                <span class="badge bg-danger">Alto</span>
                                            @break

                                            @case('medio')
                                                <span class="badge bg-warning text-dark">Medio</span>
                                            @break

                                            @case('bajo')
                                                <span class="badge bg-success">Bajo</span>
                                            @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @switch($detalle->estado_auditoria)
                                            @case('programado')
                                                <span class="badge bg-info">Programado</span>
                                            @break

                                            @case('en_ejecucion')
                                                <span class="badge bg-warning text-dark">En Ejecución</span>
                                            @break

                                            @case('finalizado')
                                                <span class="badge bg-success">Finalizado</span>
                                            @break

                                            @case('anulado')
                                                <span class="badge bg-secondary">Anulado</span>
                                            @break
                                        @endswitch
                                    </td>
                                    <td>
                                        {{ $detalle->auditorResponsable->name ?? 'Sin asignar' }}
                                    </td>
                                    <td>
                                        <small>
                                            @if ($detalle->fecha_inicio)
                                                {{ $detalle->fecha_inicio->format('d/m/Y') }}
                                            @else
                                                -
                                            @endif
                                            @if ($detalle->fecha_fin)
                                                a {{ $detalle->fecha_fin->format('d/m/Y') }}
                                            @endif
                                        </small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-inbox"></i> Sin procesos programados
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar de Información -->
        <div class="col-lg-4">
            <!-- Estado -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-flag"></i> Estado
                    </h5>
                </div>
                <div class="card-body">
                    @switch($programaAuditoria->estado)
                        @case('elaboracion')
                            <span class="badge bg-warning text-dark" style="font-size: 0.95rem;">
                                <i class="fas fa-edit"></i> En Elaboración
                            </span>
                        @break

                        @case('enviado_aprobacion')
                            <span class="badge bg-info" style="font-size: 0.95rem;">
                                <i class="fas fa-hourglass-half"></i> Enviado a Aprobación
                            </span>
                        @break

                        @case('aprobado')
                            <span class="badge bg-success" style="font-size: 0.95rem;">
                                <i class="fas fa-check"></i> Aprobado
                            </span>
                        @break
                    @endswitch
                </div>
            </div>

            <!-- Información de Auditoria -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt"></i> Información
                    </h5>
                </div>
                <div class="card-body small">
                    <p class="mb-2">
                        <strong>Matriz:</strong><br>
                        {{ $programaAuditoria->matrizPriorizacion->codigo }}
                    </p>
                    <p class="mb-2">
                        <strong>Total Procesos:</strong><br>
                        {{ $programaAuditoria->totalProcesos() }}
                    </p>
                    <p class="mb-0">
                        <strong>Riesgo Promedio:</strong><br>
                        <span class="badge bg-secondary">{{ round($programaAuditoria->riesgoPromedio(), 2) }}%</span>
                    </p>
                </div>
            </div>

            <!-- Usuarios -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-users"></i> Responsables
                    </h5>
                </div>
                <div class="card-body small">
                    <p class="mb-2">
                        <strong>Elaborado por:</strong><br>
                        {{ $programaAuditoria->elaboradoPor->name }}
                    </p>
                    @if ($programaAuditoria->aprobadoPor)
                        <p class="mb-0">
                            <strong>Aprobado por:</strong><br>
                            {{ $programaAuditoria->aprobadoPor->name }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- Fechas de Auditoría -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar"></i> Fechas
                    </h5>
                </div>
                <div class="card-body small">
                    <p class="mb-2">
                        <strong>Creado:</strong><br>
                        <span class="text-muted">{{ $programaAuditoria->created_at->format('d/m/Y H:i') }}</span>
                    </p>
                    @if ($programaAuditoria->updated_at != $programaAuditoria->created_at)
                        <p class="mb-2">
                            <strong>Modificado:</strong><br>
                            <span class="text-muted">{{ $programaAuditoria->updated_at->format('d/m/Y H:i') }}</span>
                        </p>
                    @endif
                    @if ($programaAuditoria->fecha_aprobacion)
                        <p class="mb-0">
                            <strong>Aprobado:</strong><br>
                            <span class="text-muted">{{ \Carbon\Carbon::parse($programaAuditoria->fecha_aprobacion)->format('d/m/Y') }}</span>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

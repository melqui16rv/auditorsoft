@extends('layouts.app')

@section('title', 'Matriz: ' . $matriz->nombre)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 text-dark">
                <i class="fas fa-chart-bar"></i> {{ $matriz->nombre }}
            </h1>
            <p class="text-muted">
                <strong>Código:</strong> {{ $matriz->codigo }} |
                <strong>Vigencia:</strong> {{ $matriz->vigencia }} |
                <strong>Municipio:</strong> {{ $matriz->municipio->nombre }}
            </p>
        </div>
    </div>

    <!-- Estado y Acciones -->
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <span class="badge bg-primary fs-6">Estado: 
                    @if ($matriz->estado == 'borrador')
                        <span class="badge bg-secondary">Borrador</span>
                    @elseif ($matriz->estado == 'validado')
                        <span class="badge bg-warning">Validado</span>
                    @else
                        <span class="badge bg-success">Aprobado</span>
                    @endif
                </span>
                <small class="d-block mt-2 text-muted">
                    Elaborado por: <strong>{{ $matriz->elaboradoPor->name }}</strong> 
                    ({{ $matriz->created_at->format('d/m/Y H:i') }})
                </small>
            </div>
            <div class="btn-group" role="group">
                @if ($matriz->puedeSerEditada())
                    <a href="{{ route('matriz-priorizacion.edit', $matriz) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                @endif

                @if ($matriz->estado == 'borrador' && auth()->user()->role == 'jefe_auditor')
                    <form action="{{ route('matriz-priorizacion.validar', $matriz) }}" method="POST" 
                        class="d-inline" onsubmit="return confirm('¿Validar esta matriz?')">
                        @csrf
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-check"></i> Validar
                        </button>
                    </form>
                @endif

                @if ($matriz->estado == 'validado' && auth()->user()->role == 'super_administrador')
                    <form action="{{ route('matriz-priorizacion.aprobar', $matriz) }}" method="POST" 
                        class="d-inline" onsubmit="return confirm('¿Aprobar definitivamente?')">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-thumbs-up"></i> Aprobar
                        </button>
                    </form>
                @endif

                <a href="{{ route('matriz-priorizacion.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Procesos</h5>
                    <h3 class="text-primary">{{ $matriz->detalles->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h5 class="card-title">A Auditar</h5>
                    <h3 class="text-warning">{{ $matriz->procesosAuditar() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h5 class="card-title">Riesgo Promedio</h5>
                    <h3 class="text-info">{{ number_format($matriz->riesgoPromedio(), 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h5 class="card-title">Criterios</h5>
                    <h3 class="text-success">{{ $matriz->municipio->departamento }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalles de Procesos -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Evaluación de Procesos</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Proceso</th>
                            <th style="width: 12%;">Riesgo</th>
                            <th style="width: 12%;">Ponderación</th>
                            <th style="width: 15%;">Ciclo Rotación</th>
                            <th style="width: 10%;">¿Auditar?</th>
                            <th style="width: 12%;">Días Transcurridos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($matriz->detalles as $detalle)
                            <tr>
                                <td>
                                    <strong>{{ $detalle->proceso->nombre }}</strong>
                                </td>
                                <td>
                                    @php
                                        $colors = [
                                            'extremo' => 'danger',
                                            'alto' => 'warning',
                                            'moderado' => 'info',
                                            'bajo' => 'success'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $colors[$detalle->riesgo_nivel] }}">
                                        {{ ucfirst($detalle->riesgo_nivel) }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $detalle->ponderacion_riesgo }}/5</strong>
                                </td>
                                <td>
                                    {{ ucfirst(str_replace('_', ' ', $detalle->ciclo_rotacion)) }}
                                </td>
                                <td>
                                    @if ($detalle->incluir_en_programa)
                                        <i class="fas fa-check-circle text-success"></i> Sí
                                    @else
                                        <i class="fas fa-times-circle text-secondary"></i> No
                                    @endif
                                </td>
                                <td>
                                    {{ $detalle->dias_transcurridos ?? 0 }} días
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Historial de Cambios -->
    <div class="card mt-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Auditoría</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p>
                        <strong>Elaborado por:</strong><br>
                        {{ $matriz->elaboradoPor->name }}<br>
                        <small class="text-muted">{{ $matriz->created_at->format('d/m/Y H:i') }}</small>
                    </p>
                </div>
                <div class="col-md-4">
                    @if ($matriz->updated_by)
                        <p>
                            <strong>Última actualización:</strong><br>
                            {{ $matriz->actualizadoPor->name ?? 'N/A' }}<br>
                            <small class="text-muted">{{ $matriz->updated_at->format('d/m/Y H:i') }}</small>
                        </p>
                    @else
                        <p class="text-muted">Sin actualizaciones</p>
                    @endif
                </div>
                <div class="col-md-4">
                    @if ($matriz->aprobadoPor)
                        <p>
                            <strong>Aprobado por:</strong><br>
                            {{ $matriz->aprobadoPor->name }}<br>
                            <small class="text-muted">{{ $matriz->estado_actualizado_at ?? $matriz->updated_at->format('d/m/Y H:i') }}</small>
                        </p>
                    @else
                        <p class="text-muted">Pendiente de aprobación</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

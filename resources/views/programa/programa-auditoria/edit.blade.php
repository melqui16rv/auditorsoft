@extends('layouts.app')

@section('title', 'Editar Programa - ' . $programaAuditoria->codigo)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3">
                <i class="fas fa-edit"></i> Editar Programa {{ $programaAuditoria->codigo }}
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('programa-auditoria.update', $programaAuditoria) }}">
                        @csrf
                        @method('PUT')

                        <!-- Información de Matriz (solo lectura) -->
                        <div class="mb-3">
                            <label class="form-label">
                                <strong>Matriz de Priorización</strong>
                            </label>
                            <input type="text" class="form-control" disabled value="{{ $programaAuditoria->matrizPriorizacion->codigo }} - {{ $programaAuditoria->matrizPriorizacion->nombre }}">
                            <small class="form-text text-muted">No puede cambiar la matriz asociada</small>
                        </div>

                        <!-- Nombre del Programa -->
                        <div class="mb-3">
                            <label for="nombre" class="form-label">
                                <strong>Nombre <span class="text-danger">*</span></strong>
                            </label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                id="nombre" name="nombre" maxlength="200"
                                value="{{ old('nombre', $programaAuditoria->nombre) }}" required>
                            @error('nombre')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Fechas -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fecha_inicio_programacion" class="form-label">
                                        <strong>Fecha Inicio <span class="text-danger">*</span></strong>
                                    </label>
                                    <input type="date"
                                        class="form-control @error('fecha_inicio_programacion') is-invalid @enderror"
                                        id="fecha_inicio_programacion" name="fecha_inicio_programacion"
                                        value="{{ old('fecha_inicio_programacion', $programaAuditoria->fecha_inicio_programacion) }}" required>
                                    @error('fecha_inicio_programacion')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fecha_fin_programacion" class="form-label">
                                        <strong>Fecha Fin <span class="text-danger">*</span></strong>
                                    </label>
                                    <input type="date"
                                        class="form-control @error('fecha_fin_programacion') is-invalid @enderror"
                                        id="fecha_fin_programacion" name="fecha_fin_programacion"
                                        value="{{ old('fecha_fin_programacion', $programaAuditoria->fecha_fin_programacion) }}" required>
                                    @error('fecha_fin_programacion')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Número de Auditores -->
                        <div class="mb-3">
                            <label for="numero_auditores" class="form-label">
                                <strong>Número de Auditores</strong>
                            </label>
                            <input type="number" class="form-control @error('numero_auditores') is-invalid @enderror"
                                id="numero_auditores" name="numero_auditores" min="1" max="50"
                                value="{{ old('numero_auditores', $programaAuditoria->numero_auditores) }}">
                            @error('numero_auditores')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Objetivos -->
                        <div class="mb-3">
                            <label for="objetivos" class="form-label">
                                <strong>Objetivos</strong>
                            </label>
                            <textarea class="form-control @error('objetivos') is-invalid @enderror"
                                id="objetivos" name="objetivos" rows="3" maxlength="2000">{{ old('objetivos', $programaAuditoria->objetivos) }}</textarea>
                            @error('objetivos')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Máximo 2000 caracteres</small>
                        </div>

                        <!-- Alcance -->
                        <div class="mb-3">
                            <label for="alcance" class="form-label">
                                <strong>Alcance</strong>
                            </label>
                            <textarea class="form-control @error('alcance') is-invalid @enderror"
                                id="alcance" name="alcance" rows="3" maxlength="2000">{{ old('alcance', $programaAuditoria->alcance) }}</textarea>
                            @error('alcance')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Máximo 2000 caracteres</small>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                            <a href="{{ route('programa-auditoria.show', $programaAuditoria) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel de Estado -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> Estado
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Estado Actual:</strong>
                    </p>
                    <p class="mb-3">
                        @switch($programaAuditoria->estado)
                            @case('elaboracion')
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-edit"></i> En Elaboración
                                </span>
                            @break

                            @case('enviado_aprobacion')
                                <span class="badge bg-info">
                                    <i class="fas fa-hourglass-half"></i> Enviado a Aprobación
                                </span>
                            @break

                            @case('aprobado')
                                <span class="badge bg-success">
                                    <i class="fas fa-check"></i> Aprobado
                                </span>
                            @break
                        @endswitch
                    </p>

                    <hr>

                    <p class="small text-muted mb-0">
                        <strong>Procesos:</strong> {{ $programaAuditoria->detalles->count() }}
                    </p>
                    <p class="small text-muted">
                        <strong>Vigencia:</strong> {{ $programaAuditoria->vigencia }}
                    </p>
                    <p class="small text-muted mb-0">
                        <strong>Elaborado por:</strong> {{ $programaAuditoria->elaboradoPor->name }}
                    </p>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar"></i> Fechas
                    </h5>
                </div>
                <div class="card-body small">
                    <p class="mb-2">
                        <strong>Creado:</strong><br>
                        {{ $programaAuditoria->created_at->format('d/m/Y H:i') }}
                    </p>
                    @if ($programaAuditoria->updated_at != $programaAuditoria->created_at)
                        <p class="mb-2">
                            <strong>Modificado:</strong><br>
                            {{ $programaAuditoria->updated_at->format('d/m/Y H:i') }}
                        </p>
                    @endif
                    @if ($programaAuditoria->fecha_aprobacion)
                        <p class="mb-0">
                            <strong>Aprobado:</strong><br>
                            {{ \Carbon\Carbon::parse($programaAuditoria->fecha_aprobacion)->format('d/m/Y') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

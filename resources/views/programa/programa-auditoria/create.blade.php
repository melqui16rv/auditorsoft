@extends('layouts.app')

@section('title', 'Nuevo Programa de Auditoría')

@section('content')
<div class="container-fluid">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-plus-circle text-primary"></i> Nuevo Programa de Auditoría
                    </h1>
                    <small class="text-muted">Crear programa a partir de matriz de priorización - RF 3.3</small>
                </div>
                @if(!(auth()->user()->role === 'jefe_auditor' || auth()->user()->role === 'super_administrador'))
                    <div class="alert alert-danger mb-0" role="alert">
                        <i class="fas fa-lock"></i> No tienes permisos para crear programas
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Validar permiso de acceso -->
    @if(!(auth()->user()->role === 'jefe_auditor' || auth()->user()->role === 'super_administrador'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Acceso denegado</strong>
            <p class="mb-0">Solo Jefes de Control Interno y Super Administradores pueden crear programas de auditoría.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <a href="{{ route('programa-auditoria.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Programas
        </a>
    @else
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-file-upload"></i> Datos del Nuevo Programa
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-exclamation-circle"></i> Errores de validación:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('programa-auditoria.store') }}">
                            @csrf

                            <!-- Selección de Matriz -->
                            <div class="mb-4">
                                <label for="matriz_priorizacion_id" class="form-label">
                                    <strong><i class="fas fa-cube"></i> Matriz de Priorización <span class="text-danger">*</span></strong>
                                </label>
                                <select class="form-select form-select-lg @error('matriz_priorizacion_id') is-invalid @enderror"
                                    id="matriz_priorizacion_id" name="matriz_priorizacion_id" required>
                                    <option value="">-- Seleccionar Matriz --</option>
                                    @foreach ($matrices as $m)
                                        <option value="{{ $m->id }}"
                                            {{ old('matriz_priorizacion_id') == $m->id ? 'selected' : '' }}>
                                            {{ $m->codigo }} - {{ $m->nombre }} (Vigencia {{ $m->vigencia }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('matriz_priorizacion_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted d-block mt-2">
                                    <i class="fas fa-info-circle"></i> Solo matrices aprobadas están disponibles. 
                                    Los procesos se copiarán automáticamente.
                                </small>
                            </div>

                            <!-- Nombre del Programa -->
                            <div class="mb-3">
                                <label for="nombre" class="form-label">
                                    <strong><i class="fas fa-heading"></i> Nombre del Programa <span class="text-danger">*</span></strong>
                                </label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                    id="nombre" name="nombre" maxlength="200" placeholder="Programa de Auditoría 2025"
                                    value="{{ old('nombre') }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fechas -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fecha_inicio_programacion" class="form-label">
                                            <strong><i class="fas fa-calendar-alt"></i> Fecha Inicio <span class="text-danger">*</span></strong>
                                        </label>
                                        <input type="date"
                                            class="form-control @error('fecha_inicio_programacion') is-invalid @enderror"
                                            id="fecha_inicio_programacion" name="fecha_inicio_programacion"
                                            value="{{ old('fecha_inicio_programacion') }}" required>
                                        @error('fecha_inicio_programacion')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fecha_fin_programacion" class="form-label">
                                            <strong><i class="fas fa-calendar-alt"></i> Fecha Fin <span class="text-danger">*</span></strong>
                                        </label>
                                        <input type="date"
                                            class="form-control @error('fecha_fin_programacion') is-invalid @enderror"
                                            id="fecha_fin_programacion" name="fecha_fin_programacion"
                                            value="{{ old('fecha_fin_programacion') }}" required>
                                        @error('fecha_fin_programacion')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Número de Auditores -->
                            <div class="mb-3">
                                <label for="numero_auditores" class="form-label">
                                    <strong><i class="fas fa-users"></i> Número de Auditores</strong>
                                </label>
                                <input type="number" class="form-control @error('numero_auditores') is-invalid @enderror"
                                    id="numero_auditores" name="numero_auditores" min="1" max="50"
                                    value="{{ old('numero_auditores') }}">
                                @error('numero_auditores')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Objetivos -->
                            <div class="mb-3">
                                <label for="objetivos" class="form-label">
                                    <strong><i class="fas fa-bullseye"></i> Objetivos</strong>
                                </label>
                                <textarea class="form-control @error('objetivos') is-invalid @enderror"
                                    id="objetivos" name="objetivos" rows="3" maxlength="2000"
                                    placeholder="Describe los objetivos generales del programa...">{{ old('objetivos') }}</textarea>
                                @error('objetivos')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Máximo 2000 caracteres</small>
                            </div>

                            <!-- Alcance -->
                            <div class="mb-4">
                                <label for="alcance" class="form-label">
                                    <strong><i class="fas fa-expand"></i> Alcance</strong>
                                </label>
                                <textarea class="form-control @error('alcance') is-invalid @enderror"
                                    id="alcance" name="alcance" rows="3" maxlength="2000"
                                    placeholder="Define el alcance de la auditoría...">{{ old('alcance') }}</textarea>
                                @error('alcance')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Máximo 2000 caracteres</small>
                            </div>

                            <!-- Botones -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Crear Programa
                                </button>
                                <a href="{{ route('programa-auditoria.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Panel de Información -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle"></i> Guía de Creación
                        </h5>
                    </div>
                    <div class="card-body">
                        <ol class="ps-3 small mb-0">
                            <li class="mb-3">
                                <strong>Selecciona una Matriz</strong>
                                <small class="d-block text-muted">Solo matrices aprobadas por el Comité ICCCI están disponibles</small>
                            </li>
                            <li class="mb-3">
                                <strong>Los procesos se copian automáticamente</strong>
                                <small class="d-block text-muted">El sistema traslada los procesos de la matriz priorizados</small>
                            </li>
                            <li class="mb-3">
                                <strong>Completa los datos generales</strong>
                                <small class="d-block text-muted">Nombre, fechas, objetivos y alcance del programa</small>
                            </li>
                            <li class="mb-3">
                                <strong>El programa se crea en "Elaboración"</strong>
                                <small class="d-block text-muted">Podrás editarlo hasta que lo envíes a aprobación</small>
                            </li>
                            <li>
                                <strong>Envía a aprobación cuando esté listo</strong>
                                <small class="d-block text-muted">El Comité ICCCI revisará y aprobará el programa</small>
                            </li>
                        </ol>
                    </div>
                </div>

                <div class="card bg-light">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-tasks"></i> Requerimientos Normales
                        </h5>
                    </div>
                    <div class="card-body small">
                        <ul class="mb-0 ps-3">
                            <li>RF 3.3: Registro del programa de auditoría</li>
                            <li>RF 3.4: Validación de correspondencia</li>
                            <li>RF 3.5: Registro de aprobación</li>
                            <li>RF 3.6: Visualización y exportación</li>
                        </ul>
                    </div>
                </div>

                <div class="card mt-4 border-warning">
                    <div class="card-body">
                        <p class="small mb-0">
                            <i class="fas fa-shield-alt"></i> 
                            <strong>Estado:</strong> Elaboración
                            <span class="badge bg-warning text-dark ms-2">Editable</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

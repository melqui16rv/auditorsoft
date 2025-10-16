@extends('layouts.app')

@section('title', 'Plan Anual de Auditoría (PAA)')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('page-title', 'Planes de Auditoría (PAA)')

@section('content')
<div class="container-fluid py-4">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-calendar3"></i> Plan Anual de Auditoría (PAA)
            </h1>
            <p class="text-muted mb-0">Formato FR-GCE-001 - Decreto 648/2017</p>
        </div>
        @if(in_array(auth()->user()->role, ['jefe_auditor', 'super_administrador']))
            <a href="{{ route('paa.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Crear Nuevo PAA
            </a>
        @endif
    </div>

    <!-- Mensajes de éxito/error -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('paa.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="vigencia" class="form-label">Vigencia</label>
                    <select name="vigencia" id="vigencia" class="form-select">
                        <option value="">Todas las vigencias</option>
                        @foreach($vigencias as $v)
                            <option value="{{ $v }}" {{ request('vigencia') == $v ? 'selected' : '' }}>
                                {{ $v }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="borrador" {{ request('estado') == 'borrador' ? 'selected' : '' }}>Borrador</option>
                        <option value="en_ejecucion" {{ request('estado') == 'en_ejecucion' ? 'selected' : '' }}>En Ejecución</option>
                        <option value="finalizado" {{ request('estado') == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                        <option value="anulado" {{ request('estado') == 'anulado' ? 'selected' : '' }}>Anulado</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="buscar" class="form-label">Buscar</label>
                    <input type="text" name="buscar" id="buscar" class="form-control" 
                           placeholder="Código o entidad..." value="{{ request('buscar') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Listado de PAAs -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Listado de PAAs ({{ $paas->total() }})
            </h6>
        </div>
        <div class="card-body">
            @if($paas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Vigencia</th>
                                <th>Entidad</th>
                                <th>Fecha Elaboración</th>
                                <th>Jefe OCI</th>
                                <th>Estado</th>
                                <th>Cumplimiento</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paas as $paa)
                                <tr>
                                    <td>
                                        <strong>{{ $paa->codigo }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $paa->vigencia }}</span>
                                    </td>
                                    <td>{{ $paa->nombre_entidad ?? 'N/A' }}</td>
                                    <td>{{ $paa->fecha_elaboracion->format('d/m/Y') }}</td>
                                    <td>{{ $paa->elaboradoPor->name ?? 'N/A' }}</td>
                                    <td>{!! $paa->estado_badge !!}</td>
                                    <td>
                                        @php
                                            $porcentaje = $paa->calcularPorcentajeCumplimiento();
                                            $colorBarra = $porcentaje >= 80 ? 'success' : ($porcentaje >= 50 ? 'warning' : 'danger');
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $colorBarra }}" role="progressbar" 
                                                 style="width: {{ $porcentaje }}%" 
                                                 aria-valuenow="{{ $porcentaje }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ number_format($porcentaje, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('paa.show', $paa) }}" 
                                               class="btn btn-info" 
                                               title="Ver detalle">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            
                                            @if($paa->puedeSerEditado() && in_array(auth()->user()->role, ['jefe_auditor', 'super_administrador']))
                                                <a href="{{ route('paa.edit', $paa) }}" 
                                                   class="btn btn-warning" 
                                                   title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endif
                                            
                                            <a href="{{ route('paa.pdf', $paa) }}" 
                                               class="btn btn-danger" 
                                               title="Descargar PDF">
                                                <i class="bi bi-file-pdf"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="mt-3">
                    {{ $paas->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">No se encontraron PAAs</p>
                    @if(in_array(auth()->user()->role, ['jefe_auditor', 'super_administrador']))
                        <a href="{{ route('paa.create') }}" class="btn btn-primary mt-2">
                            <i class="bi bi-plus-circle"></i> Crear Primer PAA
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table td, .table th {
        vertical-align: middle;
    }
    .progress {
        background-color: #e9ecef;
    }
    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endpush

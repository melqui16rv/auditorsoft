@extends('layouts.app')

@section('title', $matriz ? 'Editar Matriz' : 'Nueva Matriz de Priorización')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 text-dark">
                <i class="fas fa-chart-bar"></i>
                {{ $matriz ? 'Editar Matriz' : 'Nueva Matriz de Priorización' }}
            </h1>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong><i class="fas fa-exclamation-circle"></i> Errores en el formulario:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST"
        action="{{ $matriz ? route('matriz-priorizacion.update', $matriz) : route('matriz-priorizacion.store') }}"
        id="matrizForm">
        @csrf
        @if ($matriz)
            @method('PUT')
        @endif

        <!-- Encabezado -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Información General</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                            id="nombre" name="nombre" required
                            value="{{ old('nombre', $matriz?->nombre) }}"
                            placeholder="Ej: Matriz PAA 2025">
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="vigencia" class="form-label">Vigencia <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('vigencia') is-invalid @enderror"
                            id="vigencia" name="vigencia" required min="2020" max="2099"
                            value="{{ old('vigencia', $matriz?->vigencia ?? now()->year) }}">
                        @error('vigencia')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="municipio_id" class="form-label">Municipio <span class="text-danger">*</span></label>
                        <select class="form-select @error('municipio_id') is-invalid @enderror"
                            id="municipio_id" name="municipio_id" required>
                            <option value="">-- Seleccionar --</option>
                            @foreach ($municipios as $municipio)
                                <option value="{{ $municipio->id }}"
                                    {{ old('municipio_id', $matriz?->municipio_id) == $municipio->id ? 'selected' : '' }}>
                                    {{ $municipio->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('municipio_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="estado" class="form-label">Estado</label>
                        <input type="text" class="form-control" id="estado" readonly
                            value="{{ $matriz?->estado == 'borrador' ? 'Borrador' : ($matriz?->estado == 'validado' ? 'Validado' : 'Aprobado') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalles (Procesos) -->
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Procesos a Evaluar</h5>
                <button type="button" class="btn btn-sm btn-success" id="btnAgregarProceso"
                        data-procesos="{{ json_encode($procesos->map(fn($p) => ['id' => $p->id, 'nombre' => $p->nombre])->values()) }}"
                        data-contador="{{ $matriz ? $matriz->detalles->count() : 0 }}">
                    <i class="fas fa-plus"></i> Agregar Proceso
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" id="tablaProcesos">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40%;">Proceso</th>
                                <th style="width: 15%;">Riesgo</th>
                                <th style="width: 12%;">Ponderación</th>
                                <th style="width: 15%;">Ciclo Rotación</th>
                                <th style="width: 12%;">¿Auditar?</th>
                                <th style="width: 6%;">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="detallesBody">
                            @if ($matriz && $matriz->detalles->count() > 0)
                                @foreach ($matriz->detalles as $index => $detalle)
                                    <tr class="filaDetalle">
                                        <td>
                                            <select class="form-select form-select-sm procesos-select @error('procesos.' . $index . '.proceso_id') is-invalid @enderror"
                                                name="procesos[{{ $index }}][proceso_id]" required>
                                                <option value="">-- Seleccionar --</option>
                                                @foreach ($procesos as $proceso)
                                                    <option value="{{ $proceso->id }}"
                                                        {{ $detalle->proceso_id == $proceso->id ? 'selected' : '' }}>
                                                        {{ $proceso->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('procesos.' . $index . '.proceso_id')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm riesgo-select @error('procesos.' . $index . '.riesgo_nivel') is-invalid @enderror"
                                                name="procesos[{{ $index }}][riesgo_nivel]" required>
                                                <option value="">--</option>
                                                <option value="extremo" {{ $detalle->riesgo_nivel == 'extremo' ? 'selected' : '' }}>
                                                    Extremo
                                                </option>
                                                <option value="alto" {{ $detalle->riesgo_nivel == 'alto' ? 'selected' : '' }}>
                                                    Alto
                                                </option>
                                                <option value="moderado" {{ $detalle->riesgo_nivel == 'moderado' ? 'selected' : '' }}>
                                                    Moderado
                                                </option>
                                                <option value="bajo" {{ $detalle->riesgo_nivel == 'bajo' ? 'selected' : '' }}>
                                                    Bajo
                                                </option>
                                            </select>
                                            @error('procesos.' . $index . '.riesgo_nivel')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm ponderacion-input"
                                                readonly value="{{ $detalle->ponderacion_riesgo }}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm ciclo-input"
                                                readonly value="{{ ucfirst(str_replace('_', ' ', $detalle->ciclo_rotacion)) }}">
                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-check-input auditar-input"
                                                {{ $detalle->incluir_en_programa ? 'checked' : '' }} readonly>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger eliminarProceso">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    @if (!$matriz || $matriz->detalles->count() == 0)
                        <div class="text-center text-muted py-3" id="sinProcesos">
                            <p><i class="fas fa-inbox"></i> Sin procesos agregados</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="mt-4 d-flex gap-2 justify-content-between">
            <div>
                <a href="{{ route('matriz-priorizacion.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
            <div>
                @if ($matriz && $matriz->estado == 'borrador')
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                @elseif (!$matriz)
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Matriz
                    </button>
                @endif
            </div>
        </div>
    </form>
</div>

<script>
    const btnAgregar = document.getElementById('btnAgregarProceso');
    let contadorFilas = parseInt(btnAgregar.getAttribute('data-contador') || '0');
    
    // Procesos disponibles para el select
    const procesosOptions = JSON.parse(btnAgregar.getAttribute('data-procesos') || '[]');

    // Mapa de riesgos a ponderación
    const riesgoPonderacion = {
        'extremo': 5,
        'alto': 4,
        'moderado': 3,
        'bajo': 2
    };

    const riesgoCiclo = {
        'extremo': 'Anual',
        'alto': '2 años',
        'moderado': '3 años',
        'bajo': 'No auditar'
    };

    // Agregar fila de proceso
    document.getElementById('btnAgregarProceso').addEventListener('click', function () {
        const tbody = document.getElementById('detallesBody');
        const sinProcesos = document.getElementById('sinProcesos');

        const indice = contadorFilas++;

        const fila = document.createElement('tr');
        fila.className = 'filaDetalle';
        
        // Generar opciones de procesos
        let procesosOptionsHtml = '<option value="">-- Seleccionar --</option>';
        procesosOptions.forEach(proceso => {
            procesosOptionsHtml += `<option value="${proceso.id}">${proceso.nombre}</option>`;
        });
        
        fila.innerHTML = `
            <td>
                <select class="form-select form-select-sm procesos-select"
                    name="procesos[${indice}][proceso_id]" required>
                    ${procesosOptionsHtml}
                </select>
            </td>
            <td>
                <select class="form-select form-select-sm riesgo-select"
                    name="procesos[${indice}][riesgo_nivel]" required>
                    <option value="">--</option>
                    <option value="extremo">Extremo</option>
                    <option value="alto">Alto</option>
                    <option value="moderado">Moderado</option>
                    <option value="bajo">Bajo</option>
                </select>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm ponderacion-input" readonly>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm ciclo-input" readonly>
            </td>
            <td>
                <input type="checkbox" class="form-check-input auditar-input" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger eliminarProceso">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(fila);
        if (sinProcesos) sinProcesos.style.display = 'none';

        // Agregar evento al riesgo-select
        fila.querySelector('.riesgo-select').addEventListener('change', calcularAuto);
        fila.querySelector('.eliminarProceso').addEventListener('click', function () {
            fila.remove();
            if (tbody.querySelectorAll('tr').length === 0 && sinProcesos) {
                sinProcesos.style.display = 'block';
            }
        });
    });

    // Calcular automáticamente
    function calcularAuto(e) {
        const fila = e.target.closest('tr');
        const riesgo = e.target.value;
        const ponderacionInput = fila.querySelector('.ponderacion-input');
        const cicloInput = fila.querySelector('.ciclo-input');
        const auditarInput = fila.querySelector('.auditar-input');

        if (riesgo) {
            ponderacionInput.value = riesgoPonderacion[riesgo];
            cicloInput.value = riesgoCiclo[riesgo];
            auditarInput.checked = riesgo !== 'bajo';
        } else {
            ponderacionInput.value = '';
            cicloInput.value = '';
            auditarInput.checked = false;
        }
    }

    // Eventos iniciales en filas existentes
    document.querySelectorAll('.riesgo-select').forEach(select => {
        select.addEventListener('change', calcularAuto);
    });

    document.querySelectorAll('.eliminarProceso').forEach(btn => {
        btn.addEventListener('click', function () {
            const fila = this.closest('tr');
            const tbody = document.getElementById('detallesBody');
            const sinProcesos = document.getElementById('sinProcesos');
            fila.remove();
            if (tbody.querySelectorAll('tr').length === 0 && sinProcesos) {
                sinProcesos.style.display = 'block';
            }
        });
    });

    // Validar que tenga procesos antes de enviar
    document.getElementById('matrizForm').addEventListener('submit', function (e) {
        const filas = document.querySelectorAll('.filaDetalle');
        if (filas.length === 0) {
            e.preventDefault();
            alert('Debe agregar al menos un proceso a la matriz');
        }
    });
</script>
@endsection

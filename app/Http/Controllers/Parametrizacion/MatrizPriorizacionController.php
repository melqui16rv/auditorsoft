<?php

namespace App\Http\Controllers\Parametrizacion;

use App\Http\Controllers\Controller;
use App\Models\MatrizPriorizacion;
use App\Models\MatrizPriorizacionDetalle;
use App\Models\Proceso;
use App\Models\MunicipioColombia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controlador para Matriz de Priorización del Universo de Auditoría
 * RF 3.1
 */
class MatrizPriorizacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!in_array(auth()->user()->role, ['super_administrador', 'jefe_auditor'])) {
                abort(403, 'Solo Super Admin y Jefe Auditor pueden acceder a Matriz de Priorización.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of matrices
     */
    public function index(Request $request)
    {
        $query = MatrizPriorizacion::with('municipio', 'elaboradoPor', 'detalles')
            ->orderBy('vigencia', 'desc')
            ->orderBy('fecha_elaboracion', 'desc');

        // Filtros
        if ($request->vigencia) {
            $query->where('vigencia', $request->vigencia);
        }

        if ($request->estado) {
            $query->where('estado', $request->estado);
        }

        if ($request->search) {
            $query->where('nombre', 'like', "%{$request->search}%")
                ->orWhere('codigo', 'like', "%{$request->search}%");
        }

        $matrices = $query->paginate(15);

        $vigencias = MatrizPriorizacion::selectRaw('DISTINCT vigencia')
            ->orderBy('vigencia', 'desc')
            ->pluck('vigencia');

        return view('parametrizacion.matriz-priorizacion.index', compact('matrices', 'vigencias'));
    }

    /**
     * Show the form for creating a new matrix
     */
    public function create()
    {
        $matriz = null;
        $procesos = Proceso::where('auditable', true)
            ->where('activo', true)
            ->orderBy('tipo_proceso')
            ->orderBy('nombre')
            ->get();

        $municipios = MunicipioColombia::orderBy('nombre')->get();

        return view('parametrizacion.matriz-priorizacion.create', compact(
            'matriz',
            'procesos',
            'municipios'
        ));
    }

    /**
     * Store a newly created matrix in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:200',
            'vigencia' => 'required|integer|between:2020,2050',
            'municipio_id' => 'required|exists:cat_municipios_colombia,id',
            'procesos' => 'required|array|min:1',
            'procesos.*.proceso_id' => 'required|exists:cat_procesos,id',
            'procesos.*.riesgo_nivel' => 'required|in:extremo,alto,moderado,bajo',
        ]);

        DB::beginTransaction();

        try {
            $codigo = MatrizPriorizacion::generarCodigo($validated['vigencia']);

            $matriz = MatrizPriorizacion::create([
                'codigo' => $codigo,
                'nombre' => $validated['nombre'],
                'vigencia' => $validated['vigencia'],
                'municipio_id' => $validated['municipio_id'],
                'fecha_elaboracion' => now()->date(),
                'elaborado_por' => auth()->id(),
                'estado' => MatrizPriorizacion::ESTADO_BORRADOR,
                'created_by' => auth()->id(),
            ]);

            // Crear detalles
            foreach ($validated['procesos'] as $detalle) {
                MatrizPriorizacionDetalle::create([
                    'matriz_priorizacion_id' => $matriz->id,
                    'proceso_id' => $detalle['proceso_id'],
                    'riesgo_nivel' => $detalle['riesgo_nivel'],
                    // Los cálculos se hacen automáticamente en el boot del modelo
                ]);
            }

            DB::commit();

            return redirect()->route('matriz-priorizacion.show', $matriz)
                ->with('success', "Matriz {$codigo} creada exitosamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al crear matriz: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified matrix
     */
    public function show(MatrizPriorizacion $matriz)
    {
        $matriz->load('municipio', 'elaboradoPor', 'aprobadoPor', 'actualizadoPor', 'detalles.proceso');

        return view('parametrizacion.matriz-priorizacion.show', compact('matriz'));
    }

    /**
     * Show the form for editing the specified matrix
     */
    public function edit(MatrizPriorizacion $matriz)
    {
        if (!$matriz->puedeSerEditada()) {
            return back()->with('error', 'Esta matriz no puede ser editada en su estado actual.');
        }

        $matriz->load('detalles');
        $procesos = Proceso::where('auditable', true)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $municipios = MunicipioColombia::orderBy('nombre')->get();

        return view('parametrizacion.matriz-priorizacion.create', compact('matriz', 'procesos', 'municipios'));
    }

    /**
     * Update the specified matrix in storage
     */
    public function update(Request $request, MatrizPriorizacion $matrizPriorizacion)
    {
        if (!$matrizPriorizacion->puedeSerEditada()) {
            return back()->with('error', 'Esta matriz no puede ser editada en su estado actual.');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:200',
            'municipio_id' => 'required|exists:cat_municipios_colombia,id',
            'procesos' => 'required|array|min:1',
            'procesos.*.proceso_id' => 'required|exists:cat_procesos,id',
            'procesos.*.riesgo_nivel' => 'required|in:extremo,alto,moderado,bajo',
        ]);

        DB::beginTransaction();

        try {
            $matrizPriorizacion->update([
                'nombre' => $validated['nombre'],
                'municipio_id' => $validated['municipio_id'],
                'updated_by' => auth()->id(),
            ]);

            // Obtener IDs de procesos actuales
            $procesosActuales = $matrizPriorizacion->detalles()
                ->pluck('id', 'proceso_id')
                ->toArray();

            $procesosNuevos = collect($validated['procesos'])
                ->keyBy('proceso_id')
                ->toArray();

            // Eliminar detalles que no están en la actualización
            foreach ($procesosActuales as $procesoId => $detalleId) {
                if (!isset($procesosNuevos[$procesoId])) {
                    MatrizPriorizacionDetalle::find($detalleId)->delete();
                }
            }

            // Crear o actualizar detalles
            foreach ($validated['procesos'] as $detalle) {
                MatrizPriorizacionDetalle::updateOrCreate(
                    [
                        'matriz_priorizacion_id' => $matrizPriorizacion->id,
                        'proceso_id' => $detalle['proceso_id'],
                    ],
                    [
                        'riesgo_nivel' => $detalle['riesgo_nivel'],
                    ]
                );
            }

            DB::commit();

            return redirect()->route('matriz-priorizacion.show', $matrizPriorizacion)
                ->with('success', 'Matriz actualizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar matriz: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified matrix from storage
     */
    public function destroy(MatrizPriorizacion $matrizPriorizacion)
    {
        if (auth()->user()->role !== 'super_administrador') {
            return back()->with('error', 'Solo Super Admin puede eliminar matrices.');
        }

        if ($matrizPriorizacion->estado !== MatrizPriorizacion::ESTADO_BORRADOR) {
            return back()->with('error', 'Solo se pueden eliminar matrices en estado borrador.');
        }

        DB::beginTransaction();

        try {
            $codigo = $matrizPriorizacion->codigo;
            $matrizPriorizacion->deleted_by = auth()->id();
            $matrizPriorizacion->save();
            $matrizPriorizacion->delete();

            DB::commit();

            return redirect()->route('matriz-priorizacion.index')
                ->with('success', "Matriz {$codigo} eliminada exitosamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar matriz: ' . $e->getMessage());
        }
    }

    /**
     * Validar y cambiar a estado "validado"
     */
    public function validar(MatrizPriorizacion $matriz)
    {
        if (!$matriz->puedeSerAprobada()) {
            return back()->with('error', 'La matriz debe estar en borrador y tener al menos un proceso.');
        }

        $matriz->update([
            'estado' => MatrizPriorizacion::ESTADO_VALIDADO,
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Matriz validada exitosamente.');
    }

    /**
     * Aprobar matriz (solo Jefe Auditor)
     */
    public function aprobar(Request $request, MatrizPriorizacion $matriz)
    {
        if (auth()->user()->role !== 'jefe_auditor' && auth()->user()->role !== 'super_administrador') {
            return back()->with('error', 'Solo Jefe Auditor puede aprobar matrices.');
        }

        if ($matriz->estado !== MatrizPriorizacion::ESTADO_VALIDADO) {
            return back()->with('error', 'La matriz debe estar validada para ser aprobada.');
        }

        $matriz->update([
            'estado' => MatrizPriorizacion::ESTADO_APROBADO,
            'fecha_aprobacion' => now(),
            'aprobado_por_id' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Matriz aprobada exitosamente.');
    }
}

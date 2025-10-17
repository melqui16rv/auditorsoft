<?php

namespace App\Http\Controllers\Programa;

use App\Http\Controllers\Controller;
use App\Models\ProgramaAuditoria;
use App\Models\ProgramaAuditoriaDetalle;
use App\Models\MatrizPriorizacion;
use App\Models\Area;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgramaAuditoriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!in_array(auth()->user()->role, ['super_administrador', 'jefe_auditor'])) {
                abort(403, 'Solo Jefe Auditor y Super Admin pueden acceder a Programas de Auditoría.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of programs
     */
    public function index(Request $request)
    {
        $query = ProgramaAuditoria::with('matrizPriorizacion', 'elaboradoPor', 'detalles')
            ->orderBy('vigencia', 'desc')
            ->orderBy('created_at', 'desc');

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

        $programas = $query->paginate(15);

        $vigencias = ProgramaAuditoria::selectRaw('DISTINCT vigencia')
            ->orderBy('vigencia', 'desc')
            ->pluck('vigencia');

        return view('programa.programa-auditoria.index', compact('programas', 'vigencias'));
    }

    /**
     * Show the form for creating a new program (from Matriz)
     */
    public function create(Request $request)
    {
        // Obtener matrices aprobadas disponibles
        $matrices = MatrizPriorizacion::where('estado', 'aprobado')
            ->with('municipio', 'detalles')
            ->orderBy('vigencia', 'desc')
            ->get();

        // Si viene de una matriz específica
        $matriz = null;
        if ($request->matriz_id) {
            $matriz = MatrizPriorizacion::find($request->matriz_id);
        }

        $areas = Area::where('activo', true)->orderBy('nombre')->get();
        $auditores = User::where('role', 'auditor')
            ->where('activo', true)
            ->orderBy('name')
            ->get();

        return view('programa.programa-auditoria.create', compact(
            'matrices',
            'matriz',
            'areas',
            'auditores'
        ));
    }

    /**
     * Store a newly created program in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:200',
            'matriz_priorizacion_id' => 'required|exists:matriz_priorizacion,id',
            'fecha_inicio_programacion' => 'required|date',
            'fecha_fin_programacion' => 'required|date|after:fecha_inicio_programacion',
            'numero_auditores' => 'nullable|integer|min:1',
            'objetivos' => 'nullable|string',
            'alcance' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $matriz = MatrizPriorizacion::findOrFail($validated['matriz_priorizacion_id']);

            if ($matriz->estado !== 'aprobado') {
                return back()->with('error', 'Solo se pueden crear programas desde matrices aprobadas.');
            }

            $codigo = ProgramaAuditoria::generarCodigo($matriz->vigencia);

            $programa = ProgramaAuditoria::create([
                'codigo' => $codigo,
                'nombre' => $validated['nombre'],
                'vigencia' => $matriz->vigencia,
                'matriz_priorizacion_id' => $matriz->id,
                'elaborado_por' => auth()->id(),
                'estado' => ProgramaAuditoria::ESTADO_ELABORACION,
                'fecha_inicio_programacion' => $validated['fecha_inicio_programacion'],
                'fecha_fin_programacion' => $validated['fecha_fin_programacion'],
                'numero_auditores' => $validated['numero_auditores'],
                'objetivos' => $validated['objetivos'],
                'alcance' => $validated['alcance'],
                'created_by' => auth()->id(),
            ]);

            // Copiar detalles de Matriz (procesos con incluir_en_programa = true)
            foreach ($matriz->detalles()->where('incluir_en_programa', true)->get() as $matrizDetalle) {
                ProgramaAuditoriaDetalle::create([
                    'programa_auditoria_id' => $programa->id,
                    'matriz_priorizacion_detalle_id' => $matrizDetalle->id,
                    'proceso_id' => $matrizDetalle->proceso_id,
                    'riesgo_nivel' => $matrizDetalle->riesgo_nivel,
                    'ponderacion_riesgo' => $matrizDetalle->ponderacion_riesgo,
                    'ciclo_rotacion' => $matrizDetalle->ciclo_rotacion,
                    'estado_auditoria' => 'programado',
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return redirect()->route('programa-auditoria.show', $programa)
                ->with('success', "Programa {$codigo} creado exitosamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al crear programa: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified program
     */
    public function show(ProgramaAuditoria $programaAuditoria)
    {
        $programaAuditoria->load(
            'matrizPriorizacion.municipio',
            'elaboradoPor',
            'aprobadoPor',
            'actualizadoPor',
            'detalles.proceso',
            'detalles.auditorResponsable'
        );

        return view('programa.programa-auditoria.show', compact('programaAuditoria'));
    }

    /**
     * Show the form for editing the specified program
     */
    public function edit(ProgramaAuditoria $programaAuditoria)
    {
        if (!$programaAuditoria->puedeSerEditado()) {
            return back()->with('error', 'Este programa no puede ser editado en su estado actual.');
        }

        $programaAuditoria->load('detalles');
        $areas = Area::where('activo', true)->orderBy('nombre')->get();
        $auditores = User::where('role', 'auditor')
            ->where('activo', true)
            ->orderBy('name')
            ->get();

        return view('programa.programa-auditoria.edit', compact('programaAuditoria', 'areas', 'auditores'));
    }

    /**
     * Update the specified program in storage
     */
    public function update(Request $request, ProgramaAuditoria $programaAuditoria)
    {
        if (!$programaAuditoria->puedeSerEditado()) {
            return back()->with('error', 'Este programa no puede ser editado en su estado actual.');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:200',
            'fecha_inicio_programacion' => 'required|date',
            'fecha_fin_programacion' => 'required|date|after:fecha_inicio_programacion',
            'numero_auditores' => 'nullable|integer|min:1',
            'objetivos' => 'nullable|string',
            'alcance' => 'nullable|string',
        ]);

        $programaAuditoria->update([
            'nombre' => $validated['nombre'],
            'fecha_inicio_programacion' => $validated['fecha_inicio_programacion'],
            'fecha_fin_programacion' => $validated['fecha_fin_programacion'],
            'numero_auditores' => $validated['numero_auditores'],
            'objetivos' => $validated['objetivos'],
            'alcance' => $validated['alcance'],
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('programa-auditoria.show', $programaAuditoria)
            ->with('success', 'Programa actualizado exitosamente.');
    }

    /**
     * Remove the specified program from storage
     */
    public function destroy(ProgramaAuditoria $programaAuditoria)
    {
        if (!$programaAuditoria->puedeSerEditado()) {
            return back()->with('error', 'Solo se pueden eliminar programas en elaboración.');
        }

        DB::beginTransaction();

        try {
            $codigo = $programaAuditoria->codigo;
            $programaAuditoria->deleted_by = auth()->id();
            $programaAuditoria->save();
            $programaAuditoria->delete();

            DB::commit();

            return redirect()->route('programa-auditoria.index')
                ->with('success', "Programa {$codigo} eliminado exitosamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar programa: ' . $e->getMessage());
        }
    }

    /**
     * Enviar a aprobación
     */
    public function enviar(ProgramaAuditoria $programaAuditoria)
    {
        if (!$programaAuditoria->puedeSerEnviado()) {
            return back()->with('error', 'El programa no cumple con requisitos para enviar.');
        }

        $programaAuditoria->update([
            'estado' => ProgramaAuditoria::ESTADO_ENVIADO_APROBACION,
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Programa enviado a aprobación.');
    }

    /**
     * Aprobar programa (solo Super Admin)
     */
    public function aprobar(Request $request, ProgramaAuditoria $programaAuditoria)
    {
        if (auth()->user()->role !== 'super_administrador') {
            return back()->with('error', 'Solo Super Admin puede aprobar programas.');
        }

        if ($programaAuditoria->estado !== ProgramaAuditoria::ESTADO_ENVIADO_APROBACION) {
            return back()->with('error', 'El programa debe estar enviado a aprobación.');
        }

        $programaAuditoria->update([
            'estado' => ProgramaAuditoria::ESTADO_APROBADO,
            'fecha_aprobacion' => now()->toDateString(),
            'aprobado_por_id' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Programa aprobado exitosamente.');
    }
}

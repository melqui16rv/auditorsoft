<?php

namespace App\Http\Controllers\PAA;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePAARequest;
use App\Http\Requests\UpdatePAARequest;
use App\Models\PAA;
use App\Models\PAATarea;
use App\Models\MunicipioColombia;
use App\Models\RolOci;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

/**
 * Controlador para gestión del Plan Anual de Auditoría (PAA)
 * Formato FR-GCE-001
 */
class PAAController extends Controller
{
    /**
     * Constructor - Aplicar middleware de autenticación
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!in_array(auth()->user()->role, ['jefe_auditor', 'super_administrador', 'auditor'])) {
                abort(403, 'No tienes permisos para acceder a esta sección.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PAA::with(['elaboradoPor', 'municipio', 'tareas'])
            ->orderBy('vigencia', 'desc')
            ->orderBy('fecha_elaboracion', 'desc');

        // Si es auditor, solo mostrar PAAs donde tiene tareas asignadas
        if (auth()->user()->role === 'auditor') {
            $query->whereHas('tareas', function($q) {
                $q->where('auditor_responsable_id', auth()->id());
            });
        }

        // Filtrar por vigencia si se especifica
        if ($request->filled('vigencia')) {
            $query->where('vigencia', $request->vigencia);
        }

        // Filtrar por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtrar por búsqueda
        if ($request->filled('buscar')) {
            $search = $request->buscar;
            $query->where(function($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhere('nombre_entidad', 'like', "%{$search}%");
            });
        }

        $paas = $query->paginate(15);

        // Obtener años disponibles para el filtro
        $vigencias = PAA::select('vigencia')
            ->distinct()
            ->orderBy('vigencia', 'desc')
            ->pluck('vigencia');

        return view('paa.index', compact('paas', 'vigencias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $municipios = MunicipioColombia::orderBy('nombre')->get();
        $rolesOci = RolOci::orderBy('orden')->get();
        
        // Generar código automático para la vigencia actual
        $vigenciaActual = now()->year;
        $codigoSugerido = PAA::generarCodigo($vigenciaActual);

        return view('paa.create', compact('municipios', 'rolesOci', 'codigoSugerido', 'vigenciaActual'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePAARequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            
            // Generar código automático
            $data['codigo'] = PAA::generarCodigo($data['vigencia']);
            
            // Asignar Jefe OCI actual
            $data['elaborado_por'] = auth()->id();
            
            // Asignar usuario creador
            $data['created_by'] = auth()->id();
            
            // Estado inicial
            $data['estado'] = PAA::ESTADO_BORRADOR;
            
            // Manejar subida de imagen institucional
            if ($request->hasFile('imagen_institucional')) {
                $path = $request->file('imagen_institucional')->store('logos', 'public');
                $data['imagen_institucional_path'] = $path;
            }

            $paa = PAA::create($data);

            DB::commit();

            return redirect()
                ->route('paa.show', $paa)
                ->with('success', "PAA {$paa->codigo} creado exitosamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Error al crear el PAA: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PAA $paa)
    {
        $paa->load([
            'elaboradoPor',
            'municipio',
            'tareas.responsable',
            'tareas.seguimientos',
            'aprobadoPor'
        ]);

        // Si es auditor, verificar que tiene al menos una tarea asignada
        if (auth()->user()->role === 'auditor') {
            $tieneTareasAsignadas = $paa->tareas->where('auditor_responsable_id', auth()->id())->count() > 0;
            
            if (!$tieneTareasAsignadas) {
                return redirect()->route('paa.index')
                    ->with('info', 'No tienes acceso a ese PAA.');
            }

            // Filtrar solo las tareas del auditor
            $paa->setRelation('tareas', $paa->tareas->where('auditor_responsable_id', auth()->id()));
        }

        // Calcular estadísticas
        $porcentajeCumplimiento = $paa->calcularPorcentajeCumplimiento();
        
        // Para auditores, calcular cumplimiento solo con sus tareas filtradas
        if (auth()->user()->role === 'auditor') {
            $cumplimientoPorRol = $this->calcularCumplimientoPorRolFiltrado($paa);
        } else {
            $cumplimientoPorRol = $paa->calcularCumplimientoPorRol();
        }

        // Estadísticas de tareas (basadas en las tareas filtradas)
        $now = now();
        $estadisticas = [
            'total_tareas' => $paa->tareas->count(),
            'tareas_pendientes' => $paa->tareas->where('estado', 'pendiente')->count(),
            'tareas_en_proceso' => $paa->tareas->where('estado', 'en_proceso')->count(),
            'tareas_realizadas' => $paa->tareas->where('estado', 'realizada')->count(),
            'tareas_anuladas' => $paa->tareas->where('estado', 'anulada')->count(),
            'tareas_vencidas' => $paa->tareas->where('estado', '!=', 'realizada')
                                             ->where('estado', '!=', 'anulada')
                                             ->where('fecha_fin', '<', $now)
                                             ->count(),
        ];

        return view('paa.show', compact(
            'paa',
            'porcentajeCumplimiento',
            'cumplimientoPorRol',
            'estadisticas'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PAA $paa)
    {
        // Solo se puede editar si está en borrador o en ejecución
        if (!$paa->puedeSerEditado()) {
            return back()->with('error', 'Este PAA no puede ser editado en su estado actual.');
        }

        $municipios = MunicipioColombia::orderBy('nombre')->get();
        $rolesOci = RolOci::orderBy('orden')->get();

        return view('paa.edit', compact('paa', 'municipios', 'rolesOci'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePAARequest $request, PAA $paa)
    {
        // Verificar que puede ser editado
        if (!$paa->puedeSerEditado()) {
            return back()->with('error', 'Este PAA no puede ser editado en su estado actual.');
        }

        DB::beginTransaction();

        try {
            $data = $request->validated();
            
            // Asignar usuario actualizador
            $data['updated_by'] = auth()->id();
            
            // Manejar subida de nueva imagen institucional
            if ($request->hasFile('imagen_institucional')) {
                // Eliminar imagen anterior si existe
                if ($paa->imagen_institucional_path) {
                    Storage::disk('public')->delete($paa->imagen_institucional_path);
                }
                
                $path = $request->file('imagen_institucional')->store('logos', 'public');
                $data['imagen_institucional_path'] = $path;
            }

            $paa->update($data);

            DB::commit();

            return redirect()
                ->route('paa.show', $paa)
                ->with('success', 'PAA actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar el PAA: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PAA $paa)
    {
        // Solo Super Admin puede eliminar
        if (auth()->user()->role !== 'super_administrador') {
            return back()->with('error', 'Solo el Super Administrador puede eliminar PAAs.');
        }

        // Solo se puede eliminar si está en borrador
        if ($paa->estado !== PAA::ESTADO_BORRADOR) {
            return back()->with('error', 'Solo se pueden eliminar PAAs en estado borrador.');
        }

        DB::beginTransaction();

        try {
            // Eliminar imagen si existe
            if ($paa->imagen_institucional_path) {
                Storage::disk('public')->delete($paa->imagen_institucional_path);
            }

            $codigo = $paa->codigo;
            $paa->deleted_by = auth()->id();
            $paa->save();
            $paa->delete();

            DB::commit();

            return redirect()
                ->route('paa.index')
                ->with('success', "PAA {$codigo} eliminado exitosamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->with('error', 'Error al eliminar el PAA: ' . $e->getMessage());
        }
    }

    /**
     * Aprobar el PAA
     */
    public function aprobar(PAA $paa)
    {
        // Solo Jefe Auditor y Super Admin pueden aprobar
        if (!in_array(auth()->user()->role, ['jefe_auditor', 'super_administrador'])) {
            return back()->with('error', 'No tienes permisos para aprobar PAAs.');
        }

        if ($paa->estado !== PAA::ESTADO_BORRADOR) {
            return back()->with('error', 'Solo se pueden aprobar PAAs en estado borrador.');
        }

        DB::beginTransaction();

        try {
            $paa->aprobar(auth()->user());

            DB::commit();

            return back()->with('success', "PAA {$paa->codigo} aprobado y en ejecución.");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Error al aprobar el PAA: ' . $e->getMessage());
        }
    }

    /**
     * Finalizar el PAA
     */
    public function finalizar(PAA $paa)
    {
        // Solo Jefe Auditor y Super Admin pueden finalizar
        if (!in_array(auth()->user()->role, ['jefe_auditor', 'super_administrador'])) {
            return back()->with('error', 'No tienes permisos para finalizar PAAs.');
        }

        if ($paa->estado !== PAA::ESTADO_EN_EJECUCION) {
            return back()->with('error', 'Solo se pueden finalizar PAAs en ejecución.');
        }

        DB::beginTransaction();

        try {
            $paa->finalizar(auth()->user());

            DB::commit();

            return back()->with('success', "PAA {$paa->codigo} finalizado exitosamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Error al finalizar el PAA: ' . $e->getMessage());
        }
    }

    /**
     * Anular el PAA
     */
    public function anular(Request $request, PAA $paa)
    {
        // Solo Jefe Auditor y Super Admin pueden anular
        if (!in_array(auth()->user()->role, ['jefe_auditor', 'super_administrador'])) {
            return back()->with('error', 'No tienes permisos para anular PAAs.');
        }

        $request->validate([
            'motivo_anulacion' => 'required|string|min:10'
        ], [
            'motivo_anulacion.required' => 'Debe especificar el motivo de anulación.',
            'motivo_anulacion.min' => 'El motivo debe tener al menos 10 caracteres.'
        ]);

        DB::beginTransaction();

        try {
            $paa->anular(auth()->user(), $request->motivo_anulacion);

            DB::commit();

            return back()->with('success', "PAA {$paa->codigo} anulado.");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Error al anular el PAA: ' . $e->getMessage());
        }
    }

    /**
     * Calcular cumplimiento por rol usando solo las tareas filtradas (para auditores)
     */
    private function calcularCumplimientoPorRolFiltrado(PAA $paa): array
    {
        $rolesOci = RolOci::orderBy('orden')->get();
        $cumplimientoPorRol = [];

        foreach ($rolesOci as $rol) {
            // Usar la colección de tareas ya filtradas
            $tareasDelRol = $paa->tareas->where('rol_oci', $rol->nombre_rol);
            
            $totalTareasRol = $tareasDelRol->count();
            $tareasRealizadasRol = $tareasDelRol
                ->where('estado', PAATarea::ESTADO_REALIZADA)
                ->count();

            $porcentajeRol = $totalTareasRol > 0 
                ? round(($tareasRealizadasRol / $totalTareasRol) * 100, 2)
                : 0.0;

            // Usar el ID del rol como clave (igual que el método del modelo)
            $cumplimientoPorRol[$rol->id] = [
                'nombre' => $rol->nombre_rol,
                'porcentaje' => $porcentajeRol,
                'tareas_total' => $totalTareasRol,
                'tareas_realizadas' => $tareasRealizadasRol,
            ];
        }

        return $cumplimientoPorRol;
    }

    /**
     * Exportar PAA a PDF
     */
    public function exportarPdf(PAA $paa)
    {
        // TODO: Instalar el paquete barryvdh/laravel-dompdf para habilitar esta funcionalidad
        // composer require barryvdh/laravel-dompdf
        
        return back()->with('warning', 'La funcionalidad de exportar a PDF requiere instalar el paquete barryvdh/laravel-dompdf. Por favor, ejecute: composer require barryvdh/laravel-dompdf');
        
        /* DESCOMENTAR DESPUÉS DE INSTALAR EL PAQUETE:
        $paa->load([
            'elaboradoPor',
            'municipio',
            'tareas.rolOci',
            'tareas.responsable'
        ]);

        $porcentajeCumplimiento = $paa->calcularPorcentajeCumplimiento();
        $cumplimientoPorRol = $paa->calcularCumplimientoPorRol();

        $pdf = \PDF::loadView('paa.pdf', compact('paa', 'porcentajeCumplimiento', 'cumplimientoPorRol'));
        
        return $pdf->download("PAA-{$paa->codigo}.pdf");
        */
    }
}


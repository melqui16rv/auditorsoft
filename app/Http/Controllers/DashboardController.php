<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PAA;
use App\Models\PAATarea;
use App\Models\PAASeguimiento;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Controlador del Dashboard de Cumplimiento del PAA
 * 
 * Muestra indicadores, estadísticas y gráficos del Plan Anual de Auditoría
 * según el Decreto 648/2017 y roles de la Oficina de Control Interno
 */
class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard principal de cumplimiento del PAA
     */
    public function cumplimiento(Request $request)
    {
        // Obtener filtros
        $vigenciaFiltro = $request->input('vigencia', date('Y'));
        $estadoPAA = $request->input('estado_paa', 'all');

        // Query base para PAA
        $paaQuery = PAA::query();

        if ($vigenciaFiltro !== 'all') {
            $paaQuery->where('vigencia', $vigenciaFiltro);
        }

        if ($estadoPAA !== 'all') {
            $paaQuery->where('estado', $estadoPAA);
        }

        // Obtener PAAs filtrados
        $paas = $paaQuery->with(['tareas'])->get();
        $paaIds = $paas->pluck('id');

        // === INDICADORES GENERALES ===
        $totalPAAs = $paas->count();
        $totalTareas = PAATarea::whereIn('paa_id', $paaIds)->count();
        $tareasRealizadas = PAATarea::whereIn('paa_id', $paaIds)
            ->where('estado', 'realizada')
            ->count();
        $tareasPendientes = PAATarea::whereIn('paa_id', $paaIds)
            ->where('estado', 'pendiente')
            ->count();
        $tareasEnProceso = PAATarea::whereIn('paa_id', $paaIds)
            ->where('estado', 'en_proceso')
            ->count();
        $tareasAnuladas = PAATarea::whereIn('paa_id', $paaIds)
            ->whereNotNull('deleted_at')
            ->withTrashed()
            ->count();

        $porcentajeCumplimiento = $totalTareas > 0 
            ? round(($tareasRealizadas / $totalTareas) * 100, 2) 
            : 0;

        // === TAREAS POR ROL OCI (Decreto 648/2017) ===
        $tareasPorRol = PAATarea::whereIn('paa_id', $paaIds)
            ->select('rol_oci', DB::raw('count(*) as total'))
            ->groupBy('rol_oci')
            ->get()
            ->pluck('total', 'rol_oci');

        $tareasRealizadasPorRol = PAATarea::whereIn('paa_id', $paaIds)
            ->where('estado', 'realizada')
            ->select('rol_oci', DB::raw('count(*) as total'))
            ->groupBy('rol_oci')
            ->get()
            ->pluck('total', 'rol_oci');

        // Calcular porcentajes de cumplimiento por rol
        $rolesOCI = ['evaluacion_gestion', 'evaluacion_control', 'apoyo_fortalecimiento', 'fomento_cultura', 'investigaciones'];
        $cumplimientoPorRol = [];
        
        foreach ($rolesOCI as $rol) {
            $total = $tareasPorRol->get($rol, 0);
            $realizadas = $tareasRealizadasPorRol->get($rol, 0);
            $cumplimientoPorRol[$rol] = [
                'total' => $total,
                'realizadas' => $realizadas,
                'porcentaje' => $total > 0 ? round(($realizadas / $total) * 100, 2) : 0
            ];
        }

        // === TAREAS POR ESTADO ===
        $tareasPorEstado = [
            'pendiente' => $tareasPendientes,
            'en_proceso' => $tareasEnProceso,
            'realizada' => $tareasRealizadas,
            'anulada' => $tareasAnuladas,
        ];

        // === SEGUIMIENTOS ===
        $totalSeguimientos = PAASeguimiento::whereHas('tarea', function($q) use ($paaIds) {
            $q->whereIn('paa_id', $paaIds);
        })->count();

        $seguimientosRealizados = PAASeguimiento::whereHas('tarea', function($q) use ($paaIds) {
            $q->whereIn('paa_id', $paaIds);
        })->whereNotNull('fecha_realizacion')->count();

        $seguimientosPendientes = $totalSeguimientos - $seguimientosRealizados;

        // === ALERTAS DE VENCIMIENTO ===
        $hoy = Carbon::now();
        $proximoMes = Carbon::now()->addMonth();

        $tareasProximasVencer = PAATarea::whereIn('paa_id', $paaIds)
            ->where('estado', '!=', 'realizada')
            ->whereBetween('fecha_fin', [$hoy, $proximoMes])
            ->count();

        $tareasVencidas = PAATarea::whereIn('paa_id', $paaIds)
            ->where('estado', '!=', 'realizada')
            ->where('fecha_fin', '<', $hoy)
            ->count();

        // === TIMELINE DE SEGUIMIENTOS (últimos 10) ===
        $timelineSeguimientos = PAASeguimiento::whereHas('tarea', function($q) use ($paaIds) {
            $q->whereIn('paa_id', $paaIds);
        })
        ->with(['tarea.paa', 'enteControl', 'createdBy'])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

        // === VIGENCIAS DISPONIBLES ===
        $vigenciasDisponibles = PAA::select('vigencia')
            ->distinct()
            ->orderBy('vigencia', 'desc')
            ->pluck('vigencia');

        // === TAREAS POR TIPO ===
        $tareasPorTipo = PAATarea::whereIn('paa_id', $paaIds)
            ->select('tipo', DB::raw('count(*) as total'))
            ->groupBy('tipo')
            ->get()
            ->pluck('total', 'tipo');

        return view('jefe-auditor.dashboard', compact(
            'totalPAAs',
            'totalTareas',
            'tareasRealizadas',
            'tareasPendientes',
            'tareasEnProceso',
            'tareasAnuladas',
            'porcentajeCumplimiento',
            'cumplimientoPorRol',
            'tareasPorEstado',
            'totalSeguimientos',
            'seguimientosRealizados',
            'seguimientosPendientes',
            'tareasProximasVencer',
            'tareasVencidas',
            'timelineSeguimientos',
            'vigenciasDisponibles',
            'vigenciaFiltro',
            'estadoPAA',
            'tareasPorTipo'
        ));
    }

    /**
     * Dashboard general del sistema - Redirige según rol
     */
    public function index()
    {
        $user = auth()->user();

        // Redirigir a dashboard específico por rol
        return match($user->role) {
            'super_administrador' => redirect()->route('super-admin.dashboard'),
            'jefe_auditor' => redirect()->route('jefe-auditor.dashboard'),
            'auditor' => redirect()->route('auditor.dashboard'),
            'auditado' => redirect()->route('auditado.dashboard'),
            default => abort(403, 'Rol no autorizado')
        };
    }

    /**
     * Dashboard específico para auditados (NO USAR - Deprecado)
     * Usar en su lugar el método en AuditadoController
     */
    private function dashboardAuditado()
    {
        return redirect()->route('auditado.dashboard');
    }
}

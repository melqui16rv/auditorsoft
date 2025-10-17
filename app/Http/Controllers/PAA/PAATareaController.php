<?php

namespace App\Http\Controllers\PAA;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePAATareaRequest;
use App\Http\Requests\UpdatePAATareaRequest;
use App\Models\PAA;
use App\Models\PAATarea;
use App\Models\RolOci;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PAATareaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(PAA $paa)
    {
        // Verificar autorización básica
        if (!in_array(auth()->user()->role, ['jefe_auditor', 'auditor', 'super_administrador'])) {
            abort(403, 'No tienes permisos para ver las tareas de este PAA.');
        }

        // Construir query de tareas
        $query = $paa->tareas()
            ->with(['responsable', 'seguimientos'])
            ->orderBy('fecha_inicio');

        // Si es auditor, filtrar solo sus tareas asignadas
        if (auth()->user()->role === 'auditor') {
            $query->where('auditor_responsable_id', auth()->id());
        }

        $tareas = $query->paginate(15);

        // Si es auditor y no tiene tareas, mostrar mensaje
        if (auth()->user()->role === 'auditor' && $tareas->total() === 0) {
            return redirect()->route('paa.show', $paa)
                ->with('info', 'No tienes tareas asignadas en este PAA.');
        }

        return view('paa.tareas.index', compact('paa', 'tareas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(PAA $paa)
    {
        // Verificar autorización
        if (!in_array(auth()->user()->role, ['jefe_auditor', 'auditor', 'super_administrador'])) {
            abort(403, 'No tienes permisos para crear tareas en este PAA.');
        }

        // Verificar que el PAA puede ser editado
        if (!$paa->puedeSerEditado()) {
            return redirect()->route('paa.show', $paa)
                ->with('error', 'No se pueden agregar tareas a un PAA finalizado o anulado.');
        }

        // Obtener roles OCI y usuarios auditores
        $rolesOci = RolOci::orderBy('nombre_rol')->get();
        
        // Incluir Jefe Auditor, Auditor y Super Admin como posibles responsables
        // Además, asegurar que estén activos
        $responsables = User::whereIn('role', ['super_administrador', 'jefe_auditor', 'auditor'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('paa.tareas.create', compact('paa', 'rolesOci', 'responsables'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePAATareaRequest $request, PAA $paa)
    {
        // Verificar que el PAA puede ser editado
        if (!$paa->puedeSerEditado()) {
            return redirect()->route('paa.show', $paa)
                ->with('error', 'No se pueden agregar tareas a un PAA finalizado o anulado.');
        }

        DB::beginTransaction();
        try {
            // Crear la tarea
            $tarea = new PAATarea($request->validated());
            $tarea->paa_id = $paa->id;
            $tarea->estado = 'pendiente'; // Estado inicial
            $tarea->created_by = auth()->id();
            $tarea->save();

            DB::commit();

            return redirect()->route('paa.tareas.show', [$paa, $tarea])
                ->with('success', 'Tarea creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la tarea: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PAA $paa, PAATarea $tarea)
    {
        // Verificar que la tarea pertenece al PAA
        if ($tarea->paa_id !== $paa->id) {
            abort(404, 'Tarea no encontrada en este PAA.');
        }

        // Verificar autorización
        if (!in_array(auth()->user()->role, ['jefe_auditor', 'auditor', 'super_administrador'])) {
            abort(403, 'No tienes permisos para ver esta tarea.');
        }

        // Si es auditor, verificar que es el responsable de la tarea
        if (auth()->user()->role === 'auditor' && $tarea->auditor_responsable_id !== auth()->id()) {
            return redirect()->route('paa.show', $paa)
                ->with('info', 'No tienes acceso a esa tarea.');
        }

        // Cargar relaciones
        $tarea->load([
            'responsable',
            'seguimientos.evidencias',
            'seguimientos.enteControl',
            'createdBy',
            'updatedBy'
        ]);

        // Calcular estadísticas
        $totalSeguimientos = $tarea->seguimientos->count();
        $seguimientosRealizados = $tarea->seguimientos->whereNotNull('fecha_realizacion')->count();
        $seguimientosPendientes = $tarea->seguimientos->whereNull('fecha_realizacion')->count();
        $porcentajeSeguimientos = $totalSeguimientos > 0 
            ? ($seguimientosRealizados / $totalSeguimientos) * 100 
            : 0;

        // Calcular total de evidencias desde todos los seguimientos
        $totalEvidencias = $tarea->seguimientos->sum(function($seguimiento) {
            return $seguimiento->evidencias->count();
        });

        // Obtener seguimientos para la vista
        $seguimientos = $tarea->seguimientos;

        // Obtener todas las evidencias de todos los seguimientos
        $evidencias = collect();
        foreach ($tarea->seguimientos as $seguimiento) {
            $evidencias = $evidencias->merge($seguimiento->evidencias);
        }

        return view('paa.tareas.show', compact('paa', 'tarea', 'seguimientos', 'totalSeguimientos', 'seguimientosRealizados', 'seguimientosPendientes', 'porcentajeSeguimientos', 'totalEvidencias', 'evidencias'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PAA $paa, PAATarea $tarea)
    {
        // Verificar que la tarea pertenece al PAA
        if ($tarea->paa_id !== $paa->id) {
            abort(404, 'Tarea no encontrada en este PAA.');
        }

        // Verificar autorización
        if (!in_array(auth()->user()->role, ['jefe_auditor', 'auditor', 'super_administrador'])) {
            abort(403, 'No tienes permisos para editar esta tarea.');
        }

        // Si es auditor, verificar que es el responsable de la tarea
        if (auth()->user()->role === 'auditor' && $tarea->auditor_responsable_id !== auth()->id()) {
            return redirect()->route('paa.show', $paa)
                ->with('info', 'No tienes acceso a esa tarea.');
        }

        // Verificar que el PAA puede ser editado
        if (!$paa->puedeSerEditado()) {
            return redirect()->route('paa.tareas.show', [$paa, $tarea])
                ->with('error', 'No se pueden editar tareas de un PAA finalizado o anulado.');
        }

        // Obtener roles OCI y usuarios auditores
        $rolesOci = RolOci::orderBy('nombre_rol')->get();
        $responsables = User::whereIn('role', ['super_administrador', 'jefe_auditor', 'auditor'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('paa.tareas.edit', compact('paa', 'tarea', 'rolesOci', 'responsables'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePAATareaRequest $request, PAA $paa, PAATarea $tarea)
    {
        // Verificar que la tarea pertenece al PAA
        if ($tarea->paa_id !== $paa->id) {
            abort(404, 'Tarea no encontrada en este PAA.');
        }

        // Verificar que el PAA puede ser editado
        if (!$paa->puedeSerEditado()) {
            return redirect()->route('paa.tareas.show', [$paa, $tarea])
                ->with('error', 'No se pueden editar tareas de un PAA finalizado o anulado.');
        }

        DB::beginTransaction();
        try {
            $tarea->fill($request->validated());
            $tarea->updated_by = auth()->id();
            $tarea->save();

            DB::commit();

            return redirect()->route('paa.tareas.show', [$paa, $tarea])
                ->with('success', 'Tarea actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la tarea: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PAA $paa, PAATarea $tarea)
    {
        // Verificar que la tarea pertenece al PAA
        if ($tarea->paa_id !== $paa->id) {
            abort(404, 'Tarea no encontrada en este PAA.');
        }

        // Solo Super Admin puede eliminar tareas
        if (auth()->user()->role !== 'super_administrador') {
            abort(403, 'Solo el Super Administrador puede eliminar tareas.');
        }

        // Verificar que el PAA puede ser editado
        if (!$paa->puedeSerEditado()) {
            return redirect()->route('paa.show', $paa)
                ->with('error', 'No se pueden eliminar tareas de un PAA finalizado o anulado.');
        }

        DB::beginTransaction();
        try {
            $tarea->deleted_by = auth()->id();
            $tarea->save();
            $tarea->delete(); // Soft delete

            DB::commit();

            return redirect()->route('paa.show', $paa)
                ->with('success', 'Tarea eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al eliminar la tarea: ' . $e->getMessage());
        }
    }

    /**
     * Iniciar una tarea (cambiar estado a en_proceso)
     */
    public function iniciar(PAA $paa, PAATarea $tarea)
    {
        // Verificar que la tarea pertenece al PAA
        if ($tarea->paa_id !== $paa->id) {
            abort(404, 'Tarea no encontrada en este PAA.');
        }

        // Verificar autorización
        if (!in_array(auth()->user()->role, ['jefe_auditor', 'auditor', 'super_administrador'])) {
            abort(403, 'No tienes permisos para iniciar esta tarea.');
        }

        DB::beginTransaction();
        try {
            $tarea->iniciar();
            $tarea->updated_by = auth()->id();
            $tarea->save();

            DB::commit();

            return redirect()->route('paa.tareas.show', [$paa, $tarea])
                ->with('success', 'Tarea iniciada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al iniciar la tarea: ' . $e->getMessage());
        }
    }

    /**
     * Completar una tarea (cambiar estado a realizado)
     */
    public function completar(PAA $paa, PAATarea $tarea)
    {
        // Verificar que la tarea pertenece al PAA
        if ($tarea->paa_id !== $paa->id) {
            abort(404, 'Tarea no encontrada en este PAA.');
        }

        // Verificar autorización
        if (!in_array(auth()->user()->role, ['jefe_auditor', 'auditor', 'super_administrador'])) {
            abort(403, 'No tienes permisos para completar esta tarea.');
        }

        DB::beginTransaction();
        try {
            $tarea->completar();
            $tarea->updated_by = auth()->id();
            $tarea->save();

            DB::commit();

            return redirect()->route('paa.tareas.show', [$paa, $tarea])
                ->with('success', 'Tarea completada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al completar la tarea: ' . $e->getMessage());
        }
    }

    /**
     * Anular una tarea
     */
    public function anular(PAA $paa, PAATarea $tarea)
    {
        // Verificar que la tarea pertenece al PAA
        if ($tarea->paa_id !== $paa->id) {
            abort(404, 'Tarea no encontrada en este PAA.');
        }

        // Verificar autorización
        if (!in_array(auth()->user()->role, ['jefe_auditor', 'super_administrador'])) {
            abort(403, 'Solo el Jefe Auditor o Super Admin pueden anular tareas.');
        }

        // Validar motivo
        $motivo = request()->input('motivo');
        if (empty($motivo) || strlen($motivo) < 10) {
            return redirect()->back()
                ->with('error', 'Debe proporcionar un motivo de anulación de al menos 10 caracteres.');
        }

        DB::beginTransaction();
        try {
            $tarea->anular($motivo);
            $tarea->updated_by = auth()->id();
            $tarea->save();

            DB::commit();

            return redirect()->route('paa.tareas.show', [$paa, $tarea])
                ->with('success', 'Tarea anulada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al anular la tarea: ' . $e->getMessage());
        }
    }
}

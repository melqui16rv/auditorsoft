<?php

namespace App\Http\Controllers\PAA;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePAASeguimientoRequest;
use App\Http\Requests\UpdatePAASeguimientoRequest;
use App\Models\PAA;
use App\Models\PAATarea;
use App\Models\PAASeguimiento;
use App\Models\CatEnteControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PAASeguimientoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(PAA $paa, PAATarea $tarea)
    {
        // Verificar que la tarea pertenece al PAA
        if ($tarea->paa_id !== $paa->id) {
            abort(404);
        }

        // Verificar autorización
        if (!in_array(auth()->user()->role, ['jefe_auditor', 'auditor', 'super_administrador'])) {
            abort(403, 'No tiene permisos para ver los seguimientos.');
        }

        $seguimientos = $tarea->seguimientos()
            ->with(['enteControl', 'createdBy', 'evidencias'])
            ->orderBy('fecha_seguimiento', 'desc')
            ->paginate(15);

        $entesControl = CatEnteControl::all();

        // Estadísticas
        $estadisticas = [
            'pendientes' => $tarea->seguimientos()->where('estado', 'pendiente')->count(),
            'realizados' => $tarea->seguimientos()->where('estado', 'realizado')->count(),
            'anulados' => $tarea->seguimientos()->where('estado', 'anulado')->count(),
            'porcentaje' => $tarea->seguimientos()->count() > 0 
                ? round(($tarea->seguimientos()->where('estado', 'realizado')->count() / $tarea->seguimientos()->count()) * 100) 
                : 0,
        ];

        return view('paa.seguimientos.index', compact('paa', 'tarea', 'seguimientos', 'entesControl', 'estadisticas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(PAA $paa, PAATarea $tarea)
    {
        // Verificar que la tarea pertenece al PAA
        if ($tarea->paa_id !== $paa->id) {
            abort(404);
        }

        // Verificar autorización
        if (!in_array(auth()->user()->role, ['jefe_auditor', 'auditor', 'super_administrador'])) {
            abort(403, 'No tiene permisos para crear seguimientos.');
        }

        // No permitir crear seguimientos en tareas anuladas
        if ($tarea->estado == 'anulado') {
            return redirect()->route('paa.tareas.show', [$paa, $tarea])
                ->with('error', 'No se pueden crear seguimientos en tareas anuladas.');
        }

        $entesControl = CatEnteControl::all();

        return view('paa.seguimientos.create', compact('paa', 'tarea', 'entesControl'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePAASeguimientoRequest $request, PAA $paa, PAATarea $tarea)
    {
        // Verificar que la tarea pertenece al PAA
        if ($tarea->paa_id !== $paa->id) {
            abort(404);
        }

        try {
            DB::beginTransaction();

            $seguimiento = new PAASeguimiento();
            $seguimiento->paa_tarea_id = $tarea->id;
            $seguimiento->descripcion_punto_control = $request->descripcion_punto_control;
            $seguimiento->fecha_seguimiento = $request->fecha_seguimiento;
            $seguimiento->ente_control_id = $request->ente_control_id;
            $seguimiento->estado = $request->estado ?? 'pendiente';
            $seguimiento->evaluacion = $request->evaluacion ?? 'pendiente';
            $seguimiento->observaciones = $request->observaciones;
            $seguimiento->created_by = auth()->id();
            $seguimiento->save();

            DB::commit();

            return redirect()->route('paa.seguimientos.show', [$paa, $tarea, $seguimiento])
                ->with('success', 'Seguimiento creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el seguimiento: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PAA $paa, PAATarea $tarea, PAASeguimiento $seguimiento)
    {
        // Verificar que la tarea pertenece al PAA
        if ($tarea->paa_id !== $paa->id) {
            abort(404);
        }

        // Verificar que el seguimiento pertenece a la tarea
        if ($seguimiento->paa_tarea_id !== $tarea->id) {
            abort(404);
        }

        // Verificar autorización
        if (!in_array(auth()->user()->role, ['jefe_auditor', 'auditor', 'super_administrador'])) {
            abort(403, 'No tiene permisos para ver este seguimiento.');
        }

        $seguimiento->load(['enteControl', 'evidencias', 'createdBy', 'updatedBy', 'deletedBy']);

        $totalEvidencias = $seguimiento->evidencias()->count();

        return view('paa.seguimientos.show', compact('paa', 'tarea', 'seguimiento', 'totalEvidencias'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PAA $paa, PAATarea $tarea, PAASeguimiento $seguimiento)
    {
        // Verificar que la tarea pertenece al PAA
        if ($tarea->paa_id !== $paa->id) {
            abort(404);
        }

        // Verificar que el seguimiento pertenece a la tarea
        if ($seguimiento->paa_tarea_id !== $tarea->id) {
            abort(404);
        }

        // Verificar autorización
        if (!in_array(auth()->user()->role, ['jefe_auditor', 'auditor', 'super_administrador'])) {
            abort(403, 'No tiene permisos para editar este seguimiento.');
        }

        // No permitir editar seguimientos realizados (solo super admin)
        if ($seguimiento->estado == 'realizado' && auth()->user()->role != 'super_administrador') {
            return redirect()->route('paa.seguimientos.show', [$paa, $tarea, $seguimiento])
                ->with('error', 'No se pueden editar seguimientos realizados.');
        }

        $entesControl = CatEnteControl::all();

        return view('paa.seguimientos.edit', compact('paa', 'tarea', 'seguimiento', 'entesControl'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePAASeguimientoRequest $request, PAA $paa, PAATarea $tarea, PAASeguimiento $seguimiento)
    {
        // Verificar que la tarea pertenece al PAA
        if ($tarea->paa_id !== $paa->id) {
            abort(404);
        }

        // Verificar que el seguimiento pertenece a la tarea
        if ($seguimiento->paa_tarea_id !== $tarea->id) {
            abort(404);
        }

        try {
            DB::beginTransaction();

            if ($request->has('descripcion_punto_control')) {
                $seguimiento->descripcion_punto_control = $request->descripcion_punto_control;
            }
            if ($request->has('fecha_seguimiento')) {
                $seguimiento->fecha_seguimiento = $request->fecha_seguimiento;
            }
            if ($request->has('ente_control_id')) {
                $seguimiento->ente_control_id = $request->ente_control_id;
            }
            if ($request->has('estado')) {
                $seguimiento->estado = $request->estado;
            }
            if ($request->has('evaluacion')) {
                $seguimiento->evaluacion = $request->evaluacion;
            }
            if ($request->has('observaciones')) {
                $seguimiento->observaciones = $request->observaciones;
            }

            $seguimiento->updated_by = auth()->id();
            $seguimiento->save();

            DB::commit();

            return redirect()->route('paa.seguimientos.show', [$paa, $tarea, $seguimiento])
                ->with('success', 'Seguimiento actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el seguimiento: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PAA $paa, PAATarea $tarea, PAASeguimiento $seguimiento)
    {
        // Verificar que la tarea pertenece al PAA
        if ($tarea->paa_id !== $paa->id) {
            abort(404);
        }

        // Verificar que el seguimiento pertenece a la tarea
        if ($seguimiento->paa_tarea_id !== $tarea->id) {
            abort(404);
        }

        // Solo super administrador puede eliminar
        if (auth()->user()->role != 'super_administrador') {
            abort(403, 'Solo el Super Administrador puede eliminar seguimientos.');
        }

        try {
            DB::beginTransaction();

            $seguimiento->deleted_by = auth()->id();
            $seguimiento->save();
            $seguimiento->delete(); // Soft delete

            DB::commit();

            return redirect()->route('paa.seguimientos.index', [$paa, $tarea])
                ->with('success', 'Seguimiento eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al eliminar el seguimiento: ' . $e->getMessage());
        }
    }

    /**
     * Marcar seguimiento como realizado
     */
    public function realizar(PAA $paa, PAATarea $tarea, PAASeguimiento $seguimiento)
    {
        // Verificar que la tarea pertenece al PAA
        if ($tarea->paa_id !== $paa->id) {
            abort(404);
        }

        // Verificar que el seguimiento pertenece a la tarea
        if ($seguimiento->paa_tarea_id !== $tarea->id) {
            abort(404);
        }

        // Verificar autorización
        if (!in_array(auth()->user()->role, ['jefe_auditor', 'auditor', 'super_administrador'])) {
            abort(403, 'No tiene permisos para realizar esta acción.');
        }

        // Validar que esté pendiente
        if ($seguimiento->estado != 'pendiente') {
            return redirect()->back()
                ->with('error', 'Solo se pueden realizar seguimientos pendientes.');
        }

        try {
            DB::beginTransaction();

            $seguimiento->estado = 'realizado';
            $seguimiento->updated_by = auth()->id();
            $seguimiento->save();

            DB::commit();

            return redirect()->route('paa.seguimientos.show', [$paa, $tarea, $seguimiento])
                ->with('success', 'Seguimiento marcado como realizado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al realizar el seguimiento: ' . $e->getMessage());
        }
    }

    /**
     * Anular seguimiento
     */
    public function anular(Request $request, PAA $paa, PAATarea $tarea, PAASeguimiento $seguimiento)
    {
        // Validar motivo
        $request->validate([
            'motivo' => 'required|string|min:10',
        ], [
            'motivo.required' => 'Debe indicar el motivo de anulación.',
            'motivo.min' => 'El motivo debe tener al menos 10 caracteres.',
        ]);

        // Verificar que la tarea pertenece al PAA
        if ($tarea->paa_id !== $paa->id) {
            abort(404);
        }

        // Verificar que el seguimiento pertenece a la tarea
        if ($seguimiento->paa_tarea_id !== $tarea->id) {
            abort(404);
        }

        // Verificar autorización
        if (!in_array(auth()->user()->role, ['jefe_auditor', 'super_administrador'])) {
            abort(403, 'No tiene permisos para anular seguimientos.');
        }

        try {
            DB::beginTransaction();

            $seguimiento->estado = 'anulado';
            $seguimiento->observaciones = ($seguimiento->observaciones ? $seguimiento->observaciones . "\n\n" : '') 
                . "ANULADO: " . $request->motivo;
            $seguimiento->updated_by = auth()->id();
            $seguimiento->save();

            DB::commit();

            return redirect()->route('paa.seguimientos.show', [$paa, $tarea, $seguimiento])
                ->with('success', 'Seguimiento anulado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al anular el seguimiento: ' . $e->getMessage());
        }
    }
}

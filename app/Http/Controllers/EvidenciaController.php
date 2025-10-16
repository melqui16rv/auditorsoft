<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEvidenciaRequest;
use App\Models\Evidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EvidenciaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEvidenciaRequest $request)
    {
        try {
            DB::beginTransaction();

            // Validar que el evidenciable existe
            $evidenciableClass = $request->evidenciable_type;
            $evidenciable = $evidenciableClass::findOrFail($request->evidenciable_id);

            // Procesar el archivo
            $archivo = $request->file('archivo');
            $nombreOriginal = $archivo->getClientOriginalName();
            $extension = $archivo->getClientOriginalExtension();
            
            // Generar nombre único
            $nombreUnico = time() . '_' . str_replace(' ', '_', pathinfo($nombreOriginal, PATHINFO_FILENAME)) . '.' . $extension;
            
            // Guardar archivo en storage
            $rutaArchivo = $archivo->storeAs('evidencias', $nombreUnico);

            // Crear registro en BD
            $evidencia = Evidencia::create([
                'evidenciable_type' => $request->evidenciable_type,
                'evidenciable_id' => $request->evidenciable_id,
                'nombre_archivo' => $nombreOriginal,
                'ruta_archivo' => $rutaArchivo,
                'tipo_archivo' => $extension,
                'tamano_kb' => $archivo->getSize() / 1024,
                'descripcion' => $request->descripcion,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            // Determinar si es AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Evidencia subida correctamente',
                    'evidencia' => $evidencia
                ]);
            }

            return redirect()->back()->with('success', 'Evidencia subida correctamente');

        } catch (\Exception $e) {
            DB::rollBack();

            // Eliminar archivo si se subió
            if (isset($rutaArchivo) && Storage::exists($rutaArchivo)) {
                Storage::delete($rutaArchivo);
            }

            // Determinar si es AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al subir la evidencia: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al subir la evidencia: ' . $e->getMessage());
        }
    }

    /**
     * Download the specified resource.
     */
    public function download(Evidencia $evidencia)
    {
        // Verificar autorización
        if (!in_array(auth()->user()->role, ['jefe_auditor', 'auditor', 'auditado', 'super_administrador'])) {
            abort(403, 'No tiene permisos para descargar evidencias.');
        }

        // Verificar que el archivo existe
        if (!Storage::exists($evidencia->ruta_archivo)) {
            return redirect()->back()
                ->with('error', 'El archivo no existe en el servidor.');
        }

        return Storage::download($evidencia->ruta_archivo, $evidencia->nombre_archivo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Evidencia $evidencia)
    {
        // Solo super administrador puede eliminar
        if (auth()->user()->role != 'super_administrador') {
            abort(403, 'Solo el Super Administrador puede eliminar evidencias.');
        }

        try {
            DB::beginTransaction();

            // Eliminar archivo físico
            if (Storage::exists($evidencia->ruta_archivo)) {
                Storage::delete($evidencia->ruta_archivo);
            }

            // Registrar quién eliminó
            $evidencia->deleted_by = auth()->id();
            $evidencia->save();
            
            // Soft delete
            $evidencia->delete();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Evidencia eliminada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al eliminar la evidencia: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource (preview).
     */
    public function show(Evidencia $evidencia)
    {
        // Verificar autorización
        if (!in_array(auth()->user()->role, ['jefe_auditor', 'auditor', 'auditado', 'super_administrador'])) {
            abort(403, 'No tiene permisos para ver evidencias.');
        }

        $evidencia->load(['evidenciable', 'createdBy', 'deletedBy']);

        return view('evidencias.show', compact('evidencia'));
    }

    /**
     * Get icon class for file type
     */
    public static function getIconClass($extension)
    {
        $icons = [
            'pdf' => 'pdf',
            'doc' => 'word',
            'docx' => 'word',
            'xls' => 'excel',
            'xlsx' => 'excel',
            'jpg' => 'image',
            'jpeg' => 'image',
            'png' => 'image',
        ];

        return $icons[$extension] ?? 'text';
    }
}

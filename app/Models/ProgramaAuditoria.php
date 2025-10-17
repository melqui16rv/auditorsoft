<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo para Programa de Auditoría
 * 
 * Cumple con:
 * - RF 3.3-3.6 Programa de Auditoría
 * - FR-GCA-001
 */
class ProgramaAuditoria extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'programa_auditoria';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'vigencia',
        'matriz_priorizacion_id',
        'elaborado_por',
        'aprobado_por_id',
        'estado',
        'fecha_inicio_programacion',
        'fecha_fin_programacion',
        'fecha_aprobacion',
        'numero_auditores',
        'alcance',
        'objetivos',
        'version_formato',
        'fecha_aprobacion_formato',
        'medio_almacenamiento',
        'proteccion',
        'ubicacion_logica',
        'metodo_recuperacion',
        'responsable_archivo',
        'permanencia',
        'disposicion_final',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'fecha_inicio_programacion' => 'date',
        'fecha_fin_programacion' => 'date',
        'fecha_aprobacion' => 'date',
        'fecha_aprobacion_formato' => 'date',
        'vigencia' => 'integer',
    ];

    const ESTADO_ELABORACION = 'elaboracion';
    const ESTADO_ENVIADO_APROBACION = 'enviado_aprobacion';
    const ESTADO_APROBADO = 'aprobado';

    /**
     * Relación: Matriz de Priorización origen
     */
    public function matrizPriorizacion(): BelongsTo
    {
        return $this->belongsTo(MatrizPriorizacion::class, 'matriz_priorizacion_id');
    }

    /**
     * Relación: Usuario que elaboró
     */
    public function elaboradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'elaborado_por');
    }

    /**
     * Relación: Usuario que aprobó
     */
    public function aprobadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobado_por_id');
    }

    /**
     * Relación: Detalles (procesos del programa)
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(ProgramaAuditoriaDetalle::class, 'programa_auditoria_id');
    }

    /**
     * Relación: Auditoría
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function actualizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Generar código automático
     */
    public static function generarCodigo($vigencia = null)
    {
        $vigencia = $vigencia ?? date('Y');
        $ultimo = static::where('vigencia', $vigencia)
            ->withTrashed()
            ->latest('id')
            ->first();

        $numero = $ultimo ? intval(substr($ultimo->codigo, -3)) + 1 : 1;
        return sprintf('PRO-%d-%03d', $vigencia, $numero);
    }

    /**
     * Calcular cantidad de procesos a auditar
     */
    public function totalProcesos()
    {
        return $this->detalles()->count();
    }

    /**
     * Calcular promedio de riesgo del programa
     */
    public function riesgoPromedio()
    {
        return $this->detalles()->avg('ponderacion_riesgo');
    }

    /**
     * Validar si puede ser enviado a aprobación
     */
    public function puedeSerEnviado()
    {
        return $this->estado === self::ESTADO_ELABORACION 
            && $this->detalles()->count() > 0
            && $this->fecha_inicio_programacion 
            && $this->fecha_fin_programacion;
    }

    /**
     * Validar si puede ser editado
     */
    public function puedeSerEditado()
    {
        return $this->estado === self::ESTADO_ELABORACION;
    }

    /**
     * Traslado automático desde Matriz aprobada
     */
    public static function trasldarDeMatriz(MatrizPriorizacion $matriz)
    {
        // Crear programa
        $programa = static::create([
            'codigo' => static::generarCodigo($matriz->vigencia),
            'nombre' => 'Programa PAA ' . $matriz->vigencia,
            'vigencia' => $matriz->vigencia,
            'matriz_priorizacion_id' => $matriz->id,
            'elaborado_por' => auth()->id(),
            'estado' => self::ESTADO_ELABORACION,
            'created_by' => auth()->id(),
            'fecha_inicio_programacion' => now()->addDays(7)->toDateString(),
            'fecha_fin_programacion' => now()->addMonths(12)->toDateString(),
        ]);

        // Copiar detalles donde incluir_en_programa = true
        foreach ($matriz->detalles()->where('incluir_en_programa', true)->get() as $detalle) {
            ProgramaAuditoriaDetalle::create([
                'programa_auditoria_id' => $programa->id,
                'matriz_priorizacion_detalle_id' => $detalle->id,
                'proceso_id' => $detalle->proceso_id,
                'riesgo_nivel' => $detalle->riesgo_nivel,
                'ponderacion_riesgo' => $detalle->ponderacion_riesgo,
                'ciclo_rotacion' => $detalle->ciclo_rotacion,
                'objetivos_programa' => 'Auditoria del proceso: ' . $detalle->proceso->nombre,
                'alcance_programa' => 'Evaluación integral del proceso',
                'estado_auditoria' => 'programado',
                'created_by' => auth()->id(),
            ]);
        }

        return $programa;
    }
}

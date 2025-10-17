<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para detalles del Programa de Auditoría
 */
class ProgramaAuditoriaDetalle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'programa_auditoria_detalle';

    protected $fillable = [
        'programa_auditoria_id',
        'matriz_priorizacion_detalle_id',
        'proceso_id',
        'area_id',
        'fecha_inicio',
        'fecha_fin',
        'auditor_responsable_id',
        'objetivos_programa',
        'alcance_programa',
        'criterios_aplicados',
        'estado_auditoria',
        'riesgo_nivel',
        'ponderacion_riesgo',
        'ciclo_rotacion',
        'observaciones',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    const ESTADO_PROGRAMADO = 'programado';
    const ESTADO_EN_EJECUCION = 'en_ejecucion';
    const ESTADO_FINALIZADO = 'finalizado';
    const ESTADO_ANULADO = 'anulado';

    /**
     * Relación: Programa padre
     */
    public function programa(): BelongsTo
    {
        return $this->belongsTo(ProgramaAuditoria::class, 'programa_auditoria_id');
    }

    /**
     * Relación: Detalle de Matriz origen
     */
    public function matrizDetalle(): BelongsTo
    {
        return $this->belongsTo(MatrizPriorizacionDetalle::class, 'matriz_priorizacion_detalle_id');
    }

    /**
     * Relación: Proceso
     */
    public function proceso(): BelongsTo
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }

    /**
     * Relación: Área
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    /**
     * Relación: Auditor responsable
     */
    public function auditorResponsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_responsable_id');
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

    /**
     * Cambiar estado a en_ejecucion
     */
    public function iniciar()
    {
        $this->update([
            'estado_auditoria' => self::ESTADO_EN_EJECUCION,
            'fecha_inicio' => now()->toDateString(),
        ]);
    }

    /**
     * Cambiar estado a finalizado
     */
    public function finalizar()
    {
        $this->update([
            'estado_auditoria' => self::ESTADO_FINALIZADO,
            'fecha_fin' => now()->toDateString(),
        ]);
    }

    /**
     * Cambiar estado a anulado
     */
    public function anular()
    {
        $this->update([
            'estado_auditoria' => self::ESTADO_ANULADO,
        ]);
    }

    /**
     * Validar si puede iniciar
     */
    public function puedeIniciar()
    {
        return $this->estado_auditoria === self::ESTADO_PROGRAMADO;
    }

    /**
     * Validar si puede finalizarse
     */
    public function puedeFinalizarse()
    {
        return $this->estado_auditoria === self::ESTADO_EN_EJECUCION;
    }
}

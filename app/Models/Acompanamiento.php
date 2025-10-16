<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Modelo para Acompañamientos - Formato FR-GCE-003
 * 
 * Rol OCI: "Enfoque hacia la prevención"
 * Actividades de asesoría y capacitación
 * 
 * RF 2.8
 */
class Acompanamiento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'acompanamientos';

    protected $fillable = [
        'paa_id',
        'codigo_registro',
        'fecha_inicio',
        'fecha_fin',
        'nombre_acompanamiento',
        'objetivo',
        'alcance',
        'area_id',
        'proceso_id',
        'tipo_acompanamiento',
        'responsable_oci_id',
        'responsable_area_nombre',
        'responsable_area_cargo',
        'cronograma',
        'metodologia',
        'observaciones',
        'recomendaciones',
        'compromisos',
        'estado',
        'evaluacion_efectividad',
        'version_formato',
        'fecha_aprobacion_formato',
        'responsable_archivo',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_aprobacion_formato' => 'date',
        'cronograma' => 'array',
    ];

    /**
     * Tipos de acompañamiento
     */
    const TIPO_ASESORIA = 'asesoria';
    const TIPO_CAPACITACION = 'capacitacion';
    const TIPO_SEGUIMIENTO_PREVENTIVO = 'seguimiento_preventivo';
    const TIPO_REVISION_PROCESOS = 'revision_procesos';
    const TIPO_IMPLEMENTACION_CONTROLES = 'implementacion_controles';
    const TIPO_OTRO = 'otro';

    /**
     * Estados
     */
    const ESTADO_PLANEADO = 'planeado';
    const ESTADO_EN_PROCESO = 'en_proceso';
    const ESTADO_FINALIZADO = 'finalizado';
    const ESTADO_SUSPENDIDO = 'suspendido';

    /**
     * Evaluaciones de efectividad
     */
    const EVAL_MUY_EFECTIVO = 'muy_efectivo';
    const EVAL_EFECTIVO = 'efectivo';
    const EVAL_POCO_EFECTIVO = 'poco_efectivo';
    const EVAL_NO_EFECTIVO = 'no_efectivo';
    const EVAL_PENDIENTE = 'pendiente';

    /**
     * Relaciones
     */

    public function paa(): BelongsTo
    {
        return $this->belongsTo(PAA::class, 'paa_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }

    public function responsableOci(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_oci_id');
    }

    public function evidencias(): MorphMany
    {
        return $this->morphMany(Evidencia::class, 'evidenciable');
    }

    /**
     * Scopes
     */

    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo_acompanamiento', $tipo);
    }

    public function scopeVigentes($query)
    {
        return $query->whereIn('estado', [
            self::ESTADO_PLANEADO,
            self::ESTADO_EN_PROCESO
        ]);
    }

    /**
     * Métodos de negocio
     */

    public function iniciar(): bool
    {
        $this->estado = self::ESTADO_EN_PROCESO;
        return $this->save();
    }

    public function finalizar(string $evaluacion): bool
    {
        $this->estado = self::ESTADO_FINALIZADO;
        $this->evaluacion_efectividad = $evaluacion;
        $this->fecha_fin = now();
        
        return $this->save();
    }

    public function suspender(string $motivo): bool
    {
        $this->estado = self::ESTADO_SUSPENDIDO;
        $this->observaciones = ($this->observaciones ? $this->observaciones . "\n\n" : '')
            . "SUSPENDIDO: " . $motivo;
        
        return $this->save();
    }

    /**
     * Atributos calculados
     */

    public function getEstadoBadgeAttribute(): string
    {
        return match($this->estado) {
            self::ESTADO_PLANEADO => '<span class="badge bg-secondary">Planeado</span>',
            self::ESTADO_EN_PROCESO => '<span class="badge bg-primary">En Proceso</span>',
            self::ESTADO_FINALIZADO => '<span class="badge bg-success">Finalizado</span>',
            self::ESTADO_SUSPENDIDO => '<span class="badge bg-warning">Suspendido</span>',
            default => '<span class="badge bg-secondary">Desconocido</span>',
        };
    }

    public function getEvaluacionBadgeAttribute(): string
    {
        return match($this->evaluacion_efectividad) {
            self::EVAL_MUY_EFECTIVO => '<span class="badge bg-success">Muy Efectivo</span>',
            self::EVAL_EFECTIVO => '<span class="badge bg-info">Efectivo</span>',
            self::EVAL_POCO_EFECTIVO => '<span class="badge bg-warning">Poco Efectivo</span>',
            self::EVAL_NO_EFECTIVO => '<span class="badge bg-danger">No Efectivo</span>',
            self::EVAL_PENDIENTE => '<span class="badge bg-secondary">Pendiente</span>',
            default => '<span class="badge bg-secondary">N/A</span>',
        };
    }

    /**
     * Generar código automático
     */
    public static function generarCodigo(): string
    {
        $ultimo = self::orderBy('id', 'desc')->first();
        
        $consecutivo = $ultimo ? 
            (int) substr($ultimo->codigo_registro, -3) + 1 : 
            1;

        return sprintf('AC-%d-%03d', now()->year, $consecutivo);
    }
}

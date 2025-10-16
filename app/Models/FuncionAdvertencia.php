<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Modelo para Funciones de Advertencia - Formato FR-GCE-002
 * 
 * Formaliza avisos sobre peligro o riesgo inminente
 * Requiere aprobación del Comité ICCCI
 * 
 * RF 2.7
 */
class FuncionAdvertencia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'funciones_advertencia';

    protected $fillable = [
        'paa_id',
        'codigo_registro',
        'fecha_advertencia',
        'asunto',
        'descripcion_riesgo',
        'area_id',
        'proceso_id',
        'nivel_riesgo',
        'tipo_riesgo',
        'recomendaciones',
        'acciones_sugeridas',
        'destinatario_nombre',
        'destinatario_cargo',
        'informe_tecnico',
        'decision_comite',
        'fecha_decision_comite',
        'observaciones_comite',
        'estado',
        'version_formato',
        'fecha_aprobacion_formato',
        'responsable_archivo',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fecha_advertencia' => 'date',
        'fecha_decision_comite' => 'date',
        'fecha_aprobacion_formato' => 'date',
    ];

    /**
     * Niveles de riesgo
     */
    const NIVEL_EXTREMO = 'extremo';
    const NIVEL_ALTO = 'alto';
    const NIVEL_MODERADO = 'moderado';
    const NIVEL_BAJO = 'bajo';

    /**
     * Tipos de riesgo
     */
    const TIPO_OPERACIONAL = 'operacional';
    const TIPO_FINANCIERO = 'financiero';
    const TIPO_LEGAL = 'legal';
    const TIPO_REPUTACIONAL = 'reputacional';
    const TIPO_ESTRATEGICO = 'estrategico';
    const TIPO_CUMPLIMIENTO = 'cumplimiento';
    const TIPO_TECNOLOGICO = 'tecnologico';
    const TIPO_OTRO = 'otro';

    /**
     * Decisiones del Comité
     */
    const DECISION_APROBADA = 'aprobada';
    const DECISION_IMPROCEDENTE = 'improcedente';
    const DECISION_PENDIENTE = 'pendiente';

    /**
     * Estados
     */
    const ESTADO_BORRADOR = 'borrador';
    const ESTADO_EMITIDA = 'emitida';
    const ESTADO_EN_REVISION_COMITE = 'en_revision_comite';
    const ESTADO_APROBADA = 'aprobada';
    const ESTADO_CERRADA = 'cerrada';

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

    public function scopeNivelRiesgo($query, $nivel)
    {
        return $query->where('nivel_riesgo', $nivel);
    }

    public function scopePendientesRevision($query)
    {
        return $query->where('decision_comite', self::DECISION_PENDIENTE);
    }

    /**
     * Métodos de negocio
     */

    public function emitir(): bool
    {
        $this->estado = self::ESTADO_EMITIDA;
        return $this->save();
    }

    public function enviarAComite(): bool
    {
        $this->estado = self::ESTADO_EN_REVISION_COMITE;
        return $this->save();
    }

    public function aprobarPorComite(string $observaciones = null): bool
    {
        $this->decision_comite = self::DECISION_APROBADA;
        $this->fecha_decision_comite = now();
        $this->estado = self::ESTADO_APROBADA;
        $this->observaciones_comite = $observaciones;
        
        return $this->save();
    }

    public function declararImprocedente(string $justificacion): bool
    {
        $this->decision_comite = self::DECISION_IMPROCEDENTE;
        $this->fecha_decision_comite = now();
        $this->observaciones_comite = $justificacion;
        
        return $this->save();
    }

    public function cerrar(): bool
    {
        $this->estado = self::ESTADO_CERRADA;
        return $this->save();
    }

    /**
     * Atributos calculados
     */

    public function getNivelRiesgoBadgeAttribute(): string
    {
        return match($this->nivel_riesgo) {
            self::NIVEL_EXTREMO => '<span class="badge bg-danger">Extremo</span>',
            self::NIVEL_ALTO => '<span class="badge bg-warning">Alto</span>',
            self::NIVEL_MODERADO => '<span class="badge bg-info">Moderado</span>',
            self::NIVEL_BAJO => '<span class="badge bg-secondary">Bajo</span>',
            default => '<span class="badge bg-secondary">N/A</span>',
        };
    }

    public function getEstadoBadgeAttribute(): string
    {
        return match($this->estado) {
            self::ESTADO_BORRADOR => '<span class="badge bg-secondary">Borrador</span>',
            self::ESTADO_EMITIDA => '<span class="badge bg-primary">Emitida</span>',
            self::ESTADO_EN_REVISION_COMITE => '<span class="badge bg-warning">En Revisión Comité</span>',
            self::ESTADO_APROBADA => '<span class="badge bg-success">Aprobada</span>',
            self::ESTADO_CERRADA => '<span class="badge bg-dark">Cerrada</span>',
            default => '<span class="badge bg-secondary">Desconocido</span>',
        };
    }

    /**
     * Generar código automático
     */
    public static function generarCodigo(): string
    {
        $ultima = self::orderBy('id', 'desc')->first();
        
        $consecutivo = $ultima ? 
            (int) substr($ultima->codigo_registro, -3) + 1 : 
            1;

        return sprintf('FA-%d-%03d', now()->year, $consecutivo);
    }
}

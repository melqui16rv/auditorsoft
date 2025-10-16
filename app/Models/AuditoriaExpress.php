<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para Auditorías Express
 * 
 * Auditorías internas especiales solicitadas por el Representante Legal
 * o derivadas de eventualidades
 * 
 * RF 2.6
 */
class AuditoriaExpress extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'auditorias_express';

    protected $fillable = [
        'paa_id',
        'codigo_registro',
        'nombre_auditoria',
        'justificacion',
        'solicitante_nombre',
        'solicitante_cargo',
        'fecha_solicitud',
        'area_id',
        'proceso_id',
        'fecha_inicio',
        'fecha_fin',
        'auditor_responsable_id',
        'estado',
        'observaciones',
        'conclusiones',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fecha_solicitud' => 'date',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    /**
     * Estados
     */
    const ESTADO_SOLICITADA = 'solicitada';
    const ESTADO_APROBADA = 'aprobada';
    const ESTADO_EN_EJECUCION = 'en_ejecucion';
    const ESTADO_FINALIZADA = 'finalizada';
    const ESTADO_RECHAZADA = 'rechazada';

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

    public function auditorResponsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_responsable_id');
    }

    /**
     * Scopes
     */

    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeVigentes($query)
    {
        return $query->whereIn('estado', [
            self::ESTADO_APROBADA,
            self::ESTADO_EN_EJECUCION
        ]);
    }

    /**
     * Métodos de negocio
     */

    public function aprobar(): bool
    {
        $this->estado = self::ESTADO_APROBADA;
        return $this->save();
    }

    public function rechazar(string $motivo): bool
    {
        $this->estado = self::ESTADO_RECHAZADA;
        $this->observaciones = $motivo;
        return $this->save();
    }

    public function iniciarEjecucion(): bool
    {
        $this->estado = self::ESTADO_EN_EJECUCION;
        return $this->save();
    }

    public function finalizar(string $conclusiones): bool
    {
        $this->estado = self::ESTADO_FINALIZADA;
        $this->conclusiones = $conclusiones;
        return $this->save();
    }

    /**
     * Atributos calculados
     */

    public function getEstadoBadgeAttribute(): string
    {
        return match($this->estado) {
            self::ESTADO_SOLICITADA => '<span class="badge bg-warning">Solicitada</span>',
            self::ESTADO_APROBADA => '<span class="badge bg-info">Aprobada</span>',
            self::ESTADO_EN_EJECUCION => '<span class="badge bg-primary">En Ejecución</span>',
            self::ESTADO_FINALIZADA => '<span class="badge bg-success">Finalizada</span>',
            self::ESTADO_RECHAZADA => '<span class="badge bg-danger">Rechazada</span>',
            default => '<span class="badge bg-secondary">Desconocido</span>',
        };
    }

    /**
     * Generar código automático
     */
    public static function generarCodigo(): string
    {
        $ultimaAuditoria = self::orderBy('id', 'desc')->first();
        
        $consecutivo = $ultimaAuditoria ? 
            (int) substr($ultimaAuditoria->codigo_registro, -3) + 1 : 
            1;

        return sprintf('AE-%d-%03d', now()->year, $consecutivo);
    }
}

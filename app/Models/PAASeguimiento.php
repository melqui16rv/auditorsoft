<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Modelo para Seguimientos/Puntos de Control de las tareas del PAA
 * 
 * RF 2.3: Registro de puntos de control con evidencias
 */
class PAASeguimiento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'paa_seguimientos';

    protected $fillable = [
        'tarea_id',
        'fecha_realizacion',
        'observaciones',
        'ente_control_id',
        'motivo_anulacion',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fecha_realizacion' => 'datetime',
    ];

    /**
     * Relaciones
     */

    public function tarea(): BelongsTo
    {
        return $this->belongsTo(PAATarea::class, 'tarea_id');
    }

    public function enteControl(): BelongsTo
    {
        return $this->belongsTo(EntidadControl::class, 'ente_control_id');
    }

    /**
     * Evidencias (relación polimórfica)
     * RF 2.4
     */
    public function evidencias(): MorphMany
    {
        return $this->morphMany(Evidencia::class, 'evidenciable');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scopes
     */

    public function scopeRealizados($query)
    {
        return $query->whereNotNull('fecha_realizacion');
    }

    public function scopePendientes($query)
    {
        return $query->whereNull('fecha_realizacion');
    }

    /**
     * Métodos de negocio
     */

    /**
     * Marcar como realizado
     */
    public function completar(): bool
    {
        $this->fecha_realizacion = now();
        
        return $this->save();
    }

    /**
     * Anular seguimiento
     */
    public function anular(string $motivo = null): bool
    {
        if ($motivo) {
            $this->motivo_anulacion = $motivo;
        }
        
        // Eliminar de forma suave (soft delete)
        return $this->delete();
    }

    /**
     * Atributos calculados
     */

    public function getEstaRealizadoAttribute(): bool
    {
        return !is_null($this->fecha_realizacion);
    }

    /**
     * Verificar si tiene evidencias
     */
    public function tieneEvidencias(): bool
    {
        return $this->evidencias()->exists();
    }

    /**
     * Contar evidencias
     */
    public function cantidadEvidencias(): int
    {
        return $this->evidencias()->count();
    }
}

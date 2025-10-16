<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo para Tareas del PAA organizadas por roles OCI
 * 
 * Cada rol del Decreto 648/2017 tiene tareas específicas
 * RF 2.2
 */
class PAATarea extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'paa_tareas';

    protected $fillable = [
        'paa_id',
        'rol_oci_id',
        'nombre_tarea',
        'descripcion_tarea',
        'fecha_inicio_planeada',
        'fecha_fin_planeada',
        'responsable_id',
        'estado_tarea',
        'fecha_inicio_real',
        'fecha_fin_real',
        'evaluacion_general',
        'observaciones',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fecha_inicio_planeada' => 'date',
        'fecha_fin_planeada' => 'date',
        'fecha_inicio_real' => 'date',
        'fecha_fin_real' => 'date',
    ];

    /**
     * Estados de la tarea
     */
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_EN_PROCESO = 'en_proceso';
    const ESTADO_REALIZADA = 'realizada';
    const ESTADO_ANULADA = 'anulada';

    /**
     * Relaciones
     */

    public function paa(): BelongsTo
    {
        return $this->belongsTo(PAA::class, 'paa_id');
    }

    public function rolOci(): BelongsTo
    {
        return $this->belongsTo(RolOci::class, 'rol_oci_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function seguimientos(): HasMany
    {
        return $this->hasMany(PAASeguimiento::class, 'tarea_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scopes
     */

    public function scopeEstado($query, $estado)
    {
        return $query->where('estado_tarea', $estado);
    }

    public function scopeRolOci($query, $rol)
    {
        return $query->where('rol_oci_id', $rol);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado_tarea', self::ESTADO_PENDIENTE);
    }

    public function scopeRealizadas($query)
    {
        return $query->where('estado_tarea', self::ESTADO_REALIZADA);
    }

    /**
     * Métodos de negocio
     */

    /**
     * Calcular porcentaje de cumplimiento de seguimientos
     */
    public function calcularPorcentajeCumplimiento(): float
    {
        $totalSeguimientos = $this->seguimientos()->count();
        
        if ($totalSeguimientos === 0) {
            return 0.0;
        }

        $seguimientosRealizados = $this->seguimientos()
            ->whereNotNull('fecha_realizacion')
            ->count();

        return round(($seguimientosRealizados / $totalSeguimientos) * 100, 2);
    }

    /**
     * Verificar si la tarea está vencida
     */
    public function estaVencida(): bool
    {
        if (in_array($this->estado_tarea, [self::ESTADO_REALIZADA, self::ESTADO_ANULADA])) {
            return false;
        }

        return $this->fecha_fin_planeada < now()->toDateString();
    }

    /**
     * Iniciar tarea
     */
    public function iniciar(): bool
    {
        $this->estado_tarea = self::ESTADO_EN_PROCESO;
        $this->fecha_inicio_real = now()->toDateString();
        $this->updated_by = auth()->id();
        
        return $this->save();
    }

    /**
     * Marcar como realizada
     */
    public function completar(string $evaluacion = 'bien'): bool
    {
        $this->estado_tarea = self::ESTADO_REALIZADA;
        $this->fecha_fin_real = now()->toDateString();
        $this->evaluacion_general = $evaluacion;
        $this->updated_by = auth()->id();
        
        return $this->save();
    }

    /**
     * Anular tarea
     */
    public function anular(string $motivo = null): bool
    {
        $this->estado_tarea = self::ESTADO_ANULADA;
        $this->updated_by = auth()->id();
        
        if ($motivo) {
            $this->observaciones = ($this->observaciones ? $this->observaciones . "\n\n" : '')
                . "ANULADO: " . $motivo;
        }
        
        return $this->save();
    }

    /**
     * Atributos calculados
     */

    public function getEstadoBadgeAttribute(): string
    {
        return match($this->estado_tarea) {
            self::ESTADO_PENDIENTE => '<span class="badge bg-secondary">Pendiente</span>',
            self::ESTADO_EN_PROCESO => '<span class="badge bg-info">En Proceso</span>',
            self::ESTADO_REALIZADA => '<span class="badge bg-success">Realizada</span>',
            self::ESTADO_ANULADA => '<span class="badge bg-danger">Anulada</span>',
            default => '<span class="badge bg-secondary">Desconocido</span>',
        };
    }

    public function getDuracionPlanificadaDiasAttribute(): int
    {
        if (!$this->fecha_inicio_planeada || !$this->fecha_fin_planeada) {
            return 0;
        }
        
        return \Carbon\Carbon::parse($this->fecha_inicio_planeada)->diffInDays(
            \Carbon\Carbon::parse($this->fecha_fin_planeada)
        );
    }
}

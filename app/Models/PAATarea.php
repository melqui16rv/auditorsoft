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
        'rol_oci',
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'auditor_responsable_id',
        'estado',
        'tipo',
        'objetivo',
        'alcance',
        'criterios_auditoria',
        'recursos_necesarios',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
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

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_responsable_id');
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
        return $query->where('estado', $estado);
    }

    public function scopeRolOci($query, $rol)
    {
        return $query->where('rol_oci', $rol);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeRealizadas($query)
    {
        return $query->where('estado', 'realizada');
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
        if (in_array($this->estado, ['realizada', 'anulada'])) {
            return false;
        }

        return $this->fecha_fin < now()->toDateString();
    }

    /**
     * Iniciar tarea
     */
    public function iniciar(): bool
    {
        $this->estado = 'en_proceso';
        $this->updated_by = auth()->id();
        
        return $this->save();
    }

    /**
     * Marcar como realizada
     */
    public function completar(): bool
    {
        $this->estado = 'realizada';
        $this->updated_by = auth()->id();
        
        return $this->save();
    }

    /**
     * Anular tarea
     */
    public function anular(string $motivo = null): bool
    {
        $this->estado = 'anulada';
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
        return match($this->estado) {
            'pendiente' => '<span class="badge bg-secondary">Pendiente</span>',
            'en_proceso' => '<span class="badge bg-info">En Proceso</span>',
            'realizada' => '<span class="badge bg-success">Realizada</span>',
            'anulada' => '<span class="badge bg-danger">Anulada</span>',
            default => '<span class="badge bg-secondary">Desconocido</span>',
        };
    }

    public function getDuracionPlanificadaDiasAttribute(): int
    {
        if (!$this->fecha_inicio || !$this->fecha_fin) {
            return 0;
        }

        return abs(\Carbon\Carbon::parse($this->fecha_fin)->diffInDays(\Carbon\Carbon::parse($this->fecha_inicio)));
    }

    /**
     * Obtener el nombre del rol OCI
     */
    public function getNombreRolOciAttribute(): string
    {
        return match($this->rol_oci) {
            'fomento_cultura' => 'Fomento de la Cultura del Control',
            'apoyo_fortalecimiento' => 'Apoyo al Fortalecimiento',
            'investigaciones' => 'Investigaciones',
            'evaluacion_control' => 'Evaluación de Control',
            'evaluacion_gestion' => 'Evaluación de Gestión',
            default => $this->rol_oci,
        };
    }
}

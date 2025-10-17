<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo para Matriz de Priorización del Universo de Auditoría
 * 
 * Cumple con:
 * - RF 3.1 Matriz de priorización
 * - Guía de Auditoría Interna Basada en Riesgos V4
 * - ISO 19011:2018
 */
class MatrizPriorizacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'matriz_priorizacion';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'vigencia',
        'municipio_id',
        'fecha_elaboracion',
        'elaborado_por',
        'fecha_aprobacion',
        'aprobado_por_id',
        'estado',
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
        'fecha_elaboracion' => 'date',
        'fecha_aprobacion' => 'date',
        'fecha_aprobacion_formato' => 'date',
        'vigencia' => 'integer',
    ];

    const ESTADO_BORRADOR = 'borrador';
    const ESTADO_VALIDADO = 'validado';
    const ESTADO_APROBADO = 'aprobado';

    /**
     * Relación: Usuario que elaboró la matriz
     */
    public function elaboradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'elaborado_por');
    }

    /**
     * Relación: Usuario que aprobó la matriz
     */
    public function aprobadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobado_por_id');
    }

    /**
     * Relación: Municipio
     */
    public function municipio(): BelongsTo
    {
        return $this->belongsTo(MunicipioColombia::class, 'municipio_id');
    }

    /**
     * Relación: Detalles de priorización (procesos evaluados)
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(MatrizPriorizacionDetalle::class, 'matriz_priorizacion_id');
    }

    /**
     * Relación: Usuarios
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
        return sprintf('MAT-%d-%03d', $vigencia, $numero);
    }

    /**
     * Calcular cantidad de procesos a auditar (ciclo_rotacion != 'no_auditar')
     */
    public function procesosAuditar()
    {
        return $this->detalles()
            ->where('incluir_en_programa', true)
            ->count();
    }

    /**
     * Calcular promedio de riesgo
     */
    public function riesgoPromedio()
    {
        return $this->detalles()
            ->avg('ponderacion_riesgo');
    }

    /**
     * Validar si puede ser aprobada
     */
    public function puedeSerAprobada()
    {
        return $this->estado === self::ESTADO_BORRADOR 
            && $this->detalles()->count() > 0;
    }

    /**
     * Validar si puede ser editada
     */
    public function puedeSerEditada()
    {
        return $this->estado === self::ESTADO_BORRADOR;
    }
}

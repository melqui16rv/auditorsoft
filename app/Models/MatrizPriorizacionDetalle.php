<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para detalles de Matriz de Priorización
 * 
 * Cada fila representa un proceso con su evaluación de riesgo
 */
class MatrizPriorizacionDetalle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'matriz_priorizacion_detalle';

    protected $fillable = [
        'matriz_priorizacion_id',
        'proceso_id',
        'riesgo_nivel',
        'ponderacion_riesgo',
        'requiere_comite',
        'requiere_entes_reguladores',
        'fecha_ultima_auditoria',
        'dias_transcurridos',
        'ciclo_rotacion',
        'incluir_en_programa',
        'observaciones',
    ];

    protected $casts = [
        'fecha_ultima_auditoria' => 'date',
        'dias_transcurridos' => 'integer',
        'ponderacion_riesgo' => 'integer',
        'requiere_comite' => 'boolean',
        'requiere_entes_reguladores' => 'boolean',
        'incluir_en_programa' => 'boolean',
    ];

    const RIESGO_EXTREMO = 'extremo';
    const RIESGO_ALTO = 'alto';
    const RIESGO_MODERADO = 'moderado';
    const RIESGO_BAJO = 'bajo';

    const CICLO_CADA_ANO = 'cada_ano';
    const CICLO_CADA_DOS_ANOS = 'cada_dos_anos';
    const CICLO_CADA_TRES_ANOS = 'cada_tres_anos';
    const CICLO_NO_AUDITAR = 'no_auditar';

    /**
     * Relación: Matriz padre
     */
    public function matriz(): BelongsTo
    {
        return $this->belongsTo(MatrizPriorizacion::class, 'matriz_priorizacion_id');
    }

    /**
     * Relación: Proceso
     */
    public function proceso(): BelongsTo
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }

    /**
     * Calcular ponderación basada en riesgo
     */
    public static function calcularPonderacion($riesgo)
    {
        $ponderaciones = [
            self::RIESGO_EXTREMO => 5,
            self::RIESGO_ALTO => 4,
            self::RIESGO_MODERADO => 3,
            self::RIESGO_BAJO => 2,
        ];

        return $ponderaciones[$riesgo] ?? 3;
    }

    /**
     * Calcular ciclo de rotación basado en riesgo
     */
    public static function calcularCicloRotacion($riesgo)
    {
        $ciclos = [
            self::RIESGO_EXTREMO => self::CICLO_CADA_ANO,
            self::RIESGO_ALTO => self::CICLO_CADA_DOS_ANOS,
            self::RIESGO_MODERADO => self::CICLO_CADA_TRES_ANOS,
            self::RIESGO_BAJO => self::CICLO_NO_AUDITAR,
        ];

        return $ciclos[$riesgo] ?? self::CICLO_NO_AUDITAR;
    }

    /**
     * Calcular si debe incluirse en programa
     */
    public static function calcularIncluirEnPrograma($cicloRotacion)
    {
        return $cicloRotacion !== self::CICLO_NO_AUDITAR;
    }

    /**
     * Calcular días transcurridos
     */
    public function calcularDiasTranscurridos()
    {
        if (!$this->fecha_ultima_auditoria) {
            return null;
        }

        return now()->diffInDays($this->fecha_ultima_auditoria);
    }

    /**
     * Método para actualizar automáticamente ponderación, ciclo e incluir
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Calcular ponderación automática
            $model->ponderacion_riesgo = static::calcularPonderacion($model->riesgo_nivel);

            // Calcular ciclo de rotación automático
            $model->ciclo_rotacion = static::calcularCicloRotacion($model->riesgo_nivel);

            // Calcular si se incluye en programa
            $model->incluir_en_programa = static::calcularIncluirEnPrograma($model->ciclo_rotacion);

            // Calcular días transcurridos
            $model->dias_transcurridos = $model->calcularDiasTranscurridos();
        });
    }
}

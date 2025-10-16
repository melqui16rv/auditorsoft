<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo para Plan Anual de Auditoría (PAA) - Formato FR-GCE-001
 * 
 * Cumple con:
 * - Decreto 648/2017 (5 roles OCI)
 * - Guía de Auditoría Interna Basada en Riesgos V4
 * - RF 2.1 a RF 2.5
 */
class PAA extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'paa';

    protected $fillable = [
        'codigo',
        'vigencia',
        'fecha_elaboracion',
        'elaborado_por',
        'municipio_id',
        'nombre_entidad',
        'imagen_institucional_path',
        'estado',
        'fecha_aprobacion',
        'aprobado_por_id',
        'observaciones',
        
        // Metadatos FR-GCE-001
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
        'fecha_elaboracion' => 'datetime',
        'fecha_aprobacion' => 'datetime',
        'fecha_aprobacion_formato' => 'date',
        'vigencia' => 'integer',
    ];

    /**
     * Estados posibles del PAA
     */
    const ESTADO_BORRADOR = 'elaboracion';      // Alias para estado inicial
    const ESTADO_ELABORACION = 'elaboracion';
    const ESTADO_APROBADO = 'aprobado';
    const ESTADO_EN_EJECUCION = 'en_ejecucion';
    const ESTADO_FINALIZADO = 'finalizado';
    const ESTADO_ANULADO = 'anulado';

    /**
     * Relación con el Jefe de Control Interno que elaboró
     */
    public function elaboradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'elaborado_por');
    }

    /**
     * Relación con el municipio
     */
    public function municipio(): BelongsTo
    {
        return $this->belongsTo(MunicipioColombia::class, 'municipio_id');
    }

    /**
     * Usuario que aprobó el PAA
     */
    public function aprobadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobado_por_id');
    }

    /**
     * Tareas del PAA organizadas por roles OCI
     */
    public function tareas(): HasMany
    {
        return $this->hasMany(PAATarea::class, 'paa_id');
    }

    /**
     * Auditorías express relacionadas
     */
    public function auditoriasExpress(): HasMany
    {
        return $this->hasMany(AuditoriaExpress::class, 'paa_id');
    }

    /**
     * Funciones de advertencia relacionadas
     */
    public function funcionesAdvertencia(): HasMany
    {
        return $this->hasMany(FuncionAdvertencia::class, 'paa_id');
    }

    /**
     * Acompañamientos relacionados
     */
    public function acompanamientos(): HasMany
    {
        return $this->hasMany(Acompanamiento::class, 'paa_id');
    }

    /**
     * Actos de corrupción relacionados
     */
    public function actosCorrupcion(): HasMany
    {
        return $this->hasMany(ActoCorrupcion::class, 'paa_id');
    }

    /**
     * Usuario que creó el registro
     */
    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Usuario que actualizó el registro
     */
    public function actualizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Usuario que eliminó el registro
     */
    public function eliminador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Scopes
     */

    /**
     * Filtrar por vigencia
     */
    public function scopeVigencia($query, $vigencia)
    {
        return $query->where('vigencia', $vigencia);
    }

    /**
     * Filtrar por estado
     */
    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * PAAs activos (no anulados)
     */
    public function scopeActivos($query)
    {
        return $query->whereNotIn('estado', [self::ESTADO_ANULADO]);
    }

    /**
     * PAAs de la vigencia actual
     */
    public function scopeVigenciaActual($query)
    {
        return $query->where('vigencia', now()->year);
    }

    /**
     * Métodos de negocio
     */

    /**
     * Calcular porcentaje de cumplimiento del PAA (RN-001)
     * 
     * @return float Porcentaje de 0 a 100
     */
    public function calcularPorcentajeCumplimiento(): float
    {
        $totalTareas = $this->tareas()->count();
        
        if ($totalTareas === 0) {
            return 0.0;
        }

        $tareasRealizadas = $this->tareas()
            ->where('estado_tarea', PAATarea::ESTADO_REALIZADA)
            ->count();

        return round(($tareasRealizadas / $totalTareas) * 100, 2);
    }

    /**
     * Calcular cumplimiento por rol OCI
     * 
     * @return array ['rol_id' => ['nombre' => '', 'porcentaje' => 0.0, 'tareas_total' => 0, 'tareas_realizadas' => 0]]
     */
    public function calcularCumplimientoPorRol(): array
    {
        $resultados = [];

        // Obtener todos los roles OCI desde la base de datos
        $rolesOci = \App\Models\RolOci::orderBy('orden')->get();

        foreach ($rolesOci as $rol) {
            $totalTareasRol = $this->tareas()
                ->where('rol_oci_id', $rol->id)
                ->count();

            $tareasRealizadasRol = $this->tareas()
                ->where('rol_oci_id', $rol->id)
                ->where('estado_tarea', PAATarea::ESTADO_REALIZADA)
                ->count();

            $porcentaje = $totalTareasRol > 0 
                ? round(($tareasRealizadasRol / $totalTareasRol) * 100, 2)
                : 0.0;

            $resultados[$rol->id] = [
                'nombre' => $rol->nombre_rol,
                'porcentaje' => $porcentaje,
                'tareas_total' => $totalTareasRol,
                'tareas_realizadas' => $tareasRealizadasRol,
            ];
        }

        return $resultados;
    }

    /**
     * Verificar si el PAA está vencido
     */
    public function estaVencido(): bool
    {
        return $this->vigencia < now()->year;
    }

    /**
     * Verificar si el PAA puede ser editado
     */
    public function puedeSerEditado(): bool
    {
        return in_array($this->estado, [
            self::ESTADO_ELABORACION,
            self::ESTADO_APROBADO,
            self::ESTADO_EN_EJECUCION
        ]);
    }

    /**
     * Aprobar el PAA
     */
    public function aprobar(User $aprobador): bool
    {
        $this->estado = self::ESTADO_APROBADO;
        $this->fecha_aprobacion = now();
        $this->aprobado_por_id = $aprobador->id;
        $this->updated_by = $aprobador->id;

        return $this->save();
    }

    /**
     * Finalizar el PAA
     */
    public function finalizar(User $usuario): bool
    {
        $this->estado = self::ESTADO_FINALIZADO;
        $this->updated_by = $usuario->id;

        return $this->save();
    }

    /**
     * Anular el PAA
     */
    public function anular(User $usuario, string $observacion = null): bool
    {
        $this->estado = self::ESTADO_ANULADO;
        
        if ($observacion) {
            $this->observaciones = ($this->observaciones ? $this->observaciones . "\n\n" : '')
                . "ANULADO: " . $observacion;
        }
        
        $this->updated_by = $usuario->id;

        return $this->save();
    }

    /**
     * Generar código automático para el PAA
     * Formato: PAA-{VIGENCIA}-{CONSECUTIVO}
     */
    public static function generarCodigo(int $vigencia): string
    {
        $ultimoPAA = self::where('vigencia', $vigencia)
            ->orderBy('id', 'desc')
            ->first();

        $consecutivo = $ultimoPAA ? 
            (int) substr($ultimoPAA->codigo, -3) + 1 : 
            1;

        return sprintf('PAA-%d-%03d', $vigencia, $consecutivo);
    }

    /**
     * Atributos calculados
     */

    /**
     * Nombre completo del PAA
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->codigo} - Vigencia {$this->vigencia}";
    }

    /**
     * URL del logo institucional
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->imagen_institucional_path 
            ? asset('storage/' . $this->imagen_institucional_path)
            : null;
    }

    /**
     * Etiqueta de estado con color Bootstrap
     */
    public function getEstadoBadgeAttribute(): string
    {
        return match($this->estado) {
            self::ESTADO_ELABORACION => '<span class="badge bg-secondary">En Elaboración</span>',
            self::ESTADO_APROBADO => '<span class="badge bg-info">Aprobado</span>',
            self::ESTADO_EN_EJECUCION => '<span class="badge bg-primary">En Ejecución</span>',
            self::ESTADO_FINALIZADO => '<span class="badge bg-success">Finalizado</span>',
            self::ESTADO_ANULADO => '<span class="badge bg-danger">Anulado</span>',
            default => '<span class="badge bg-secondary">Desconocido</span>',
        };
    }
}

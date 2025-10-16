<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Modelo para Actos de Corrupción - Formato FR-GCE-004
 * 
 * Evidencia denuncias de posibles actos de corrupción
 * ALTA CONFIDENCIALIDAD - Acceso restringido solo a Jefe OCI
 * 
 * RF 2.9
 */
class ActoCorrupcion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'actos_corrupcion';

    protected $fillable = [
        'paa_id',
        'codigo_registro',
        'fecha_reporte',
        'asunto',
        'descripcion_hechos',
        'tipo_acto',
        'tipo_acto_otro',
        'presuntos_involucrados',
        'area_id',
        'proceso_id',
        'fuente_reporte',
        'nombre_denunciante',
        'es_anonima',
        'evidencias_iniciales',
        'cuantia_estimada',
        'moneda',
        'entidad_competente',
        'entidad_competente_otra',
        'radicado_autoridad',
        'fecha_radicacion',
        'numero_radicado',
        'estado_investigacion',
        'observaciones_seguimiento',
        'es_altamente_confidencial',
        'restricciones_acceso',
        'version_formato',
        'fecha_aprobacion_formato',
        'responsable_archivo',
        'proteccion',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fecha_reporte' => 'date',
        'fecha_radicacion' => 'date',
        'fecha_aprobacion_formato' => 'date',
        'cuantia_estimada' => 'decimal:2',
        'es_anonima' => 'boolean',
        'radicado_autoridad' => 'boolean',
        'es_altamente_confidencial' => 'boolean',
    ];

    /**
     * Tipos de actos de corrupción
     */
    const TIPO_PECULADO = 'peculado';
    const TIPO_COHECHO = 'cohecho';
    const TIPO_CONCUSION = 'concusion';
    const TIPO_PREVARICATO = 'prevaricato';
    const TIPO_CELEBRACION_INDEBIDA = 'celebracion_indebida_contratos';
    const TIPO_TRAFICO_INFLUENCIAS = 'trafico_influencias';
    const TIPO_ENRIQUECIMIENTO_ILICITO = 'enriquecimiento_ilicito';
    const TIPO_SOBORNO_TRANSNACIONAL = 'soborno_transnacional';
    const TIPO_LAVADO_ACTIVOS = 'lavado_activos';
    const TIPO_FRAUDE = 'fraude';
    const TIPO_OTRO = 'otro';

    /**
     * Fuentes del reporte
     */
    const FUENTE_AUDITORIA_INTERNA = 'auditoria_interna';
    const FUENTE_DENUNCIA_CIUDADANA = 'denuncia_ciudadana';
    const FUENTE_ENTE_CONTROL_EXTERNO = 'ente_control_externo';
    const FUENTE_REVISION_INTERNA = 'revision_interna';
    const FUENTE_HALLAZGO_AUDITORIA = 'hallazgo_auditoria';
    const FUENTE_ANONIMA = 'anonima';
    const FUENTE_OTRA = 'otra';

    /**
     * Entidades competentes
     */
    const ENTIDAD_FISCALIA = 'fiscalia_general';
    const ENTIDAD_PROCURADURIA = 'procuraduria_general';
    const ENTIDAD_CONTRALORIA = 'contraloria_general';
    const ENTIDAD_POLICIA_JUDICIAL = 'policia_judicial';
    const ENTIDAD_SUPERINTENDENCIA = 'superintendencia';
    const ENTIDAD_OTRA = 'otra';

    /**
     * Estados de investigación
     */
    const ESTADO_REPORTE_INICIAL = 'reporte_inicial';
    const ESTADO_EN_VERIFICACION = 'en_verificacion';
    const ESTADO_RADICADO_AUTORIDAD = 'radicado_autoridad';
    const ESTADO_EN_INVESTIGACION = 'en_investigacion';
    const ESTADO_ARCHIVADO = 'archivado';
    const ESTADO_CERRADO_SIN_MERITO = 'cerrado_sin_merito';
    const ESTADO_SANCIONADO = 'sancionado';

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
        return $query->where('estado_investigacion', $estado);
    }

    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo_acto', $tipo);
    }

    public function scopeRadicados($query)
    {
        return $query->where('radicado_autoridad', true);
    }

    public function scopePendientesRadicar($query)
    {
        return $query->where('radicado_autoridad', false)
                     ->whereIn('estado_investigacion', [
                         self::ESTADO_REPORTE_INICIAL,
                         self::ESTADO_EN_VERIFICACION
                     ]);
    }

    /**
     * Métodos de negocio
     */

    public function radicarAnteAutoridad(string $entidad, string $numeroRadicado): bool
    {
        $this->radicado_autoridad = true;
        $this->fecha_radicacion = now();
        $this->numero_radicado = $numeroRadicado;
        $this->entidad_competente = $entidad;
        $this->estado_investigacion = self::ESTADO_RADICADO_AUTORIDAD;
        
        return $this->save();
    }

    public function actualizarEstado(string $nuevoEstado, string $observaciones = null): bool
    {
        $this->estado_investigacion = $nuevoEstado;
        
        if ($observaciones) {
            $this->observaciones_seguimiento = ($this->observaciones_seguimiento ? 
                $this->observaciones_seguimiento . "\n\n" : '') . 
                "[" . now()->format('Y-m-d H:i') . "] " . $observaciones;
        }
        
        return $this->save();
    }

    /**
     * Atributos calculados
     */

    public function getEstadoBadgeAttribute(): string
    {
        return match($this->estado_investigacion) {
            self::ESTADO_REPORTE_INICIAL => '<span class="badge bg-warning">Reporte Inicial</span>',
            self::ESTADO_EN_VERIFICACION => '<span class="badge bg-info">En Verificación</span>',
            self::ESTADO_RADICADO_AUTORIDAD => '<span class="badge bg-primary">Radicado ante Autoridad</span>',
            self::ESTADO_EN_INVESTIGACION => '<span class="badge bg-dark">En Investigación</span>',
            self::ESTADO_ARCHIVADO => '<span class="badge bg-secondary">Archivado</span>',
            self::ESTADO_CERRADO_SIN_MERITO => '<span class="badge bg-secondary">Cerrado Sin Mérito</span>',
            self::ESTADO_SANCIONADO => '<span class="badge bg-danger">Sancionado</span>',
            default => '<span class="badge bg-secondary">Desconocido</span>',
        };
    }

    public function getTipoActoBadgeAttribute(): string
    {
        $color = match($this->tipo_acto) {
            self::TIPO_PECULADO, self::TIPO_COHECHO, self::TIPO_FRAUDE => 'danger',
            self::TIPO_PREVARICATO => 'warning',
            default => 'dark',
        };

        $nombre = ucwords(str_replace('_', ' ', $this->tipo_acto));

        return "<span class=\"badge bg-{$color}\">{$nombre}</span>";
    }

    public function getCuantiaFormateadaAttribute(): ?string
    {
        if (!$this->cuantia_estimada) {
            return null;
        }

        return '$' . number_format($this->cuantia_estimada, 2, ',', '.') . ' ' . $this->moneda;
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

        return sprintf('ACORR-%d-%03d', now()->year, $consecutivo);
    }
}

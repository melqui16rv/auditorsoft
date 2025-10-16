<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

/**
 * Modelo para Evidencias (tabla polimórfica)
 * 
 * Soporta evidencias para:
 * - PAA Seguimientos
 * - PIAI
 * - Informes
 * - Acciones Correctivas
 * - Acompañamientos
 * - Funciones de Advertencia
 * - Actos de Corrupción
 * 
 * RF 2.4
 */
class Evidencia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'evidencias';

    protected $fillable = [
        'evidenciable_type',
        'evidenciable_id',
        'nombre_archivo',
        'ruta_archivo',
        'tipo_mime',
        'tamano_kb',
        'tipo_archivo',
        'descripcion',
        'created_by',
        'deleted_by',
    ];

    protected $casts = [
        'tamano_kb' => 'decimal:2',
    ];

    /**
     * Tipos de evidencia
     */
    const TIPO_DOCUMENTO = 'documento';
    const TIPO_IMAGEN = 'imagen';
    const TIPO_AUDIO = 'audio';
    const TIPO_VIDEO = 'video';
    const TIPO_HOJA_CALCULO = 'hoja_calculo';
    const TIPO_PRESENTACION = 'presentacion';
    const TIPO_PDF = 'pdf';
    const TIPO_OTRO = 'otro';

    /**
     * Relación polimórfica
     */
    public function evidenciable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relación con el usuario que creó la evidencia
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con el usuario que eliminó la evidencia
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Scopes
     */

    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo_archivo', $tipo);
    }

    /**
     * Métodos de negocio
     */

    /**
     * Obtener URL pública del archivo
     */
    public function getUrlArchivoAttribute(): string
    {
        return Storage::url($this->ruta_archivo);
    }

    /**
     * Obtener URL de descarga
     */
    public function getUrlDescargaAttribute(): string
    {
        return route('evidencias.descargar', $this->id);
    }

    /**
     * Verificar si el archivo existe físicamente
     */
    public function archivoExiste(): bool
    {
        return Storage::exists($this->ruta_archivo);
    }

    /**
     * Eliminar archivo físico
     */
    public function eliminarArchivo(): bool
    {
        if ($this->archivoExiste()) {
            return Storage::delete($this->ruta_archivo);
        }

        return false;
    }

    /**
     * Obtener tamaño formateado
     */
    public function getTamañoFormateadoAttribute(): string
    {
        $kb = (float) $this->tamano_kb;

        if ($kb >= 1048576) {
            return number_format($kb / 1048576, 2) . ' GB';
        } elseif ($kb >= 1024) {
            return number_format($kb / 1024, 2) . ' MB';
        } else {
            return number_format($kb, 2) . ' KB';
        }
    }

    /**
     * Obtener icono según tipo
     */
    public function getIconoAttribute(): string
    {
        $extension = strtolower($this->tipo_archivo);
        
        return match($extension) {
            'pdf' => 'bi-file-pdf-fill text-danger',
            'jpg', 'jpeg', 'png', 'gif' => 'bi-file-image-fill text-primary',
            'doc', 'docx' => 'bi-file-word-fill text-info',
            'xls', 'xlsx' => 'bi-file-excel-fill text-success',
            'ppt', 'pptx' => 'bi-file-ppt-fill text-warning',
            default => 'bi-file-earmark text-secondary',
        };
    }

    /**
     * Verificar si es una imagen
     */
    public function esImagen(): bool
    {
        return in_array(strtolower($this->tipo_archivo), ['jpg', 'jpeg', 'png', 'gif']) ||
               str_starts_with($this->tipo_mime, 'image/');
    }

    /**
     * Verificar si es un PDF
     */
    public function esPdf(): bool
    {
        return strtolower($this->tipo_archivo) === 'pdf' ||
               $this->tipo_mime === 'application/pdf';
    }

    /**
     * Obtener HTML del icono según tipo de archivo
     */
    public function getIconHtml(): string
    {
        $iconClass = $this->getIconClass();
        $size = '4rem';
        
        return "<i class='{$iconClass}' style='font-size: {$size};'></i>";
    }

    /**
     * Obtener clase de icono según extensión del archivo
     */
    public function getIconClass(): string
    {
        $extension = strtolower($this->tipo_archivo ?? $this->extension ?? '');
        
        return match($extension) {
            'pdf' => 'fas fa-file-pdf text-danger',
            'doc', 'docx' => 'fas fa-file-word text-primary',
            'xls', 'xlsx' => 'fas fa-file-excel text-success',
            'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image text-info',
            'txt' => 'fas fa-file-alt text-secondary',
            default => 'fas fa-file text-secondary',
        };
    }

    /**
     * Event: Al eliminar, también eliminar archivo físico
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($evidencia) {
            $evidencia->eliminarArchivo();
        });
    }
}

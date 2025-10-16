<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionInstitucional extends Model
{
    use HasFactory;

    protected $table = 'configuracion_institucional';

    protected $fillable = [
        'nombre_entidad',
        'nit',
        'sigla',
        'logo_path',
        'direccion',
        'telefono',
        'email_institucional',
        'sitio_web',
        'ciudad_principal',
        'representante_legal',
        'cargo_representante',
        'mision',
        'vision',
        'colores_institucionales',
        'activo',
    ];

    protected $casts = [
        'colores_institucionales' => 'array',
        'activo' => 'boolean',
    ];

    /**
     * Obtener la configuración activa (singleton)
     */
    public static function getConfiguracion()
    {
        return self::where('activo', true)->first() ?? new self();
    }

    /**
     * Obtener la URL del logo
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo_path 
            ? asset('storage/' . $this->logo_path)
            : asset('images/logo-default.png');
    }

    /**
     * Obtener color primario para reportes
     */
    public function getColorPrimarioAttribute()
    {
        return $this->colores_institucionales['primario'] ?? '#1e40af';
    }

    /**
     * Obtener color secundario para reportes
     */
    public function getColorSecundarioAttribute()
    {
        return $this->colores_institucionales['secundario'] ?? '#64748b';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CriterioNormatividad extends Model
{
    use HasFactory;

    protected $table = 'cat_criterios_normatividad';

    protected $fillable = [
        'codigo',
        'tipo_norma',
        'nombre',
        'descripcion',
        'numero_norma',
        'fecha_expedicion',
        'entidad_emisora',
        'url_documento',
        'activo',
    ];

    protected $casts = [
        'fecha_expedicion' => 'date',
        'activo' => 'boolean',
    ];

    /**
     * Scope para criterios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para filtrar por tipo de norma
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo_norma', $tipo);
    }

    /**
     * Obtener nombre completo
     */
    public function getNombreCompletoAttribute()
    {
        $numero = $this->numero_norma ? " No. {$this->numero_norma}" : '';
        return "{$this->nombre}{$numero}";
    }
}

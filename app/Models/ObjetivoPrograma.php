<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjetivoPrograma extends Model
{
    use HasFactory;

    protected $table = 'cat_objetivos_programa';

    protected $fillable = [
        'nombre',
        'descripcion',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Scope para objetivos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}

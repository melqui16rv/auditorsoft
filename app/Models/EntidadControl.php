<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntidadControl extends Model
{
    use HasFactory;

    protected $table = 'cat_entidades_control';

    protected $fillable = [
        'nombre',
        'sigla',
        'tipo',
        'nit',
        'direccion',
        'telefono',
        'email',
        'sitio_web',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Scope para entidades activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}

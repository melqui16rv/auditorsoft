<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proceso extends Model
{
    use HasFactory;

    protected $table = 'cat_procesos';

    protected $fillable = [
        'codigo',
        'nombre',
        'tipo_proceso',
        'descripcion',
        'responsable_proceso',
        'orden',
        'auditable',
        'activo',
    ];

    protected $casts = [
        'auditable' => 'boolean',
        'activo' => 'boolean',
    ];

    /**
     * Relación uno a muchos con Áreas
     */
    public function areas()
    {
        return $this->hasMany(Area::class, 'proceso_id');
    }

    /**
     * Scope para procesos auditables
     */
    public function scopeAuditables($query)
    {
        return $query->where('auditable', true)->where('activo', true);
    }

    /**
     * Scope para filtrar por tipo de proceso
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo_proceso', $tipo);
    }

    /**
     * Scope para procesos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $table = 'cat_areas';

    protected $fillable = [
        'proceso_id',
        'codigo',
        'nombre',
        'descripcion',
        'responsable_area',
        'auditable',
        'activo',
    ];

    protected $casts = [
        'auditable' => 'boolean',
        'activo' => 'boolean',
    ];

    /**
     * Relación muchos a uno con Proceso
     */
    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }

    /**
     * Scope para áreas auditables
     */
    public function scopeAuditables($query)
    {
        return $query->where('auditable', true)->where('activo', true);
    }

    /**
     * Scope para áreas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Obtener nombre completo con proceso
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->proceso->nombre} - {$this->nombre}";
    }
}

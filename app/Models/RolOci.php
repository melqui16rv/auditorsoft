<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolOci extends Model
{
    use HasFactory;

    protected $table = 'cat_roles_oci';

    protected $fillable = [
        'nombre_rol',
        'descripcion',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Relación muchos a muchos con Funcionarios
     */
    public function funcionarios()
    {
        return $this->belongsToMany(Funcionario::class, 'funcionario_rol_oci', 'rol_oci_id', 'funcionario_id')
                    ->withPivot('fecha_asignacion', 'fecha_fin', 'activo')
                    ->withTimestamps();
    }

    /**
     * Scope para obtener solo roles activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}

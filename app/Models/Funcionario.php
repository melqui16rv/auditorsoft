<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Funcionario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'funcionarios';

    protected $fillable = [
        'user_id',
        'nombres',
        'apellidos',
        'cargo_operativo',
        'dependencia',
        'telefono',
        'extension',
        'es_jefe_oci',
        'activo',
        'observaciones',
    ];

    protected $casts = [
        'es_jefe_oci' => 'boolean',
        'activo' => 'boolean',
    ];

    /**
     * Relación uno a uno con User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación muchos a muchos con Roles OCI
     */
    public function rolesOci()
    {
        return $this->belongsToMany(RolOci::class, 'funcionario_rol_oci', 'funcionario_id', 'rol_oci_id')
                    ->withPivot('fecha_asignacion', 'fecha_fin', 'activo')
                    ->withTimestamps();
    }

    /**
     * Obtener roles OCI activos
     */
    public function rolesOciActivos()
    {
        return $this->rolesOci()->wherePivot('activo', true);
    }

    /**
     * Verificar si tiene un rol OCI específico
     */
    public function tieneRolOci($nombreRol)
    {
        return $this->rolesOciActivos()
                    ->where('nombre_rol', $nombreRol)
                    ->exists();
    }

    /**
     * Obtener nombre completo
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    /**
     * Scope para funcionarios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para jefes OCI
     */
    public function scopeJefesOci($query)
    {
        return $query->where('es_jefe_oci', true)->where('activo', true);
    }
}

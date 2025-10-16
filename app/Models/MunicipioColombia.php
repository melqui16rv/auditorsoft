<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MunicipioColombia extends Model
{
    use HasFactory;

    protected $table = 'cat_municipios_colombia';

    protected $fillable = [
        'codigo_dane',
        'nombre',
        'departamento',
        'codigo_departamento',
        'region',
        'capital_departamento',
    ];

    protected $casts = [
        'capital_departamento' => 'boolean',
    ];

    /**
     * Scope para búsqueda por nombre de municipio
     */
    public function scopeBuscarPorNombre($query, $nombre)
    {
        return $query->where('nombre', 'like', "%{$nombre}%");
    }

    /**
     * Scope para filtrar por departamento
     */
    public function scopeDepartamento($query, $departamento)
    {
        return $query->where('departamento', $departamento);
    }

    /**
     * Scope para obtener solo capitales de departamento
     */
    public function scopeCapitales($query)
    {
        return $query->where('capital_departamento', true);
    }

    /**
     * Obtener nombre completo con departamento
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} ({$this->departamento})";
    }

    /**
     * Obtener lista de departamentos únicos
     */
    public static function getDepartamentos()
    {
        return self::select('departamento', 'codigo_departamento')
                   ->distinct()
                   ->orderBy('departamento')
                   ->get();
    }

    /**
     * Obtener municipios por departamento agrupados
     */
    public static function getMunicipiosPorDepartamento()
    {
        return self::orderBy('departamento')
                   ->orderBy('nombre')
                   ->get()
                   ->groupBy('departamento');
    }
}

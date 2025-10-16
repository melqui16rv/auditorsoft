<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlcanceAuditoria extends Model
{
    use HasFactory;

    protected $table = 'cat_alcances_auditoria';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Scope para alcances activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}

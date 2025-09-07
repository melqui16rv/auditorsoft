<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Verificar si el usuario es de un rol específico
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Verificar si el usuario es auditado
     */
    public function isAuditado()
    {
        return $this->role === 'auditado';
    }

    /**
     * Verificar si el usuario es auditor
     */
    public function isAuditor()
    {
        return $this->role === 'auditor';
    }

    /**
     * Verificar si el usuario es jefe auditor
     */
    public function isJefeAuditor()
    {
        return $this->role === 'jefe_auditor';
    }

    /**
     * Verificar si el usuario es super administrador
     */
    public function isSuperAdmin()
    {
        return $this->role === 'super_administrador';
    }

    /**
     * Obtener la ruta del dashboard según el rol
     */
    public function getDashboardRoute()
    {
        return match($this->role) {
            'auditado' => 'auditado.dashboard',
            'auditor' => 'auditor.dashboard', 
            'jefe_auditor' => 'jefe-auditor.dashboard',
            'super_administrador' => 'super-admin.dashboard',
            default => 'login'
        };
    }
}

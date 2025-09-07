<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            // Usuarios originales de prueba
            [
                'name' => 'Usuario Auditado',
                'email' => 'auditado@auditorsoft.com',
                'password' => Hash::make('auditado123'),
                'role' => 'auditado',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Usuario Auditor',
                'email' => 'auditor@auditorsoft.com',
                'password' => Hash::make('auditor123'),
                'role' => 'auditor',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jefe Auditor',
                'email' => 'jefe@auditorsoft.com',
                'password' => Hash::make('jefe123'),
                'role' => 'jefe_auditor',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Super Administrador',
                'email' => 'admin@auditorsoft.com',
                'password' => Hash::make('admin123'),
                'role' => 'super_administrador',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Usuarios adicionales por rol
            // AUDITADOS
            [
                'name' => 'María González Hernández',
                'email' => 'maria.gonzalez@empresaabc.com',
                'password' => Hash::make('maria2025'),
                'role' => 'auditado',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Carlos Rodríguez Pérez',
                'email' => 'carlos.rodriguez@corporacionxyz.com',
                'password' => Hash::make('carlos2025'),
                'role' => 'auditado',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // AUDITORES
            [
                'name' => 'Ana Martínez López',
                'email' => 'ana.martinez@auditorsoft.com',
                'password' => Hash::make('ana2025'),
                'role' => 'auditor',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Luis Fernando Silva',
                'email' => 'luis.silva@auditorsoft.com',
                'password' => Hash::make('luis2025'),
                'role' => 'auditor',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // JEFES AUDITORES
            [
                'name' => 'Patricia Mendoza Torres',
                'email' => 'patricia.mendoza@auditorsoft.com',
                'password' => Hash::make('patricia2025'),
                'role' => 'jefe_auditor',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Roberto Jiménez Castro',
                'email' => 'roberto.jimenez@auditorsoft.com',
                'password' => Hash::make('roberto2025'),
                'role' => 'jefe_auditor',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // SUPER ADMINISTRADORES
            [
                'name' => 'Elena Vásquez Morales',
                'email' => 'elena.vasquez@auditorsoft.com',
                'password' => Hash::make('elena2025'),
                'role' => 'super_administrador',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Miguel Ángel Ruiz',
                'email' => 'miguel.ruiz@auditorsoft.com',
                'password' => Hash::make('miguel2025'),
                'role' => 'super_administrador',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}

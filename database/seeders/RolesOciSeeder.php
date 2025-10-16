<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesOciSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seed de los 5 roles de la OCI según Decreto 648 de 2017
     */
    public function run(): void
    {
        // Verificar si ya existen registros
        if (DB::table('cat_roles_oci')->count() > 0) {
            $this->command->info('✅ La tabla cat_roles_oci ya contiene ' . DB::table('cat_roles_oci')->count() . ' roles. Seeder omitido.');
            return;
        }

        $roles = [
            [
                'nombre_rol' => 'Liderazgo Estratégico',
                'descripcion' => 'Planificación estratégica, asesoría a la alta dirección, administración de riesgos, evaluación del control interno institucional.',
                'orden' => 1,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_rol' => 'Enfoque hacia la Prevención',
                'descripcion' => 'Fomento de cultura del autocontrol, prevención de daño antijurídico, función de advertencia, acompañamiento a procesos.',
                'orden' => 2,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_rol' => 'Relación con Entes Externos de Control',
                'descripcion' => 'Interlocución con organismos de control (Contraloría, Procuraduría, Auditoría General, etc.), atención de requerimientos, seguimiento a planes de mejoramiento.',
                'orden' => 3,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_rol' => 'Evaluación de la Gestión de Riesgo',
                'descripcion' => 'Evaluación y seguimiento del sistema de administración de riesgos, identificación de riesgos emergentes, evaluación de controles.',
                'orden' => 4,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_rol' => 'Evaluación y Seguimiento',
                'descripcion' => 'Auditorías internas, evaluación del Sistema de Control Interno, seguimiento a planes de mejoramiento, evaluación de indicadores de gestión.',
                'orden' => 5,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('cat_roles_oci')->insert($roles);

        $this->command->info('✅ 5 roles OCI del Decreto 648/2017 insertados exitosamente:');
        $this->command->info('   1. Liderazgo Estratégico');
        $this->command->info('   2. Enfoque hacia la Prevención');
        $this->command->info('   3. Relación con Entes Externos de Control');
        $this->command->info('   4. Evaluación de la Gestión de Riesgo');
        $this->command->info('   5. Evaluación y Seguimiento (Auditoría Interna)');
    }
}

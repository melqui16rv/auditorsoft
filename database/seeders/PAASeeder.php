<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\PAA;
use App\Models\PAATarea;
use App\Models\RolOci;
use App\Models\User;
use App\Models\MunicipioColombia;

class PAASeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener municipio de Mariquita (Tolima)
        $mariquita = MunicipioColombia::where('nombre', 'Mariquita')->first();
        $municipioId = $mariquita?->id ?? 1;

        // Obtener el jefe auditor
        $jefeAuditor = User::where('role', 'jefe_auditor')->first();
        $jefe = $jefeAuditor ? $jefeAuditor->id : 1;

        // Obtener usuario admin para auditoría
        $admin = User::where('role', 'super_administrador')->first();
        $adminId = $admin ? $admin->id : 1;

        // Crear PAA 2025
        $paa = PAA::create([
            'codigo' => 'PAA-2025-' . sprintf('%03d', rand(100, 999)),
            'vigencia' => 2025,
            'fecha_elaboracion' => now(),
            'elaborado_por' => $jefe,
            'municipio_id' => $municipioId,
            'nombre_entidad' => 'Alcaldía de Mariquita',
            'imagen_institucional_path' => null,
            'estado' => PAA::ESTADO_ELABORACION,
            'fecha_aprobacion' => null,
            'aprobado_por_id' => null,
            'observaciones' => 'Plan Anual de Auditoría elaborado como ejemplo de prueba',
            
            // Metadatos FR-GCE-001
            'version_formato' => '1.0',
            'fecha_aprobacion_formato' => now()->toDateString(),
            'medio_almacenamiento' => 'Medio magnético',
            'proteccion' => 'Controlado',
            'ubicacion_logica' => 'PC control interno',
            'metodo_recuperacion' => 'Por fecha',
            'responsable_archivo' => 'Jefe de Control Interno',
            'permanencia' => 'Permanente',
            'disposicion_final' => 'Backups',
            
            'created_by' => $jefe,
            'updated_by' => $jefe,
        ]);

        // Obtener los roles OCI
        $rolesOci = RolOci::orderBy('orden')->get();

        // Crear tareas para cada rol OCI
        $tareasData = [
            // Liderazgo Estratégico
            [
                'rol_orden' => 1,
                'descripcion' => 'Establecer canales de comunicación directos y expeditos con el alcalde',
                'fecha_inicio' => '2025-01-01',
                'fecha_fin' => '2025-12-31',
                'estado' => 'realizada',
                'evaluacion' => 'bien',
            ],
            [
                'rol_orden' => 1,
                'descripcion' => 'Presentar reportes mensuales de gestión a la administración',
                'fecha_inicio' => '2025-01-01',
                'fecha_fin' => '2025-12-31',
                'estado' => 'en_proceso',
                'evaluacion' => 'bien',
            ],
            [
                'rol_orden' => 1,
                'descripcion' => 'Participar en sesiones del Comité Institucional de Coordinación de Control Interno',
                'fecha_inicio' => '2025-01-01',
                'fecha_fin' => '2025-12-31',
                'estado' => 'pendiente',
                'evaluacion' => 'pendiente',
            ],

            // Enfoque hacia la prevención
            [
                'rol_orden' => 2,
                'descripcion' => 'Brindar asesorías preventivas a procesos críticos',
                'fecha_inicio' => '2025-02-01',
                'fecha_fin' => '2025-12-31',
                'estado' => 'en_proceso',
                'evaluacion' => 'bien',
            ],
            [
                'rol_orden' => 2,
                'descripcion' => 'Acompañamiento a procesos de contratación pública',
                'fecha_inicio' => '2025-03-01',
                'fecha_fin' => '2025-12-31',
                'estado' => 'pendiente',
                'evaluacion' => 'pendiente',
            ],

            // Relación con entes externos de control
            [
                'rol_orden' => 3,
                'descripcion' => 'Presentar reporte anual a la Contraloría Departamental',
                'fecha_inicio' => '2025-01-15',
                'fecha_fin' => '2025-03-31',
                'estado' => 'pendiente',
                'evaluacion' => 'pendiente',
            ],
            [
                'rol_orden' => 3,
                'descripcion' => 'Atender requerimientos de la Procuraduría Regional',
                'fecha_inicio' => '2025-01-01',
                'fecha_fin' => '2025-12-31',
                'estado' => 'en_proceso',
                'evaluacion' => 'bien',
            ],

            // Evaluación de la gestión de riesgo
            [
                'rol_orden' => 4,
                'descripcion' => 'Evaluar los mapas de riesgo de procesos críticos',
                'fecha_inicio' => '2025-04-01',
                'fecha_fin' => '2025-06-30',
                'estado' => 'pendiente',
                'evaluacion' => 'pendiente',
            ],
            [
                'rol_orden' => 4,
                'descripcion' => 'Revisar implementación de controles recomendados',
                'fecha_inicio' => '2025-05-01',
                'fecha_fin' => '2025-12-31',
                'estado' => 'pendiente',
                'evaluacion' => 'pendiente',
            ],

            // Evaluación y Seguimiento (Auditoría)
            [
                'rol_orden' => 5,
                'descripcion' => 'Ejecutar auditorías internas según programa aprobado',
                'fecha_inicio' => '2025-05-01',
                'fecha_fin' => '2025-11-30',
                'estado' => 'en_proceso',
                'evaluacion' => 'bien',
            ],
            [
                'rol_orden' => 5,
                'descripcion' => 'Realizar auditorías express por solicitud de direccionamiento',
                'fecha_inicio' => '2025-01-01',
                'fecha_fin' => '2025-12-31',
                'estado' => 'pendiente',
                'evaluacion' => 'pendiente',
            ],
            [
                'rol_orden' => 5,
                'descripcion' => 'Hacer seguimiento a planes de mejoramiento',
                'fecha_inicio' => '2025-06-01',
                'fecha_fin' => '2025-12-31',
                'estado' => 'pendiente',
                'evaluacion' => 'pendiente',
            ],
        ];

        // Obtener auditor para asignar responsables
        $auditor = User::where('role', 'auditor')->first();
        $auditorId = $auditor ? $auditor->id : $jefe;

        // Crear las tareas usando inserción directa para evitar conflictos
        $estadosTarea = ['pendiente', 'en_proceso', 'realizada', 'anulada'];
        $rolesOciMap = [
            1 => 'fomento_cultura',
            2 => 'apoyo_fortalecimiento',
            3 => 'investigaciones',
            4 => 'evaluacion_control',
            5 => 'evaluacion_gestion',
        ];

        foreach ($tareasData as $index => $tareaData) {
            $rolOciEnum = $rolesOciMap[$tareaData['rol_orden']] ?? 'evaluacion_gestion';

            DB::table('paa_tareas')->insert([
                'paa_id' => $paa->id,
                'rol_oci' => $rolOciEnum,
                'nombre' => substr($tareaData['descripcion'], 0, 255),
                'descripcion' => $tareaData['descripcion'],
                'fecha_inicio' => $tareaData['fecha_inicio'],
                'fecha_fin' => $tareaData['fecha_fin'],
                'auditor_responsable_id' => $auditorId,
                'estado' => $tareaData['estado'],
                'observaciones' => 'Tarea de prueba del sistema',
                'tipo' => 'auditoria_operativa',
                'created_by' => $jefe,
                'updated_by' => $jefe,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ PAA y tareas de prueba creadas exitosamente');
        $this->command->line("   Código PAA: {$paa->codigo}");
        $this->command->line("   Vigencia: {$paa->vigencia}");
        $this->command->line("   Entidad: {$paa->nombre_entidad}");
        $this->command->line("   Total tareas creadas: " . count($tareasData));
    }
}

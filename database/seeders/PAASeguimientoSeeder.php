<?php

namespace Database\Seeders;

use App\Models\PAA;
use App\Models\PAASeguimiento;
use App\Models\PAATarea;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PAASeguimientoSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener tareas realizadas o en proceso
        $tareas = PAATarea::where('estado', '!=', 'anulada')->get();

        $users = User::where('role', 'auditor')->get();

        if ($tareas->isEmpty() || $users->isEmpty()) {
            $this->command->info('No hay tareas o auditores para crear seguimientos.');
            return;
        }

        DB::beginTransaction();

        try {
            $contador = 0;

            foreach ($tareas as $tarea) {
                // Crear 1-3 seguimientos por tarea
                $numSeguimientos = rand(1, 3);

                for ($i = 0; $i < $numSeguimientos; $i++) {
                    $auditor = $users->random();
                    $fechaRealizacion = $tarea->estado === 'realizada' 
                        ? now()->addDays(rand(0, 3))
                        : null;

                    PAASeguimiento::create([
                        'tarea_id' => $tarea->id,
                        'fecha_realizacion' => $fechaRealizacion,
                        'observaciones' => $this->generarObservacion($tarea, $i + 1),
                        'ente_control_id' => null,
                        'created_by' => $auditor->id,
                        'updated_by' => $auditor->id,
                    ]);

                    $contador++;
                }
            }

            DB::commit();

            $this->command->info("✓ Se crearon {$contador} seguimientos del PAA exitosamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error al crear seguimientos: " . $e->getMessage());
        }
    }

    private function generarObservacion($tarea, $numero): string
    {
        $estados = [
            1 => [
                "Se realizó el seguimiento inicial a la tarea. Se verificó el avance del 0% al {random}%.",
                "Primer punto de control realizado. Equipo responsable reporta avance positivo.",
                "Control inicial ejecutado. Se identificaron {random} hallazgos menores que serán subsanados.",
            ],
            2 => [
                "Segundo seguimiento: Avance de {random}% reportado. Cronograma en tiempo.",
                "Control de mitad de proceso. Se realizó revisión de documentos preliminares.",
                "Punto de control intermedio. Se recomendó ajuste en metodología.",
            ],
            3 => [
                "Seguimiento final: Tarea completada al {random}%. Documentación completa en revisión.",
                "Control final realizado. Se validó cumplimiento de objetivos.",
                "Cierre de seguimiento. Evidencia reunida satisfactoriamente.",
            ],
        ];

        $texto = $estados[$numero][rand(0, count($estados[$numero]) - 1)];
        $texto = str_replace('{random}', rand(20, 100), $texto);

        return $texto;
    }
}

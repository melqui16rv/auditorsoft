<?php

namespace Database\Seeders;

use App\Models\Evidencia;
use App\Models\PAASeguimiento;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EvidenciaSeeder extends Seeder
{
    public function run(): void
    {
        $seguimientos = PAASeguimiento::whereNotNull('fecha_realizacion')
            ->get();

        $users = User::where('role', 'auditor')->get();

        if ($seguimientos->isEmpty() || $users->isEmpty()) {
            $this->command->info('No hay seguimientos realizados o auditores para crear evidencias.');
            return;
        }

        DB::beginTransaction();

        try {
            $contador = 0;

            foreach ($seguimientos as $seguimiento) {
                // Crear 1-3 evidencias por seguimiento
                $numEvidencias = rand(1, 3);

                for ($i = 0; $i < $numEvidencias; $i++) {
                    $auditor = $users->random();
                    $tipos = ['pdf', 'doc', 'xlsx', 'jpg', 'png'];
                    $tipo = $tipos[rand(0, count($tipos) - 1)];

                    $nombreArchivo = "evidencia_" . $seguimiento->id . "_" . ($i + 1) . "." . $tipo;
                    $rutaArchivo = "evidencias/" . Str::random(10) . "/" . $nombreArchivo;

                    Evidencia::create([
                        'evidenciable_type' => PAASeguimiento::class,
                        'evidenciable_id' => $seguimiento->id,
                        'nombre_archivo' => $nombreArchivo,
                        'ruta_archivo' => $rutaArchivo,
                        'tipo_mime' => $this->obtenerMime($tipo),
                        'tamano_kb' => rand(100, 2000),
                        'tamaño_bytes' => rand(100000, 2000000),
                        'extension' => $tipo,
                        'descripcion' => $this->generarDescripcion($tipo, $i + 1),
                        'uploaded_by' => $auditor->id,
                    ]);

                    $contador++;
                }
            }

            DB::commit();

            $this->command->info("✓ Se crearon {$contador} evidencias exitosamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error al crear evidencias: " . $e->getMessage());
        }
    }

    private function obtenerMime(string $tipo): string
    {
        return match($tipo) {
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            default => 'application/octet-stream',
        };
    }

    private function generarDescripcion(string $tipo, int $numero): string
    {
        $descripciones = [
            'pdf' => [
                "Reporte formal de auditoría {$numero}",
                "Documento de evidencia compilada {$numero}",
                "Informe de verificación de cumplimiento",
            ],
            'doc' => [
                "Acta de la reunión de seguimiento {$numero}",
                "Notas de hallazgos identificados",
                "Documento de análisis de resultados",
            ],
            'xlsx' => [
                "Matriz de datos consolidados {$numero}",
                "Seguimiento de indicadores de desempeño",
                "Registro de cumplimiento y avances",
            ],
            'jpg' => [
                "Fotografía de evidencia física {$numero}",
                "Captura de pantalla de sistema",
                "Documento fotográfico de área auditada",
            ],
            'png' => [
                "Gráfico de análisis de datos {$numero}",
                "Captura de proceso ejecutado",
                "Imagen de evidencia documental",
            ],
        ];

        $items = $descripciones[$tipo] ?? ["Evidencia de tipo {$tipo}"];
        return $items[rand(0, count($items) - 1)];
    }
}

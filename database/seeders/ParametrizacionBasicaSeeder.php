<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParametrizacionBasicaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seed inicial de catálogos básicos con datos de ejemplo
     */
    public function run(): void
    {
        $this->seedEntidadesControl();
        $this->seedProcesosYAreas();
        $this->seedCriteriosNormatividad();
        $this->seedAlcancesAuditoria();
        $this->seedObjetivosPrograma();
    }

    private function seedEntidadesControl()
    {
        $entidades = [
            [
                'nombre' => 'Contraloría General de la República',
                'sigla' => 'CGR',
                'tipo' => 'nacional',
                'sitio_web' => 'https://www.contraloria.gov.co',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Procuraduría General de la Nación',
                'sigla' => 'PGN',
                'tipo' => 'nacional',
                'sitio_web' => 'https://www.procuraduria.gov.co',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Auditoría General de la República',
                'sigla' => 'AGR',
                'tipo' => 'nacional',
                'sitio_web' => 'https://www.auditoria.gov.co',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('cat_entidades_control')->insert($entidades);
    }

    private function seedProcesosYAreas()
    {
        // Procesos Estratégicos
        $procesos = [
            [
                'codigo' => 'PE-01',
                'nombre' => 'Direccionamiento Estratégico',
                'tipo_proceso' => 'estrategico',
                'descripcion' => 'Planeación estratégica institucional',
                'orden' => 1,
                'auditable' => true,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'PM-01',
                'nombre' => 'Gestión Ambiental',
                'tipo_proceso' => 'misional',
                'descripcion' => 'Procesos misionales de gestión ambiental',
                'orden' => 2,
                'auditable' => true,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'PA-01',
                'nombre' => 'Gestión Financiera',
                'tipo_proceso' => 'apoyo',
                'descripcion' => 'Gestión presupuestal y financiera',
                'orden' => 3,
                'auditable' => true,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('cat_procesos')->insert($procesos);

        // Áreas de ejemplo
        $areas = [
            [
                'proceso_id' => 1,
                'codigo' => 'PE-01-01',
                'nombre' => 'Planeación Institucional',
                'auditable' => true,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'proceso_id' => 2,
                'codigo' => 'PM-01-01',
                'nombre' => 'Licencias Ambientales',
                'auditable' => true,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'proceso_id' => 3,
                'codigo' => 'PA-01-01',
                'nombre' => 'Contabilidad',
                'auditable' => true,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('cat_areas')->insert($areas);
    }

    private function seedCriteriosNormatividad()
    {
        $criterios = [
            [
                'codigo' => 'DEC-648-2017',
                'tipo_norma' => 'decreto',
                'nombre' => 'Decreto 648 de 2017',
                'descripcion' => 'Por el cual se modifica la estructura de la Función Pública y se dictan otras disposiciones',
                'numero_norma' => '648',
                'fecha_expedicion' => '2017-04-11',
                'entidad_emisora' => 'Presidencia de la República',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'NTC-ISO-19011',
                'tipo_norma' => 'norma_tecnica',
                'nombre' => 'NTC ISO 19011:2018',
                'descripcion' => 'Directrices para la auditoría de sistemas de gestión',
                'numero_norma' => 'ISO 19011',
                'fecha_expedicion' => '2018-01-01',
                'entidad_emisora' => 'ICONTEC',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('cat_criterios_normatividad')->insert($criterios);
    }

    private function seedAlcancesAuditoria()
    {
        $alcances = [
            [
                'nombre' => 'Evaluación del Sistema de Control Interno',
                'descripcion' => 'Evaluación de la efectividad del sistema de control interno institucional',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Auditoría Financiera',
                'descripcion' => 'Revisión de estados financieros y gestión presupuestal',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Auditoría de Cumplimiento',
                'descripcion' => 'Verificación del cumplimiento normativo y procedimental',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('cat_alcances_auditoria')->insert($alcances);
    }

    private function seedObjetivosPrograma()
    {
        $objetivos = [
            [
                'nombre' => 'Evaluar la eficacia del Sistema de Control Interno',
                'descripcion' => 'Determinar si el SCI es adecuado y efectivo para el cumplimiento de objetivos institucionales',
                'orden' => 1,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Verificar el cumplimiento normativo',
                'descripcion' => 'Comprobar el cumplimiento de leyes, decretos, resoluciones y procedimientos internos',
                'orden' => 2,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Evaluar la gestión de riesgos',
                'descripcion' => 'Revisar la identificación, análisis y tratamiento de riesgos institucionales',
                'orden' => 3,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('cat_objetivos_programa')->insert($objetivos);
    }
}

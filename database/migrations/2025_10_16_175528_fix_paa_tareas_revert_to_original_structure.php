<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Revierte paa_tareas a su estructura original con rol_oci_id (FK), 
     * nombre_tarea, estado_tarea, etc.
     */
    public function up(): void
    {
        Schema::table('paa_tareas', function (Blueprint $table) {
            // 1. Renombrar campos de vuelta a la estructura original
            if (Schema::hasColumn('paa_tareas', 'nombre')) {
                $table->renameColumn('nombre', 'nombre_tarea');
            }
            if (Schema::hasColumn('paa_tareas', 'descripcion')) {
                $table->renameColumn('descripcion', 'descripcion_tarea');
            }
            if (Schema::hasColumn('paa_tareas', 'fecha_inicio')) {
                $table->renameColumn('fecha_inicio', 'fecha_inicio_planeada');
            }
            if (Schema::hasColumn('paa_tareas', 'fecha_fin')) {
                $table->renameColumn('fecha_fin', 'fecha_fin_planeada');
            }
            if (Schema::hasColumn('paa_tareas', 'auditor_responsable_id')) {
                $table->renameColumn('auditor_responsable_id', 'responsable_id');
            }
        });

        Schema::table('paa_tareas', function (Blueprint $table) {
            // 2. Eliminar campos extra que no necesitamos
            $columns_to_drop = ['tipo', 'objetivo', 'alcance', 'criterios_auditoria', 'recursos_necesarios'];
            foreach ($columns_to_drop as $col) {
                if (Schema::hasColumn('paa_tareas', $col)) {
                    $table->dropColumn($col);
                }
            }
            
            // 3. Cambiar rol_oci (enum) de vuelta a rol_oci_id (FK)
            if (Schema::hasColumn('paa_tareas', 'rol_oci') && !Schema::hasColumn('paa_tareas', 'rol_oci_id')) {
                $table->dropColumn('rol_oci');
            }
        });

        Schema::table('paa_tareas', function (Blueprint $table) {
            // 4. Agregar rol_oci_id como FK
            if (!Schema::hasColumn('paa_tareas', 'rol_oci_id')) {
                $table->foreignId('rol_oci_id')
                      ->after('paa_id')
                      ->constrained('cat_roles_oci')
                      ->onDelete('restrict')
                      ->comment('Rol OCI según Decreto 648/2017');
            }
            
            // 5. Renombrar estado a estado_tarea
            if (Schema::hasColumn('paa_tareas', 'estado') && !Schema::hasColumn('paa_tareas', 'estado_tarea')) {
                $table->dropColumn('estado');
            }
        });

        Schema::table('paa_tareas', function (Blueprint $table) {
            // 6. Recrear estado_tarea con valores correctos
            if (!Schema::hasColumn('paa_tareas', 'estado_tarea')) {
                $table->enum('estado_tarea', [
                    'pendiente',
                    'en_proceso',
                    'realizado',
                    'anulado',
                    'vencido'
                ])->default('pendiente')->after('responsable_id')->comment('Estado actual de la tarea');
            }
            
            // 7. Agregar campos de fechas reales
            if (!Schema::hasColumn('paa_tareas', 'fecha_inicio_real')) {
                $table->date('fecha_inicio_real')->nullable()->after('fecha_fin_planeada')->comment('Fecha real de inicio');
            }
            if (!Schema::hasColumn('paa_tareas', 'fecha_fin_real')) {
                $table->date('fecha_fin_real')->nullable()->after('fecha_inicio_real')->comment('Fecha real de finalización');
            }
            
            // 8. Agregar evaluacion_general
            if (!Schema::hasColumn('paa_tareas', 'evaluacion_general')) {
                $table->enum('evaluacion_general', ['bien', 'mal', 'pendiente'])
                      ->default('pendiente')
                      ->after('fecha_fin_real')
                      ->comment('Evaluación general de la tarea');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paa_tareas', function (Blueprint $table) {
            // Revertir todos los cambios
            if (Schema::hasColumn('paa_tareas', 'nombre_tarea')) {
                $table->renameColumn('nombre_tarea', 'nombre');
            }
            if (Schema::hasColumn('paa_tareas', 'descripcion_tarea')) {
                $table->renameColumn('descripcion_tarea', 'descripcion');
            }
            if (Schema::hasColumn('paa_tareas', 'fecha_inicio_planeada')) {
                $table->renameColumn('fecha_inicio_planeada', 'fecha_inicio');
            }
            if (Schema::hasColumn('paa_tareas', 'fecha_fin_planeada')) {
                $table->renameColumn('fecha_fin_planeada', 'fecha_fin');
            }
            if (Schema::hasColumn('paa_tareas', 'responsable_id')) {
                $table->renameColumn('responsable_id', 'auditor_responsable_id');
            }
            
            $table->dropColumn(['rol_oci_id', 'estado_tarea', 'fecha_inicio_real', 'fecha_fin_real', 'evaluacion_general']);
            
            $table->enum('rol_oci', [
                'evaluacion_gestion',
                'evaluacion_control',
                'apoyo_fortalecimiento',
                'fomento_cultura',
                'investigaciones'
            ])->after('paa_id');
            
            $table->enum('estado', ['pendiente', 'en_proceso', 'realizada', 'anulada'])->default('pendiente');
        });
    }
};

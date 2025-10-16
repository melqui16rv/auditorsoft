<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Corrección de campos de paa_tareas para sincronizar con controladores
     */
    public function up(): void
    {
        Schema::table('paa_tareas', function (Blueprint $table) {
            // 1. Eliminar FK antes de modificar
            $table->dropForeign(['rol_oci_id']);
            
            // 2. Renombrar campos
            $table->renameColumn('nombre_tarea', 'nombre');
            $table->renameColumn('descripcion_tarea', 'descripcion');
            $table->renameColumn('fecha_inicio_planeada', 'fecha_inicio');
            $table->renameColumn('fecha_fin_planeada', 'fecha_fin');
            $table->renameColumn('estado_tarea', 'estado');
            $table->renameColumn('responsable_id', 'auditor_responsable_id');
        });
        
        // Modificar en segunda llamada para evitar conflictos
        Schema::table('paa_tareas', function (Blueprint $table) {
            // 3. Cambiar rol_oci_id (FK) a rol_oci (string)
            $table->dropColumn('rol_oci_id');
            $table->enum('rol_oci', [
                'evaluacion_gestion',
                'evaluacion_control',
                'apoyo_fortalecimiento',
                'fomento_cultura',
                'investigaciones'
            ])->after('paa_id')->comment('Rol OCI según Decreto 648/2017');
            
            // 4. Actualizar enum de estado (quitar 'vencido', cambiar 'realizado' → 'realizada')
            $table->dropColumn('estado');
        });
        
        Schema::table('paa_tareas', function (Blueprint $table) {
            $table->enum('estado', [
                'pendiente',
                'en_proceso',
                'realizada',
                'anulada'
            ])->default('pendiente')->after('auditor_responsable_id')->comment('Estado actual de la tarea');
            
            // 5. Agregar nuevos campos requeridos
            $table->string('tipo', 100)->after('rol_oci')->comment('Tipo de auditoría');
            $table->text('objetivo')->after('descripcion')->nullable()->comment('Objetivo de la tarea');
            $table->text('alcance')->after('objetivo')->nullable()->comment('Alcance de la auditoría');
            $table->text('criterios_auditoria')->after('alcance')->nullable()->comment('Criterios de auditoría');
            $table->text('recursos_necesarios')->after('criterios_auditoria')->nullable()->comment('Recursos necesarios');
            
            // 6. Eliminar campos no usados
            $table->dropColumn(['fecha_inicio_real', 'fecha_fin_real', 'evaluacion_general']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paa_tareas', function (Blueprint $table) {
            // Revertir cambios
            $table->renameColumn('nombre', 'nombre_tarea');
            $table->renameColumn('descripcion', 'descripcion_tarea');
            $table->renameColumn('fecha_inicio', 'fecha_inicio_planeada');
            $table->renameColumn('fecha_fin', 'fecha_fin_planeada');
            $table->renameColumn('auditor_responsable_id', 'responsable_id');
            
            $table->dropColumn(['tipo', 'objetivo', 'alcance', 'criterios_auditoria', 'recursos_necesarios', 'rol_oci', 'estado']);
            
            $table->enum('estado_tarea', ['pendiente', 'en_proceso', 'realizado', 'anulado', 'vencido'])->default('pendiente');
            $table->foreignId('rol_oci_id')->constrained('cat_roles_oci');
            $table->date('fecha_inicio_real')->nullable();
            $table->date('fecha_fin_real')->nullable();
            $table->enum('evaluacion_general', ['bien', 'mal', 'pendiente'])->default('pendiente');
        });
    }
};

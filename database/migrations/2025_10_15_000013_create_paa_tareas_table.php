<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla de tareas del PAA organizadas por los 5 roles OCI del Decreto 648/2017
     * Cada rol OCI tiene tareas específicas asignadas
     */
    public function up(): void
    {
        Schema::create('paa_tareas', function (Blueprint $table) {
            $table->id();
            
            // Relación con PAA
            $table->foreignId('paa_id')
                  ->constrained('paa')
                  ->onDelete('cascade')
                  ->comment('Plan Anual de Auditoría');
            
            // Rol OCI responsable (Decreto 648/2017)
            $table->foreignId('rol_oci_id')
                  ->constrained('cat_roles_oci')
                  ->onDelete('restrict')
                  ->comment('Rol OCI según Decreto 648/2017');
            
            // Descripción de la tarea
            $table->string('nombre_tarea', 255)->comment('Nombre de la tarea');
            $table->text('descripcion_tarea')->comment('Descripción detallada de la tarea');
            
            // Planificación temporal
            $table->date('fecha_inicio_planeada')->comment('Fecha planeada de inicio');
            $table->date('fecha_fin_planeada')->comment('Fecha planeada de finalización');
            
            // Responsables
            $table->foreignId('responsable_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Funcionario responsable de la tarea');
            
            // Estado de la tarea
            $table->enum('estado_tarea', [
                'pendiente',
                'en_proceso',
                'realizado',
                'anulado',
                'vencido'
            ])->default('pendiente')->comment('Estado actual de la tarea');
            
            // Fechas reales de ejecución
            $table->date('fecha_inicio_real')->nullable()->comment('Fecha real de inicio');
            $table->date('fecha_fin_real')->nullable()->comment('Fecha real de finalización');
            
            // Evaluación general
            $table->enum('evaluacion_general', ['bien', 'mal', 'pendiente'])
                  ->default('pendiente')
                  ->comment('Evaluación general de la tarea');
            
            $table->text('observaciones')->nullable()->comment('Observaciones de la tarea');
            
            // Auditoría de datos
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('paa_id');
            $table->index('rol_oci_id');
            $table->index('estado_tarea');
            $table->index(['paa_id', 'rol_oci_id']);
            $table->index(['paa_id', 'estado_tarea']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paa_tareas');
    }
};

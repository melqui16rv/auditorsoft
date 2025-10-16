<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla para Acompañamientos - Formato FR-GCE-003 (RF 2.8)
     * Rol OCI: "Enfoque hacia la prevención" - Actividades de asesoría
     */
    public function up(): void
    {
        Schema::create('acompanamientos', function (Blueprint $table) {
            $table->id();
            
            // Relación con PAA
            $table->foreignId('paa_id')
                  ->nullable()
                  ->constrained('paa')
                  ->onDelete('set null')
                  ->comment('PAA relacionado');
            
            // Identificación
            $table->string('codigo_registro', 50)->unique()->comment('Código único (ej: AC-2025-001)');
            $table->date('fecha_inicio')->comment('Fecha de inicio del acompañamiento');
            $table->date('fecha_fin')->nullable()->comment('Fecha de finalización');
            
            // Información del acompañamiento
            $table->string('nombre_acompanamiento', 255)->comment('Nombre del acompañamiento');
            $table->text('objetivo')->comment('Objetivo del acompañamiento');
            $table->text('alcance')->comment('Alcance del acompañamiento');
            
            // Área o proceso acompañado
            $table->foreignId('area_id')
                  ->nullable()
                  ->constrained('cat_areas')
                  ->onDelete('set null')
                  ->comment('Área acompañada');
            
            $table->foreignId('proceso_id')
                  ->nullable()
                  ->constrained('cat_procesos')
                  ->onDelete('set null')
                  ->comment('Proceso acompañado');
            
            // Tipo de acompañamiento
            $table->enum('tipo_acompanamiento', [
                'asesoria',
                'capacitacion',
                'seguimiento_preventivo',
                'revision_procesos',
                'implementacion_controles',
                'otro'
            ])->comment('Tipo de acompañamiento');
            
            // Responsables
            $table->foreignId('responsable_oci_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Responsable de la OCI');
            
            $table->string('responsable_area_nombre', 255)->nullable()->comment('Responsable del área');
            $table->string('responsable_area_cargo', 255)->nullable()->comment('Cargo del responsable del área');
            
            // Cronograma
            $table->json('cronograma')->nullable()->comment('Cronograma de actividades (JSON)');
            $table->text('metodologia')->nullable()->comment('Metodología utilizada');
            
            // Resultados
            $table->text('observaciones')->nullable()->comment('Observaciones del acompañamiento');
            $table->text('recomendaciones')->nullable()->comment('Recomendaciones');
            $table->text('compromisos')->nullable()->comment('Compromisos adquiridos');
            
            // Estado
            $table->enum('estado', [
                'planeado',
                'en_proceso',
                'finalizado',
                'suspendido'
            ])->default('planeado')->comment('Estado del acompañamiento');
            
            // Evaluación
            $table->enum('evaluacion_efectividad', [
                'muy_efectivo',
                'efectivo',
                'poco_efectivo',
                'no_efectivo',
                'pendiente'
            ])->default('pendiente')->comment('Evaluación de efectividad');
            
            // Metadatos FR-GCE-003
            $table->string('version_formato', 20)->default('V1');
            $table->date('fecha_aprobacion_formato')->nullable();
            $table->string('responsable_archivo', 100)->default('Jefe OCI');
            
            // Auditoría de datos
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('paa_id');
            $table->index('estado');
            $table->index('tipo_acompanamiento');
            $table->index(['fecha_inicio', 'fecha_fin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acompanamientos');
    }
};

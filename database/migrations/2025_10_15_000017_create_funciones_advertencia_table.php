<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla para Funciones de Advertencia - Formato FR-GCE-002 (RF 2.7)
     * Formaliza avisos sobre peligro o riesgo inminente
     */
    public function up(): void
    {
        Schema::create('funciones_advertencia', function (Blueprint $table) {
            $table->id();
            
            // Relación con PAA
            $table->foreignId('paa_id')
                  ->nullable()
                  ->constrained('paa')
                  ->onDelete('set null')
                  ->comment('PAA relacionado');
            
            // Identificación
            $table->string('codigo_registro', 50)->unique()->comment('Código único (ej: FA-2025-001)');
            $table->date('fecha_advertencia')->comment('Fecha de la advertencia');
            
            // Información de la advertencia
            $table->string('asunto', 500)->comment('Asunto de la advertencia');
            $table->text('descripcion_riesgo')->comment('Descripción del peligro o riesgo inminente');
            
            // Área o proceso afectado
            $table->foreignId('area_id')
                  ->nullable()
                  ->constrained('cat_areas')
                  ->onDelete('set null')
                  ->comment('Área afectada');
            
            $table->foreignId('proceso_id')
                  ->nullable()
                  ->constrained('cat_procesos')
                  ->onDelete('set null')
                  ->comment('Proceso afectado');
            
            // Clasificación del riesgo
            $table->enum('nivel_riesgo', [
                'extremo',
                'alto',
                'moderado',
                'bajo'
            ])->comment('Nivel de riesgo identificado');
            
            $table->enum('tipo_riesgo', [
                'operacional',
                'financiero',
                'legal',
                'reputacional',
                'estrategico',
                'cumplimiento',
                'tecnologico',
                'otro'
            ])->comment('Tipo de riesgo');
            
            // Recomendaciones
            $table->text('recomendaciones')->comment('Recomendaciones de la OCI');
            $table->text('acciones_sugeridas')->nullable()->comment('Acciones sugeridas para mitigar el riesgo');
            
            // Destinatario
            $table->string('destinatario_nombre', 255)->comment('Nombre del destinatario');
            $table->string('destinatario_cargo', 255)->comment('Cargo del destinatario');
            
            // Informe técnico anexo
            $table->text('informe_tecnico')->nullable()->comment('Informe técnico de soporte');
            
            // Respuesta del Comité ICCCI
            $table->enum('decision_comite', [
                'aprobada',
                'improcedente',
                'pendiente'
            ])->default('pendiente')->comment('Decisión del Comité ICCCI');
            
            $table->date('fecha_decision_comite')->nullable()->comment('Fecha de decisión del comité');
            $table->text('observaciones_comite')->nullable()->comment('Observaciones del comité');
            
            // Estado
            $table->enum('estado', [
                'borrador',
                'emitida',
                'en_revision_comite',
                'aprobada',
                'cerrada'
            ])->default('borrador')->comment('Estado de la función de advertencia');
            
            // Metadatos FR-GCE-002
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
            $table->index('nivel_riesgo');
            $table->index('fecha_advertencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funciones_advertencia');
    }
};

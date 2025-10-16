<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla para Auditorías Express (RF 2.6)
     * Auditorías internas especiales solicitadas por el Representante Legal
     */
    public function up(): void
    {
        Schema::create('auditorias_express', function (Blueprint $table) {
            $table->id();
            
            // Relación con PAA
            $table->foreignId('paa_id')
                  ->nullable()
                  ->constrained('paa')
                  ->onDelete('set null')
                  ->comment('PAA relacionado (si aplica)');
            
            // Información básica
            $table->string('codigo_registro', 50)->unique()->comment('Código único (ej: AE-2025-001)');
            $table->string('nombre_auditoria', 255)->comment('Nombre de la auditoría express');
            $table->text('justificacion')->comment('Justificación de la solicitud');
            
            // Solicitante
            $table->string('solicitante_nombre', 255)->comment('Nombre del solicitante (ej: Representante Legal)');
            $table->string('solicitante_cargo', 255)->comment('Cargo del solicitante');
            $table->date('fecha_solicitud')->comment('Fecha de la solicitud');
            
            // Área o proceso a auditar
            $table->foreignId('area_id')
                  ->nullable()
                  ->constrained('cat_areas')
                  ->onDelete('set null')
                  ->comment('Área a auditar');
            
            $table->foreignId('proceso_id')
                  ->nullable()
                  ->constrained('cat_procesos')
                  ->onDelete('set null')
                  ->comment('Proceso a auditar');
            
            // Planificación
            $table->date('fecha_inicio')->nullable()->comment('Fecha de inicio de la auditoría');
            $table->date('fecha_fin')->nullable()->comment('Fecha de finalización');
            
            $table->foreignId('auditor_responsable_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Auditor responsable');
            
            // Estado y resultados
            $table->enum('estado', [
                'solicitada',
                'aprobada',
                'en_ejecucion',
                'finalizada',
                'rechazada'
            ])->default('solicitada')->comment('Estado de la auditoría express');
            
            $table->text('observaciones')->nullable()->comment('Observaciones generales');
            $table->text('conclusiones')->nullable()->comment('Conclusiones de la auditoría');
            
            // Auditoría de datos
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('paa_id');
            $table->index('estado');
            $table->index('fecha_solicitud');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditorias_express');
    }
};

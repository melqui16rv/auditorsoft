<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla de seguimientos/puntos de control de las tareas del PAA
     * Permite registrar avances, evidencias y evaluaciones por cada tarea
     */
    public function up(): void
    {
        Schema::create('paa_seguimientos', function (Blueprint $table) {
            $table->id();
            
            // Relación con tarea del PAA
            $table->foreignId('tarea_id')
                  ->constrained('paa_tareas')
                  ->onDelete('cascade')
                  ->comment('Tarea del PAA');
            
            // Información del punto de control
            $table->date('fecha_seguimiento')->comment('Fecha del seguimiento');
            $table->string('nombre_seguimiento', 255)->nullable()->comment('Nombre del punto de control');
            $table->text('observaciones')->comment('Observaciones del seguimiento');
            
            // Estado del punto de control
            $table->enum('estado_cumplimiento', [
                'pendiente',
                'realizado',
                'anulado',
                'no_aplica'
            ])->default('pendiente')->comment('Estado de cumplimiento');
            
            // Evaluación del seguimiento
            $table->enum('evaluacion', ['bien', 'mal', 'pendiente', 'no_aplica'])
                  ->default('pendiente')
                  ->comment('Evaluación del punto de control');
            
            // Relación con ente de control externo (si aplica)
            $table->foreignId('ente_control_id')
                  ->nullable()
                  ->constrained('cat_entidades_control')
                  ->onDelete('set null')
                  ->comment('Ente de control relacionado (si aplica)');
            
            // Responsable del seguimiento
            $table->foreignId('responsable_seguimiento_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Funcionario que realizó el seguimiento');
            
            // Auditoría de datos
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('tarea_id');
            $table->index('fecha_seguimiento');
            $table->index('estado_cumplimiento');
            $table->index(['tarea_id', 'estado_cumplimiento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paa_seguimientos');
    }
};

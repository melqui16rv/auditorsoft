<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla de detalles de Matriz de Priorización
     * Cada fila = un proceso con su nivel de riesgo
     */
    public function up(): void
    {
        Schema::create('matriz_priorizacion_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matriz_priorizacion_id')->constrained('matriz_priorizacion')->onDelete('cascade');
            $table->foreignId('proceso_id')->constrained('cat_procesos')->onDelete('restrict');
            
            // Riesgo: Manual ingresado por Jefe Auditor
            // Valores: extremo, alto, moderado, bajo
            $table->enum('riesgo_nivel', ['extremo', 'alto', 'moderado', 'bajo'])->default('moderado');
            
            // Ponderación: Calculada automáticamente basada en riesgo
            // extremo=5, alto=4, moderado=3, bajo=2
            $table->integer('ponderacion_riesgo')->default(3);
            
            // Requisitos
            $table->boolean('requiere_comite')->default(false);
            $table->boolean('requiere_entes_reguladores')->default(false);
            
            // Auditoría anterior
            $table->date('fecha_ultima_auditoria')->nullable();
            $table->integer('dias_transcurridos')->nullable(); // Calculado
            
            // Ciclo de rotación calculado automáticamente
            // Valores: cada_ano (extremo), cada_dos_anos (alto), cada_tres_anos (moderado), no_auditar (bajo)
            $table->enum('ciclo_rotacion', ['cada_ano', 'cada_dos_anos', 'cada_tres_anos', 'no_auditar'])->default('no_auditar');
            
            // ¿Se incluye en el programa de auditoría?
            // Automático: TRUE si ciclo_rotacion != 'no_auditar'
            $table->boolean('incluir_en_programa')->default(false);
            
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices para búsquedas
            $table->index('matriz_priorizacion_id');
            $table->index('proceso_id');
            $table->unique(['matriz_priorizacion_id', 'proceso_id'], 'unique_matriz_proceso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matriz_priorizacion_detalle');
    }
};

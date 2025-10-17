<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('programa_auditoria_detalle', function (Blueprint $table) {
            $table->id();
            
            // Referencia al programa
            $table->unsignedBigInteger('programa_auditoria_id');
            $table->foreign('programa_auditoria_id', 'pad_pa_fk')
                ->references('id')
                ->on('programa_auditoria')
                ->onDelete('cascade');
            
            // Referencia a detalle de Matriz de Priorización
            $table->unsignedBigInteger('matriz_priorizacion_detalle_id');
            $table->foreign('matriz_priorizacion_detalle_id', 'pad_mpd_fk')
                ->references('id')
                ->on('matriz_priorizacion_detalle')
                ->onDelete('cascade');
            
            // Proceso y área a auditar
            $table->unsignedBigInteger('proceso_id');
            $table->foreign('proceso_id', 'pad_proc_fk')
                ->references('id')
                ->on('cat_procesos');
            
            $table->unsignedBigInteger('area_id')->nullable();
            $table->foreign('area_id', 'pad_area_fk')
                ->references('id')
                ->on('cat_areas');
            
            // Información de auditoría
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            
            $table->unsignedBigInteger('auditor_responsable_id')->nullable();
            $table->foreign('auditor_responsable_id', 'pad_aud_fk')
                ->references('id')
                ->on('users');
            
            // Objetivos y alcance trasladados desde Matriz
            $table->text('objetivos_programa')->nullable();
            $table->text('alcance_programa')->nullable();
            $table->text('criterios_aplicados')->nullable();
            
            // Estado de ejecución
            $table->enum('estado_auditoria', ['programado', 'en_ejecucion', 'finalizado', 'anulado'])
                ->default('programado');
            
            // Riesgo heredado de Matriz
            $table->enum('riesgo_nivel', ['extremo', 'alto', 'moderado', 'bajo'])->nullable();
            $table->integer('ponderacion_riesgo')->nullable();
            $table->string('ciclo_rotacion', 50)->nullable();
            
            // Observaciones
            $table->text('observaciones')->nullable();
            
            // Auditoría
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by', 'pad_cb_fk')->references('id')->on('users');
            $table->foreign('updated_by', 'pad_ub_fk')->references('id')->on('users');
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programa_auditoria_detalle');
    }
};

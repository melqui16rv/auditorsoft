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
        Schema::create('programa_auditoria', function (Blueprint $table) {
            $table->id();
            
            // Datos básicos
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->year('vigencia');
            
            // Referencia a Matriz de Priorización
            $table->unsignedBigInteger('matriz_priorizacion_id');
            $table->foreign('matriz_priorizacion_id')
                ->references('id')
                ->on('matriz_priorizacion')
                ->onDelete('cascade');
            
            // Responsables
            $table->unsignedBigInteger('elaborado_por');
            $table->foreign('elaborado_por')
                ->references('id')
                ->on('users');
            
            $table->unsignedBigInteger('aprobado_por_id')->nullable();
            $table->foreign('aprobado_por_id')
                ->references('id')
                ->on('users');
            
            // Estados: elaboracion, enviado_aprobacion, aprobado
            $table->enum('estado', ['elaboracion', 'enviado_aprobacion', 'aprobado'])
                ->default('elaboracion');
            
            // Fechas de programa
            $table->date('fecha_inicio_programacion')->nullable();
            $table->date('fecha_fin_programacion')->nullable();
            $table->date('fecha_aprobacion')->nullable();
            
            // Recursos y alcance
            $table->integer('numero_auditores')->nullable();
            $table->text('alcance')->nullable();
            $table->text('objetivos')->nullable();
            
            // Metadata (Decreto 648)
            $table->string('version_formato', 20)->nullable();
            $table->date('fecha_aprobacion_formato')->nullable();
            $table->string('medio_almacenamiento', 100)->nullable();
            $table->string('proteccion', 50)->nullable();
            $table->string('ubicacion_logica', 100)->nullable();
            $table->string('metodo_recuperacion', 50)->nullable();
            $table->string('responsable_archivo', 100)->nullable();
            $table->string('permanencia', 50)->nullable();
            $table->string('disposicion_final', 50)->nullable();
            
            // Auditoría
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programa_auditoria');
    }
};

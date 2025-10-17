<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla para Matriz de Priorización del Universo de Auditoría (RF-3.1)
     * Según Video Access y Guía Auditoría Interna V4
     */
    public function up(): void
    {
        Schema::create('matriz_priorizacion', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->integer('vigencia')->default(date('Y'));
            $table->foreignId('municipio_id')->nullable()->constrained('cat_municipios_colombia')->onDelete('restrict');
            $table->date('fecha_elaboracion');
            $table->foreignId('elaborado_por')->constrained('users')->onDelete('restrict');
            $table->date('fecha_aprobacion')->nullable();
            $table->foreignId('aprobado_por_id')->nullable()->constrained('users')->onDelete('restrict');
            
            // Estados: borrador, validado, aprobado
            $table->enum('estado', ['borrador', 'validado', 'aprobado'])->default('borrador');
            
            // Metadatos FR-GCE
            $table->string('version_formato', 20)->default('V1.0');
            $table->date('fecha_aprobacion_formato')->default('2025-10-17');
            $table->string('medio_almacenamiento', 100)->default('Medio magnético');
            $table->string('proteccion', 50)->default('Controlado');
            $table->string('ubicacion_logica', 100)->default('PC control interno');
            $table->string('metodo_recuperacion', 50)->default('Por fecha');
            $table->string('responsable_archivo', 100)->default('Jefe OCI');
            $table->string('permanencia', 50)->default('Permanente');
            $table->string('disposicion_final', 50)->default('Backups');
            
            // Auditoría
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matriz_priorizacion');
    }
};

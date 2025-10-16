<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla principal del Plan Anual de Auditoría (PAA) - Formato FR-GCE-001
     * Cumple con Decreto 648/2017 y Guía de Auditoría Interna Basada en Riesgos V4
     */
    public function up(): void
    {
        Schema::create('paa', function (Blueprint $table) {
            $table->id();
            
            // Identificación del PAA
            $table->string('codigo_registro', 50)->unique()->comment('Código único del PAA (ej: PAA-2025-001)');
            $table->year('vigencia')->comment('Año de vigencia del PAA');
            $table->date('fecha_elaboracion')->comment('Fecha de creación del PAA');
            
            // Responsable y ubicación
            $table->foreignId('jefe_oci_id')
                  ->constrained('users')
                  ->onDelete('restrict')
                  ->comment('Jefe de Control Interno responsable');
            
            $table->foreignId('municipio_id')
                  ->nullable()
                  ->constrained('cat_municipios_colombia')
                  ->onDelete('set null')
                  ->comment('Municipio/ciudad de la entidad');
            
            // Información institucional
            $table->string('nombre_entidad', 255)->nullable()->comment('Nombre de la entidad');
            $table->string('imagen_institucional_path', 500)->nullable()->comment('Ruta del logo institucional');
            
            // Estados y control
            $table->enum('estado', ['borrador', 'en_ejecucion', 'finalizado', 'anulado'])
                  ->default('borrador')
                  ->comment('Estado del PAA');
            
            $table->date('fecha_aprobacion')->nullable()->comment('Fecha de aprobación del PAA');
            $table->foreignId('aprobado_por_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Usuario que aprobó el PAA');
            
            $table->text('observaciones')->nullable()->comment('Observaciones generales del PAA');
            
            // Metadatos FR-GCE-001 (Decreto 648/2017)
            $table->string('version_formato', 20)->default('V1')->comment('Versión del formato FR-GCE-001');
            $table->date('fecha_aprobacion_formato')->nullable()->comment('Fecha de aprobación del formato');
            $table->string('medio_almacenamiento', 50)->default('Medio magnético')->comment('Medio de almacenamiento');
            $table->string('proteccion', 50)->default('Controlado')->comment('Clasificación de protección');
            $table->string('ubicacion_logica', 100)->default('PC control interno')->comment('Ubicación lógica del archivo');
            $table->string('metodo_recuperacion', 50)->default('Por fecha')->comment('Método de recuperación');
            $table->string('responsable_archivo', 100)->default('Jefe OCI')->comment('Responsable del archivo');
            $table->string('permanencia', 50)->default('Permanente')->comment('Tiempo de permanencia');
            $table->string('disposicion_final', 50)->default('Backups')->comment('Disposición final del archivo');
            
            // Auditoría de datos
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('vigencia');
            $table->index('estado');
            $table->index(['vigencia', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paa');
    }
};

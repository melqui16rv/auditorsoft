<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla polimórfica para evidencias (archivos adjuntos)
     * Soporta evidencias para: PAA seguimientos, PIAIs, informes, acciones correctivas, etc.
     */
    public function up(): void
    {
        Schema::create('evidencias', function (Blueprint $table) {
            $table->id();
            
            // Relación polimórfica
            $table->morphs('evidenciable'); // Crea evidenciable_id y evidenciable_type
            
            // Información del archivo
            $table->string('nombre_archivo', 255)->comment('Nombre original del archivo');
            $table->string('ruta_archivo', 500)->comment('Ruta del archivo en storage');
            $table->string('tipo_mime', 100)->comment('Tipo MIME del archivo');
            $table->unsignedBigInteger('tamaño_bytes')->comment('Tamaño del archivo en bytes');
            $table->string('extension', 20)->comment('Extensión del archivo');
            
            // Descripción y clasificación
            $table->string('titulo', 255)->nullable()->comment('Título de la evidencia');
            $table->text('descripcion')->nullable()->comment('Descripción de la evidencia');
            
            $table->enum('tipo_evidencia', [
                'documento',
                'imagen',
                'audio',
                'video',
                'hoja_calculo',
                'presentacion',
                'pdf',
                'otro'
            ])->default('documento')->comment('Tipo de evidencia');
            
            // Clasificación de seguridad
            $table->string('proteccion', 50)->default('Controlado')->comment('Clasificación de protección');
            $table->boolean('es_confidencial')->default(true)->comment('¿Es información confidencial?');
            
            // Fecha de la evidencia
            $table->date('fecha_evidencia')->nullable()->comment('Fecha de la evidencia o documento');
            
            // Auditoría de datos
            $table->foreignId('uploaded_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Usuario que cargó la evidencia');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices (morphs() ya crea índice para evidenciable_type y evidenciable_id)
            $table->index('tipo_evidencia');
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidencias');
    }
};

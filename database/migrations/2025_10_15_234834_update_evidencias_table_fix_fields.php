<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Corrección de campos de evidencias para sincronizar con controladores
     */
    public function up(): void
    {
        Schema::table('evidencias', function (Blueprint $table) {
            // 1. Renombrar campos
            $table->renameColumn('extension', 'tipo_archivo');
            $table->renameColumn('uploaded_by', 'created_by');
        });
        
        Schema::table('evidencias', function (Blueprint $table) {
            // 2. Cambiar tamaño_bytes a tamano_kb
            $table->dropColumn('tamaño_bytes');
            $table->decimal('tamano_kb', 10, 2)->after('ruta_archivo')->comment('Tamaño del archivo en KB');
            
            // 3. Agregar campo deleted_by
            $table->foreignId('deleted_by')->nullable()->after('created_by')->constrained('users')->onDelete('set null')->comment('Usuario que eliminó la evidencia');
            
            // 4. Eliminar campos no usados
            $table->dropColumn([
                'titulo',
                'tipo_evidencia',
                'proteccion',
                'es_confidencial',
                'fecha_evidencia'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evidencias', function (Blueprint $table) {
            $table->renameColumn('tipo_archivo', 'extension');
            $table->renameColumn('created_by', 'uploaded_by');
            
            $table->dropColumn(['tamano_kb', 'deleted_by']);
            $table->unsignedBigInteger('tamaño_bytes');
            
            $table->string('titulo', 255)->nullable();
            $table->enum('tipo_evidencia', ['documento', 'imagen', 'audio', 'video', 'hoja_calculo', 'presentacion', 'pdf', 'otro'])->default('documento');
            $table->string('proteccion', 50)->default('Controlado');
            $table->boolean('es_confidencial')->default(true);
            $table->date('fecha_evidencia')->nullable();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Corrección de campos de paa_seguimientos para sincronizar con controladores
     */
    public function up(): void
    {
        Schema::table('paa_seguimientos', function (Blueprint $table) {
            // 1. Eliminar foreign key si existe
            if (Schema::hasColumn('paa_seguimientos', 'responsable_seguimiento_id')) {
                $table->dropForeign(['responsable_seguimiento_id']);
            }
        });
        
        Schema::table('paa_seguimientos', function (Blueprint $table) {
            // 2. Agregar campos nuevos solo si no existen
            if (!Schema::hasColumn('paa_seguimientos', 'fecha_realizacion')) {
                $table->datetime('fecha_realizacion')->nullable()->after('observaciones')->comment('Fecha en que se realizó el seguimiento');
            }
            if (!Schema::hasColumn('paa_seguimientos', 'motivo_anulacion')) {
                $table->text('motivo_anulacion')->nullable()->after('observaciones')->comment('Motivo de anulación del seguimiento');
            }
            
            // 3. Eliminar campos no usados si existen
            $columnsToCheck = ['nombre_seguimiento', 'fecha_seguimiento', 'estado_cumplimiento', 'evaluacion', 'responsable_seguimiento_id'];
            $columnsToRemove = [];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('paa_seguimientos', $column)) {
                    $columnsToRemove[] = $column;
                }
            }
            
            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paa_seguimientos', function (Blueprint $table) {
            $table->dropColumn(['fecha_realizacion', 'motivo_anulacion']);
            
            $table->date('fecha_seguimiento');
            $table->string('nombre_seguimiento', 255)->nullable();
            $table->enum('estado_cumplimiento', ['pendiente', 'realizado', 'anulado', 'no_aplica'])->default('pendiente');
            $table->enum('evaluacion', ['bien', 'mal', 'pendiente', 'no_aplica'])->default('pendiente');
            $table->foreignId('responsable_seguimiento_id')->nullable()->constrained('users');
        });
    }
};

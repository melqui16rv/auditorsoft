<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Corrección de campos de paa para sincronizar con controladores
     */
    public function up(): void
    {
        Schema::table('paa', function (Blueprint $table) {
            // 1. Renombrar campos
            $table->renameColumn('codigo_registro', 'codigo');
            $table->renameColumn('jefe_oci_id', 'elaborado_por');
        });
        
        Schema::table('paa', function (Blueprint $table) {
            // 2. Actualizar enum de estado
            $table->dropColumn('estado');
        });
        
        Schema::table('paa', function (Blueprint $table) {
            $table->enum('estado', [
                'elaboracion',
                'aprobado',
                'en_ejecucion',
                'finalizado',
                'anulado'
            ])->default('elaboracion')->after('imagen_institucional_path')->comment('Estado del PAA');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paa', function (Blueprint $table) {
            $table->renameColumn('codigo', 'codigo_registro');
            $table->renameColumn('elaborado_por', 'jefe_oci_id');
            
            $table->dropColumn('estado');
            $table->enum('estado', ['borrador', 'en_ejecucion', 'finalizado', 'anulado'])->default('borrador');
        });
    }
};

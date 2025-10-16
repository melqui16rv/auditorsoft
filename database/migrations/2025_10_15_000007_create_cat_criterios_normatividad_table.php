<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Catálogo de criterios de auditoría (normatividad, legislación, NTC)
     */
    public function up(): void
    {
        Schema::create('cat_criterios_normatividad', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->enum('tipo_norma', ['ley', 'decreto', 'resolucion', 'acuerdo', 'norma_tecnica', 'procedimiento', 'manual', 'otro']);
            $table->string('nombre', 255);
            $table->text('descripcion');
            $table->string('numero_norma', 50)->nullable();
            $table->date('fecha_expedicion')->nullable();
            $table->string('entidad_emisora', 200)->nullable();
            $table->string('url_documento', 500)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_criterios_normatividad');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Catálogo de municipios de Colombia (1,123 municipios organizados por departamento)
     */
    public function up(): void
    {
        Schema::create('cat_municipios_colombia', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_dane', 10)->unique()->comment('Código DANE del municipio');
            $table->string('nombre', 100)->index();
            $table->string('departamento', 100)->index();
            $table->string('codigo_departamento', 5);
            $table->string('region', 50)->nullable()->comment('Región geográfica (Andina, Caribe, etc.)');
            $table->boolean('capital_departamento')->default(false);
            $table->timestamps();
            
            // Índice compuesto para búsquedas rápidas
            $table->index(['departamento', 'nombre'], 'idx_depto_municipio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_municipios_colombia');
    }
};

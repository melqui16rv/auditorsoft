<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Catálogo de procesos institucionales
     */
    public function up(): void
    {
        Schema::create('cat_procesos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 200);
            $table->enum('tipo_proceso', ['estrategico', 'misional', 'apoyo', 'evaluacion_mejora']);
            $table->text('descripcion')->nullable();
            $table->string('responsable_proceso', 200)->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('auditable')->default(true);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_procesos');
    }
};

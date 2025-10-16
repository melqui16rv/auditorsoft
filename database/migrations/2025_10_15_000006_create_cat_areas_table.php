<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Catálogo de áreas auditables por proceso
     */
    public function up(): void
    {
        Schema::create('cat_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_id')->constrained('cat_procesos')->onDelete('cascade');
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->string('responsable_area', 200)->nullable();
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
        Schema::dropIfExists('cat_areas');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Catálogo de entidades de control externas (Contraloría, Procuraduría, etc.)
     */
    public function up(): void
    {
        Schema::create('cat_entidades_control', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200)->unique();
            $table->string('sigla', 20)->nullable();
            $table->enum('tipo', ['nacional', 'departamental', 'municipal', 'especial'])->default('nacional');
            $table->string('nit', 20)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('sitio_web', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_entidades_control');
    }
};

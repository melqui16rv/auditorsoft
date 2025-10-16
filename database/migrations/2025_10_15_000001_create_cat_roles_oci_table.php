<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla de catálogo para los 5 roles de la OCI según Decreto 648/2017
     */
    public function up(): void
    {
        Schema::create('cat_roles_oci', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_rol', 100)->unique();
            $table->text('descripcion');
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_roles_oci');
    }
};

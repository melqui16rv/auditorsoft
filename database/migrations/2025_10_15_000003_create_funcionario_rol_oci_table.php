<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla pivote para la relación muchos a muchos entre funcionarios y roles OCI
     * Un funcionario puede tener múltiples roles OCI asignados
     */
    public function up(): void
    {
        Schema::create('funcionario_rol_oci', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funcionario_id')->constrained('funcionarios')->onDelete('cascade');
            $table->foreignId('rol_oci_id')->constrained('cat_roles_oci')->onDelete('cascade');
            $table->date('fecha_asignacion')->default(now());
            $table->date('fecha_fin')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            // Un funcionario no puede tener el mismo rol OCI asignado más de una vez activo
            $table->unique(['funcionario_id', 'rol_oci_id', 'activo'], 'unique_funcionario_rol_activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funcionario_rol_oci');
    }
};

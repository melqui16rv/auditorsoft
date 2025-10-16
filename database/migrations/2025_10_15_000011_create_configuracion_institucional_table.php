<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla para almacenar la configuración institucional del sistema
     */
    public function up(): void
    {
        Schema::create('configuracion_institucional', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_entidad', 255);
            $table->string('nit', 20)->nullable();
            $table->string('sigla', 50)->nullable();
            $table->string('logo_path', 500)->nullable()->comment('Ruta del logo institucional');
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('email_institucional', 100)->nullable();
            $table->string('sitio_web', 255)->nullable();
            $table->string('ciudad_principal', 100)->nullable();
            $table->string('representante_legal', 200)->nullable();
            $table->string('cargo_representante', 150)->nullable();
            $table->text('mision')->nullable();
            $table->text('vision')->nullable();
            $table->json('colores_institucionales')->nullable()->comment('Colores para reportes (JSON)');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Insertar configuración por defecto
        DB::table('configuracion_institucional')->insert([
            'nombre_entidad' => 'Entidad Territorial',
            'sigla' => 'ET',
            'activo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion_institucional');
    }
};

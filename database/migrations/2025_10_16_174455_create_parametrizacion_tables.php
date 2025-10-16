<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Catálogos de parametrización del sistema de auditoría
     * Según RF-1 del documento de requerimientos
     */
    public function up(): void
    {
        // 1. Entidades de Control Externas (RF 1.3)
        Schema::create('cat_entidades_control', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_entidad', 255);
            $table->string('tipo_entidad', 100)->nullable(); // Contraloría, Procuraduría, etc.
            $table->text('descripcion')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Procesos de la Entidad (RF 1.4)
        Schema::create('cat_procesos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_proceso', 255);
            $table->enum('tipo_proceso', ['estrategico', 'misional', 'apoyo', 'evaluacion_mejora'])
                  ->comment('Tipos según mapa de procesos institucional');
            $table->text('descripcion')->nullable();
            $table->integer('orden')->default(0)->comment('Orden de presentación');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('tipo_proceso');
        });

        // 3. Áreas Auditables (RF 1.4)
        Schema::create('cat_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_id')->constrained('cat_procesos')->onDelete('cascade');
            $table->string('nombre_area', 255);
            $table->text('descripcion')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('proceso_id');
        });

        // 4. Criterios de Auditoría / Normatividad (RF 1.5)
        Schema::create('cat_criterios_normatividad', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_norma', 100)->nullable(); // Ej: Ley 99/93, Decreto 648/2017
            $table->string('nombre_criterio', 255);
            $table->text('descripcion');
            $table->enum('tipo_norma', [
                'ley',
                'decreto',
                'resolucion',
                'ntc',
                'iso',
                'guia',
                'circular',
                'otro'
            ])->default('otro');
            $table->date('fecha_vigencia')->nullable();
            $table->string('url_consulta', 500)->nullable(); // Link a la norma
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('tipo_norma');
            $table->index('codigo_norma');
        });

        // 5. Alcances de Auditoría (RF 1.5)
        Schema::create('cat_alcances_auditoria', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_alcance', 255);
            $table->text('descripcion');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 6. Objetivos del Programa de Auditoría (RF 3.3)
        Schema::create('cat_objetivos_programa', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_objetivo', 255);
            $table->text('descripcion');
            $table->string('referencia_norma', 255)->nullable()->comment('Ej: ISO 19011:2018');
            $table->integer('orden')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 7. Tabla pivote: Asociación de Criterios y Alcances por Área
        // Esto permite validar que solo se seleccionen criterios/alcances válidos para cada área (RN-008)
        Schema::create('area_criterio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('cat_areas')->onDelete('cascade');
            $table->foreignId('criterio_id')->constrained('cat_criterios_normatividad')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['area_id', 'criterio_id']);
        });

        Schema::create('area_alcance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('cat_areas')->onDelete('cascade');
            $table->foreignId('alcance_id')->constrained('cat_alcances_auditoria')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['area_id', 'alcance_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_alcance');
        Schema::dropIfExists('area_criterio');
        Schema::dropIfExists('cat_objetivos_programa');
        Schema::dropIfExists('cat_alcances_auditoria');
        Schema::dropIfExists('cat_criterios_normatividad');
        Schema::dropIfExists('cat_areas');
        Schema::dropIfExists('cat_procesos');
        Schema::dropIfExists('cat_entidades_control');
    }
};

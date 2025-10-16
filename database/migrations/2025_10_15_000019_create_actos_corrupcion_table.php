<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla para Actos de Corrupción - Formato FR-GCE-004 (RF 2.9)
     * Evidencia denuncias de posibles actos de corrupción
     */
    public function up(): void
    {
        Schema::create('actos_corrupcion', function (Blueprint $table) {
            $table->id();
            
            // Relación con PAA
            $table->foreignId('paa_id')
                  ->nullable()
                  ->constrained('paa')
                  ->onDelete('set null')
                  ->comment('PAA relacionado');
            
            // Identificación
            $table->string('codigo_registro', 50)->unique()->comment('Código único (ej: ACORR-2025-001)');
            $table->date('fecha_reporte')->comment('Fecha del reporte');
            
            // Información del acto de corrupción
            $table->string('asunto', 500)->comment('Asunto del reporte');
            $table->text('descripcion_hechos')->comment('Descripción detallada de los hechos');
            
            // Clasificación
            $table->enum('tipo_acto', [
                'peculado',
                'cohecho',
                'concusion',
                'prevaricato',
                'celebracion_indebida_contratos',
                'trafico_influencias',
                'enriquecimiento_ilicito',
                'soborno_transnacional',
                'lavado_activos',
                'fraude',
                'otro'
            ])->comment('Tipo de acto de corrupción');
            
            $table->text('tipo_acto_otro')->nullable()->comment('Especificar si es "otro"');
            
            // Presuntos involucrados
            $table->text('presuntos_involucrados')->comment('Nombres y cargos de presuntos involucrados');
            
            // Área o proceso afectado
            $table->foreignId('area_id')
                  ->nullable()
                  ->constrained('cat_areas')
                  ->onDelete('set null')
                  ->comment('Área afectada');
            
            $table->foreignId('proceso_id')
                  ->nullable()
                  ->constrained('cat_procesos')
                  ->onDelete('set null')
                  ->comment('Proceso afectado');
            
            // Fuente del reporte
            $table->enum('fuente_reporte', [
                'auditoria_interna',
                'denuncia_ciudadana',
                'ente_control_externo',
                'revision_interna',
                'hallazgo_auditoria',
                'anonima',
                'otra'
            ])->comment('Fuente del reporte');
            
            $table->string('nombre_denunciante', 255)->nullable()->comment('Nombre del denunciante (si aplica)');
            $table->boolean('es_anonima')->default(false)->comment('¿Es denuncia anónima?');
            
            // Evidencias iniciales
            $table->text('evidencias_iniciales')->nullable()->comment('Descripción de evidencias iniciales');
            
            // Cuantía estimada (si aplica)
            $table->decimal('cuantia_estimada', 15, 2)->nullable()->comment('Cuantía estimada del daño');
            $table->string('moneda', 10)->default('COP')->comment('Moneda');
            
            // Entidad competente para investigación
            $table->enum('entidad_competente', [
                'fiscalia_general',
                'procuraduria_general',
                'contraloria_general',
                'policia_judicial',
                'superintendencia',
                'otra'
            ])->nullable()->comment('Entidad competente para investigación');
            
            $table->string('entidad_competente_otra', 255)->nullable()->comment('Especificar si es "otra"');
            
            // Radicación ante autoridad
            $table->boolean('radicado_autoridad')->default(false)->comment('¿Se radicó ante autoridad?');
            $table->date('fecha_radicacion')->nullable()->comment('Fecha de radicación');
            $table->string('numero_radicado', 100)->nullable()->comment('Número de radicado');
            
            // Seguimiento
            $table->enum('estado_investigacion', [
                'reporte_inicial',
                'en_verificacion',
                'radicado_autoridad',
                'en_investigacion',
                'archivado',
                'cerrado_sin_merito',
                'sancionado'
            ])->default('reporte_inicial')->comment('Estado de la investigación');
            
            $table->text('observaciones_seguimiento')->nullable()->comment('Observaciones del seguimiento');
            
            // Confidencialidad crítica
            $table->boolean('es_altamente_confidencial')->default(true)->comment('Información altamente confidencial');
            $table->text('restricciones_acceso')->nullable()->comment('Restricciones de acceso');
            
            // Metadatos FR-GCE-004
            $table->string('version_formato', 20)->default('V1');
            $table->date('fecha_aprobacion_formato')->nullable();
            $table->string('responsable_archivo', 100)->default('Jefe OCI');
            $table->string('proteccion', 50)->default('Estrictamente confidencial');
            
            // Auditoría de datos
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('paa_id');
            $table->index('estado_investigacion');
            $table->index('tipo_acto');
            $table->index('fecha_reporte');
            $table->index('radicado_autoridad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actos_corrupcion');
    }
};

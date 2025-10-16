<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Catálogo de alcances de auditoría predefinidos
     */
    public function up(): void
    {
        Schema::create('cat_alcances_auditoria', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200);
            $table->text('descripcion');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_alcances_auditoria');
    }
};

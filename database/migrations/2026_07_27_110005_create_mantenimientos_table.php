<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aeronave_id')->constrained('aeronaves')->cascadeOnDelete();
            $table->foreignId('tecnico_id')->nullable()->constrained('empleados')->nullOnDelete();
            $table->enum('tipo', ['preventivo', 'correctivo', 'inspeccion', 'revision_100h', 'revision_anual']);
            $table->text('descripcion');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->decimal('horas_vuelo_aeronave', 10, 1)->nullable();  // Horas de la aeronave al momento
            $table->decimal('costo', 12, 2)->default(0);
            $table->enum('estado', ['programado', 'en_proceso', 'completado', 'cancelado'])->default('programado');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};

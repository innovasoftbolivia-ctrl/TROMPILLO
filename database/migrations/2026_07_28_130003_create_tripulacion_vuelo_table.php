<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Normaliza la tripulación del vuelo (antes columnas repetidas piloto_id/copiloto_id)
 * en una relación muchos-a-muchos que admite comandante, primer oficial y auxiliares.
 * Aditivo: las columnas piloto_id/copiloto_id de `vuelos` se conservan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tripulacion_vuelo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vuelo_id')->constrained('vuelos')->cascadeOnDelete();
            $table->foreignId('empleado_id')->constrained('empleados')->cascadeOnDelete();
            $table->enum('rol', ['comandante', 'primer_oficial', 'auxiliar', 'despachador']);
            $table->timestamps();
            $table->unique(['vuelo_id', 'empleado_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tripulacion_vuelo');
    }
};

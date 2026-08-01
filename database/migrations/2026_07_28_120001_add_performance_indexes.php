<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Índices sobre las columnas usadas en filtros (WHERE) y ordenamientos (ORDER BY)
 * de los listados. Las FK y campos únicos ya están indexados por Laravel.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vuelos', function (Blueprint $table) {
            $table->index('estado');
            $table->index('tipo');
            $table->index('salida_programada');
        });
        Schema::table('reservas', function (Blueprint $table) {
            $table->index('estado');
            $table->index('fecha_reserva');
        });
        Schema::table('mantenimientos', function (Blueprint $table) {
            $table->index('estado');
            $table->index('tipo');
            $table->index('fecha_inicio');
        });
        Schema::table('aeronaves', function (Blueprint $table) {
            $table->index('estado');
        });
        Schema::table('empleados', function (Blueprint $table) {
            $table->index('cargo');
        });
        Schema::table('aeropuertos', function (Blueprint $table) {
            $table->index('tipo');
        });
        Schema::table('pilotos', function (Blueprint $table) {
            $table->index('tipo_licencia');
        });
        Schema::table('envios_carga', function (Blueprint $table) {
            $table->index('estado');
        });
        Schema::table('rutas', function (Blueprint $table) {
            $table->index('activa');
        });
    }

    public function down(): void
    {
        Schema::table('vuelos', function (Blueprint $table) {
            $table->dropIndex(['estado']);
            $table->dropIndex(['tipo']);
            $table->dropIndex(['salida_programada']);
        });
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropIndex(['estado']);
            $table->dropIndex(['fecha_reserva']);
        });
        Schema::table('mantenimientos', function (Blueprint $table) {
            $table->dropIndex(['estado']);
            $table->dropIndex(['tipo']);
            $table->dropIndex(['fecha_inicio']);
        });
        Schema::table('aeronaves', function (Blueprint $table) {
            $table->dropIndex(['estado']);
        });
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropIndex(['cargo']);
        });
        Schema::table('aeropuertos', function (Blueprint $table) {
            $table->dropIndex(['tipo']);
        });
        Schema::table('pilotos', function (Blueprint $table) {
            $table->dropIndex(['tipo_licencia']);
        });
        Schema::table('envios_carga', function (Blueprint $table) {
            $table->dropIndex(['estado']);
        });
        Schema::table('rutas', function (Blueprint $table) {
            $table->dropIndex(['activa']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aeronaves', function (Blueprint $table) {
            $table->id();
            $table->string('matricula', 10)->unique();            // Ej: HK-1234
            $table->string('modelo');                             // Ej: Cessna 206 Stationair
            $table->string('fabricante');                         // Cessna, Piper, Beechcraft...
            $table->year('ano_fabricacion')->nullable();
            $table->unsignedSmallInteger('capacidad_pasajeros');
            $table->decimal('capacidad_carga_kg', 8, 2)->default(0);
            $table->decimal('peso_vacio_kg', 8, 2)->nullable();
            $table->decimal('peso_maximo_despegue_kg', 8, 2)->nullable();  // MTOW
            $table->integer('autonomia_km')->nullable();
            $table->integer('velocidad_crucero_kmh')->nullable();
            $table->decimal('horas_vuelo_totales', 10, 1)->default(0);
            $table->date('fecha_ultima_revision')->nullable();
            $table->date('fecha_proxima_revision')->nullable();
            $table->enum('estado', ['activa', 'mantenimiento', 'inactiva', 'retirada'])->default('activa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aeronaves');
    }
};

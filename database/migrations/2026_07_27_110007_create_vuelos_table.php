<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vuelos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_vuelo', 15)->nullable();
            $table->foreignId('ruta_id')->nullable()->constrained('rutas')->nullOnDelete();       // Null en charter
            $table->foreignId('origen_id')->constrained('aeropuertos')->restrictOnDelete();
            $table->foreignId('destino_id')->constrained('aeropuertos')->restrictOnDelete();
            $table->foreignId('aeronave_id')->nullable()->constrained('aeronaves')->nullOnDelete();
            $table->foreignId('piloto_id')->nullable()->constrained('pilotos')->nullOnDelete();
            $table->foreignId('copiloto_id')->nullable()->constrained('pilotos')->nullOnDelete();
            $table->enum('tipo', ['regular', 'charter', 'carga', 'ambulancia'])->default('regular');
            $table->dateTime('salida_programada');
            $table->dateTime('llegada_programada')->nullable();
            $table->dateTime('salida_real')->nullable();
            $table->dateTime('llegada_real')->nullable();
            $table->unsignedSmallInteger('asientos_disponibles')->default(0);
            $table->decimal('precio', 12, 2)->default(0);
            $table->enum('estado', ['programado', 'confirmado', 'abordando', 'en_vuelo', 'aterrizado', 'cancelado', 'retrasado'])->default('programado');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vuelos');
    }
};

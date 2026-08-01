<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aeropuertos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_oaci', 4)->unique();          // Código OACI (ICAO), ej: SKBO
            $table->string('codigo_iata', 3)->nullable();         // IATA, muchas pistas no tienen
            $table->string('nombre');
            $table->string('ciudad');
            $table->string('departamento')->nullable();
            $table->string('pais')->default('Bolivia');
            $table->enum('tipo', ['aeropuerto', 'aerodromo', 'pista'])->default('aerodromo');
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->integer('elevacion_pies')->nullable();
            $table->integer('longitud_pista_m')->nullable();      // Largo de pista (crítico para avionetas)
            $table->enum('superficie_pista', ['asfalto', 'concreto', 'tierra', 'pasto'])->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aeropuertos');
    }
};

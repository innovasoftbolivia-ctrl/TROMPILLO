<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo geográfico normalizado: paises -> departamentos -> ciudades.
 * Los aeropuertos y pasajeros referencian el catálogo (aditivo: se conservan
 * las columnas de texto existentes para no romper nada).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paises', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('codigo_iso', 3)->nullable();
            $table->string('gentilicio')->nullable();
            $table->timestamps();
        });

        Schema::create('departamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pais_id')->constrained('paises')->cascadeOnDelete();
            $table->string('nombre');
            $table->timestamps();
            $table->unique(['pais_id', 'nombre']);
        });

        Schema::create('ciudades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')->constrained('departamentos')->cascadeOnDelete();
            $table->string('nombre');
            $table->timestamps();
            $table->unique(['departamento_id', 'nombre']);
        });

        Schema::table('aeropuertos', function (Blueprint $table) {
            $table->foreignId('ciudad_id')->nullable()->after('ciudad')->constrained('ciudades')->nullOnDelete();
        });

        Schema::table('pasajeros', function (Blueprint $table) {
            $table->foreignId('pais_id')->nullable()->after('nacionalidad')->constrained('paises')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pasajeros', function (Blueprint $table) {
            $table->dropForeign(['pais_id']);
            $table->dropColumn('pais_id');
        });
        Schema::table('aeropuertos', function (Blueprint $table) {
            $table->dropForeign(['ciudad_id']);
            $table->dropColumn('ciudad_id');
        });
        Schema::dropIfExists('ciudades');
        Schema::dropIfExists('departamentos');
        Schema::dropIfExists('paises');
    }
};

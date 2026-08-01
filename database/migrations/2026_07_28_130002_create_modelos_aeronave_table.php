<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Normaliza las especificaciones del modelo de aeronave (antes duplicadas por
 * cada matrícula). Aditivo: `aeronaves` gana `modelo_aeronave_id` y se conservan
 * las columnas existentes para no romper CRUD/procedures.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modelos_aeronave', function (Blueprint $table) {
            $table->id();
            $table->string('fabricante');
            $table->string('modelo');
            $table->unsignedSmallInteger('capacidad_pasajeros');
            $table->decimal('capacidad_carga_kg', 8, 2)->default(0);
            $table->decimal('peso_vacio_kg', 8, 2)->nullable();
            $table->decimal('peso_maximo_despegue_kg', 8, 2)->nullable();
            $table->integer('autonomia_km')->nullable();
            $table->integer('velocidad_crucero_kmh')->nullable();
            $table->timestamps();
            $table->unique(['fabricante', 'modelo']);
        });

        Schema::table('aeronaves', function (Blueprint $table) {
            $table->foreignId('modelo_aeronave_id')->nullable()->after('id')->constrained('modelos_aeronave')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('aeronaves', function (Blueprint $table) {
            $table->dropForeign(['modelo_aeronave_id']);
            $table->dropColumn('modelo_aeronave_id');
        });
        Schema::dropIfExists('modelos_aeronave');
    }
};

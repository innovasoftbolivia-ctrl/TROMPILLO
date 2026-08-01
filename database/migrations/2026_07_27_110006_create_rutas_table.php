<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rutas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origen_id')->constrained('aeropuertos')->restrictOnDelete();
            $table->foreignId('destino_id')->constrained('aeropuertos')->restrictOnDelete();
            $table->integer('distancia_km')->nullable();
            $table->integer('duracion_estimada_min')->nullable();
            $table->decimal('precio_base', 12, 2)->default(0);
            $table->boolean('activa')->default(true);
            $table->timestamps();

            $table->unique(['origen_id', 'destino_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutas');
    }
};

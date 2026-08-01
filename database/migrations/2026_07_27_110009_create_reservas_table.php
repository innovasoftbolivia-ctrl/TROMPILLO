<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique();              // Localizador / PNR
            $table->foreignId('vuelo_id')->constrained('vuelos')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();       // Agente que registra
            $table->foreignId('pasajero_id')->nullable()->constrained('pasajeros')->nullOnDelete();  // Titular
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada', 'completada'])->default('pendiente');
            $table->decimal('total', 12, 2)->default(0);
            $table->dateTime('fecha_reserva')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boletos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_boleto', 20)->unique();
            $table->foreignId('reserva_id')->constrained('reservas')->cascadeOnDelete();
            $table->foreignId('pasajero_id')->constrained('pasajeros')->restrictOnDelete();
            $table->foreignId('vuelo_id')->constrained('vuelos')->cascadeOnDelete();
            $table->string('asiento', 5)->nullable();
            $table->decimal('precio', 12, 2)->default(0);
            $table->decimal('equipaje_kg', 5, 2)->default(0);
            $table->boolean('checkin')->default(false);
            $table->enum('estado', ['emitido', 'usado', 'cancelado', 'no_show'])->default('emitido');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boletos');
    }
};

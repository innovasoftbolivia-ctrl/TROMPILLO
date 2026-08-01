<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')->constrained('reservas')->cascadeOnDelete();
            $table->decimal('monto', 12, 2);
            $table->enum('metodo', ['efectivo', 'tarjeta_credito', 'tarjeta_debito', 'transferencia', 'pse', 'nequi']);
            $table->enum('estado', ['pendiente', 'pagado', 'rechazado', 'reembolsado'])->default('pendiente');
            $table->string('referencia')->nullable();
            $table->dateTime('fecha_pago')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};

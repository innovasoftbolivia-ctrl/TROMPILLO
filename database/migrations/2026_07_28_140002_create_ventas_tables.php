<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ventas con estructura maestro-detalle:
 *  - ventas         (cabecera / maestro): cliente, vendedor, totales, estado.
 *  - venta_detalles (líneas / detalle): cada boleto o envío de carga vendido.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->foreignId('persona_id')->nullable()->constrained('personas')->nullOnDelete();   // cliente
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();       // vendedor
            $table->foreignId('reserva_id')->nullable()->constrained('reservas')->nullOnDelete();
            $table->dateTime('fecha');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('estado', ['pendiente', 'pagada', 'anulada'])->default('pendiente');
            $table->string('metodo_pago', 30)->nullable();
            $table->timestamps();
        });

        Schema::create('venta_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->foreignId('boleto_id')->nullable()->constrained('boletos')->nullOnDelete();
            $table->foreignId('envio_carga_id')->nullable()->constrained('envios_carga')->nullOnDelete();
            $table->string('descripcion');
            $table->unsignedInteger('cantidad')->default(1);
            $table->decimal('precio_unitario', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta_detalles');
        Schema::dropIfExists('ventas');
    }
};

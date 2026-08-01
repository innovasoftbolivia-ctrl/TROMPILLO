<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Opción A: el dinero (pagos) pasa a pertenecer a la VENTA (fuente de verdad).
 * Se agrega pagos.venta_id; se conserva reserva_id por compatibilidad.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->foreignId('venta_id')->nullable()->after('reserva_id')->constrained('ventas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['venta_id']);
            $table->dropColumn('venta_id');
        });
    }
};

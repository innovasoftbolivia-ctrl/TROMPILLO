<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('envios_carga', function (Blueprint $table) {
            $table->id();
            $table->string('guia', 20)->unique();                // Número de guía de carga
            $table->foreignId('vuelo_id')->nullable()->constrained('vuelos')->nullOnDelete();
            $table->string('remitente');
            $table->string('remitente_documento', 30)->nullable();
            $table->string('destinatario');
            $table->string('destinatario_documento', 30)->nullable();
            $table->text('descripcion');
            $table->decimal('peso_kg', 8, 2);
            $table->decimal('valor_declarado', 12, 2)->default(0);
            $table->decimal('costo_envio', 12, 2)->default(0);
            $table->enum('estado', ['registrado', 'en_transito', 'entregado', 'devuelto'])->default('registrado');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('envios_carga');
    }
};

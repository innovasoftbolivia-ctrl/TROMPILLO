<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pilotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados')->cascadeOnDelete();
            $table->string('licencia_numero', 30)->unique();
            $table->enum('tipo_licencia', ['PPL', 'CPL', 'ATPL', 'PCA']);  // Privado / Comercial / Línea / Piloto Comercial Avión
            $table->decimal('horas_vuelo', 10, 1)->default(0);
            $table->date('vencimiento_licencia')->nullable();
            $table->date('vencimiento_medico')->nullable();      // Certificación médica aeronáutica
            $table->string('habilitaciones')->nullable();        // Type ratings / equipos habilitados
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pilotos');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('tipo_documento', 10)->default('CC');
            $table->string('numero_documento', 30)->unique();
            $table->enum('cargo', ['piloto', 'copiloto', 'tecnico', 'despachador', 'administrativo', 'ventas', 'gerente']);
            $table->string('telefono', 30)->nullable();
            $table->string('email')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->date('fecha_contratacion')->nullable();
            $table->decimal('salario', 12, 2)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};

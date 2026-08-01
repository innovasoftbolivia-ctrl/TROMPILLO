<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pasajeros', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('tipo_documento', 10)->default('CC');
            $table->string('numero_documento', 30);
            $table->date('fecha_nacimiento')->nullable();
            $table->string('nacionalidad')->default('Boliviana');
            $table->string('telefono', 30)->nullable();
            $table->string('email')->nullable();
            $table->decimal('peso_kg', 5, 2)->nullable();        // Necesario para peso y balance en avionetas
            $table->string('contacto_emergencia')->nullable();
            $table->string('telefono_emergencia', 30)->nullable();
            $table->timestamps();

            $table->unique(['tipo_documento', 'numero_documento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pasajeros');
    }
};

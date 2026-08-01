<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Normaliza los datos comunes de pasajeros/empleados/pilotos en una tabla `personas`
 * con separación natural / jurídica (herencia por tablas de subtipo).
 * Aditivo: pasajeros y empleados ganan persona_id; se conservan sus columnas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_persona', ['natural', 'juridica'])->default('natural');
            $table->string('tipo_documento', 10)->default('CI');   // CI, NIT, Pasaporte, CEX
            $table->string('numero_documento', 30);
            $table->string('telefono', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('direccion')->nullable();
            $table->foreignId('pais_id')->nullable()->constrained('paises')->nullOnDelete();
            $table->timestamps();
            $table->unique(['tipo_documento', 'numero_documento']);
        });

        Schema::create('personas_naturales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->unique()->constrained('personas')->cascadeOnDelete();
            $table->string('nombres');
            $table->string('apellidos');
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('sexo', ['M', 'F', 'X'])->nullable();
            $table->timestamps();
        });

        Schema::create('personas_juridicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->unique()->constrained('personas')->cascadeOnDelete();
            $table->string('razon_social');
            $table->string('nit', 20);
            $table->string('representante_legal')->nullable();
            $table->timestamps();
        });

        Schema::table('pasajeros', function (Blueprint $table) {
            $table->foreignId('persona_id')->nullable()->after('id')->constrained('personas')->nullOnDelete();
        });

        Schema::table('empleados', function (Blueprint $table) {
            $table->foreignId('persona_id')->nullable()->after('id')->constrained('personas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropForeign(['persona_id']);
            $table->dropColumn('persona_id');
        });
        Schema::table('pasajeros', function (Blueprint $table) {
            $table->dropForeign(['persona_id']);
            $table->dropColumn('persona_id');
        });
        Schema::dropIfExists('personas_juridicas');
        Schema::dropIfExists('personas_naturales');
        Schema::dropIfExists('personas');
    }
};

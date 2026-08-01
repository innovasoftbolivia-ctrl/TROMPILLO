<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * La aerolínea es nacional: los departamentos son todos de Bolivia, así que
 * `paises` deja de estar en la cadena geográfica. Se quita departamentos.pais_id.
 * `paises` queda SOLO para la nacionalidad de personas/pasajeros.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departamentos', function (Blueprint $table) {
            $table->dropForeign(['pais_id']);
            $table->dropUnique(['pais_id', 'nombre']);
            $table->dropColumn('pais_id');
            $table->unique('nombre');
        });
    }

    public function down(): void
    {
        Schema::table('departamentos', function (Blueprint $table) {
            $table->dropUnique(['nombre']);
            $table->foreignId('pais_id')->nullable()->after('id')->constrained('paises')->cascadeOnDelete();
            $table->unique(['pais_id', 'nombre']);
        });
    }
};

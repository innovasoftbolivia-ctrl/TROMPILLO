<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('rol', ['admin', 'operador', 'vendedor', 'piloto'])->default('vendedor')->after('email');
            $table->foreignId('empleado_id')->nullable()->after('rol')->constrained('empleados')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['empleado_id']);
            $table->dropColumn(['rol', 'empleado_id']);
        });
    }
};

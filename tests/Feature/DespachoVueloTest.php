<?php

namespace Tests\Feature;

use App\Models\Aeronave;
use App\Models\Piloto;
use App\Models\User;
use App\Models\Vuelo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DespachoVueloTest extends TestCase
{
    public function test_despachar_vuelo_stored_procedure()
    {
        $user = User::factory()->create();
        
        $vuelo = Vuelo::where('estado', 'programado')->first();
        $aeronave = Aeronave::where('estado', 'activa')->first();
        $piloto = Piloto::first();

        if (!$vuelo || !$aeronave || !$piloto) {
            $this->markTestSkipped('Datos incompletos en la BD para probar el despacho.');
        }

        // Llamar al SP
        DB::statement('CALL sp_despachar_vuelo(?, ?, ?, ?)', [
            $vuelo->id,
            $aeronave->id,
            $piloto->id,
            null // copiloto_id
        ]);

        // Verificar el cambio de estado y asignaciones
        $vueloRefrescado = Vuelo::find($vuelo->id);

        $this->assertEquals('abordando', $vueloRefrescado->estado);
        $this->assertEquals($aeronave->id, $vueloRefrescado->aeronave_id);
        $this->assertEquals($piloto->id, $vueloRefrescado->piloto_id);

        // Restaurar estado (limpieza)
        $vueloRefrescado->estado = 'programado';
        $vueloRefrescado->aeronave_id = null;
        $vueloRefrescado->piloto_id = null;
        $vueloRefrescado->save();
    }
}

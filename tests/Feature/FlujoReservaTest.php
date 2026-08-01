<?php

namespace Tests\Feature;

use App\Models\Pasajero;
use App\Models\User;
use App\Models\Vuelo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FlujoReservaTest extends TestCase
{
    // Usamos transaction para no afectar la DB real de pruebas (o setup similar)
    // Asumiendo que existe data semilla en la BD (vuelos, pasajeros).

    public function test_crear_reserva_completa_stored_procedure()
    {
        // 1. Setup Data
        $user = User::factory()->create();
        
        // Asumiendo que el seeder ya generó vuelos y pasajeros
        $vuelo = Vuelo::where('estado', 'programado')->first();
        $pasajero = Pasajero::first();

        if (!$vuelo || !$pasajero) {
            $this->markTestSkipped('No hay vuelos programados o pasajeros en la BD para probar.');
        }

        // 2. Prepare parameters
        $codigoReserva = 'TEST' . rand(100, 999);
        $boletosJson = json_encode([
            [
                'pasajero_id' => $pasajero->id,
                'asiento' => '1A',
                'precio' => 1500.00,
                'equipaje_kg' => 20
            ]
        ]);

        // 3. Execute SP
        DB::statement('CALL sp_crear_reserva_completa(?,?,?,?,?,?,?,?,?,?,?,?, @rid)', [
            $codigoReserva, 
            $vuelo->id, 
            $user->id, 
            $pasajero->id, 
            'confirmada', 
            now()->format('Y-m-d H:i:s'),
            'Notas test', 
            $boletosJson, 
            1500.00, 
            'efectivo',
            'pagado', 
            'REF-TEST-123'
        ]);

        $reservaId = DB::selectOne('SELECT @rid AS id')->id;

        // 4. Assertions
        $this->assertNotNull($reservaId);
        
        $this->assertDatabaseHas('reservas', [
            'id' => $reservaId,
            'codigo' => $codigoReserva,
            'estado' => 'confirmada'
        ]);

        $this->assertDatabaseHas('boletos', [
            'vuelo_id' => $vuelo->id,
            'pasajero_id' => $pasajero->id,
            'asiento' => '1A',
            'precio' => 1500.00
        ]);

        $this->assertDatabaseHas('pagos', [
            'monto' => 1500.00,
            'estado' => 'pagado',
            'metodo_pago' => 'efectivo'
        ]);

        // Intentar confirmar la reserva (Generar Venta)
        DB::statement('CALL sp_confirmar_reserva(?, ?, @vid)', [$reservaId, $user->id]);
        $ventaId = DB::selectOne('SELECT @vid AS id')->id;

        $this->assertNotNull($ventaId);

        $this->assertDatabaseHas('ventas', [
            'id' => $ventaId,
            'reserva_id' => $reservaId,
            'estado' => 'pagada'
        ]);
        
        // Limpieza manual (si no usamos RefreshDatabase con transacciones debido a los SP)
        DB::table('ventas')->where('id', $ventaId)->delete();
        DB::table('pagos')->where('reserva_id', $reservaId)->delete();
        DB::table('boletos')->where('reserva_id', $reservaId)->delete();
        DB::table('reservas')->where('id', $reservaId)->delete();
    }
}

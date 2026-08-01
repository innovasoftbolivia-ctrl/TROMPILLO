<?php

namespace Database\Seeders;

use App\Models\Aeronave;
use App\Models\Aeropuerto;
use App\Models\Boleto;
use App\Models\Empleado;
use App\Models\EnvioCarga;
use App\Models\Mantenimiento;
use App\Models\Pago;
use App\Models\Pasajero;
use App\Models\Piloto;
use App\Models\Reserva;
use App\Models\Ruta;
use App\Models\User;
use App\Models\Vuelo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AerolineaSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- Usuarios del sistema ----------
        $admin = User::firstOrCreate(
            ['email' => 'admin@aerolinea.test'],
            ['name' => 'Admin Trompillo', 'password' => bcrypt('password'), 'rol' => 'admin']
        );

        // ---------- Aeropuertos (solo Bolivia) ----------
        $aeropuertos = [
            ['codigo_oaci' => 'SLET', 'codigo_iata' => 'SRZ', 'nombre' => 'El Trompillo', 'ciudad' => 'Santa Cruz de la Sierra', 'departamento' => 'Santa Cruz', 'tipo' => 'aeropuerto', 'longitud_pista_m' => 1800, 'superficie_pista' => 'asfalto', 'elevacion_pies' => 1364],
            ['codigo_oaci' => 'SLVR', 'codigo_iata' => 'VVI', 'nombre' => 'Viru Viru Internacional', 'ciudad' => 'Santa Cruz de la Sierra', 'departamento' => 'Santa Cruz', 'tipo' => 'aeropuerto', 'longitud_pista_m' => 3500, 'superficie_pista' => 'asfalto', 'elevacion_pies' => 1224],
            ['codigo_oaci' => 'SLLP', 'codigo_iata' => 'LPB', 'nombre' => 'El Alto Internacional', 'ciudad' => 'La Paz', 'departamento' => 'La Paz', 'tipo' => 'aeropuerto', 'longitud_pista_m' => 4000, 'superficie_pista' => 'asfalto', 'elevacion_pies' => 13323],
            ['codigo_oaci' => 'SLCB', 'codigo_iata' => 'CBB', 'nombre' => 'Jorge Wilstermann', 'ciudad' => 'Cochabamba', 'departamento' => 'Cochabamba', 'tipo' => 'aeropuerto', 'longitud_pista_m' => 3800, 'superficie_pista' => 'asfalto', 'elevacion_pies' => 8360],
            ['codigo_oaci' => 'SLSU', 'codigo_iata' => 'SRE', 'nombre' => 'Juana Azurduy de Padilla', 'ciudad' => 'Sucre', 'departamento' => 'Chuquisaca', 'tipo' => 'aeropuerto', 'longitud_pista_m' => 3000, 'superficie_pista' => 'asfalto', 'elevacion_pies' => 9540],
            ['codigo_oaci' => 'SLTJ', 'codigo_iata' => 'TJA', 'nombre' => 'Capitán Oriel Lea Plaza', 'ciudad' => 'Tarija', 'departamento' => 'Tarija', 'tipo' => 'aeropuerto', 'longitud_pista_m' => 2800, 'superficie_pista' => 'asfalto', 'elevacion_pies' => 6079],
            ['codigo_oaci' => 'SLTR', 'codigo_iata' => 'TDD', 'nombre' => 'Teniente Jorge Henrich', 'ciudad' => 'Trinidad', 'departamento' => 'Beni', 'tipo' => 'aeropuerto', 'longitud_pista_m' => 2000, 'superficie_pista' => 'asfalto', 'elevacion_pies' => 509],
            ['codigo_oaci' => 'SLCO', 'codigo_iata' => 'CIJ', 'nombre' => 'Capitán Aníbal Arab', 'ciudad' => 'Cobija', 'departamento' => 'Pando', 'tipo' => 'aeropuerto', 'longitud_pista_m' => 1800, 'superficie_pista' => 'asfalto', 'elevacion_pies' => 892],
            ['codigo_oaci' => 'SLRA', 'codigo_iata' => 'RIB', 'nombre' => 'Riberalta', 'ciudad' => 'Riberalta', 'departamento' => 'Beni', 'tipo' => 'aerodromo', 'longitud_pista_m' => 1500, 'superficie_pista' => 'asfalto', 'elevacion_pies' => 456],
            ['codigo_oaci' => 'SLPS', 'codigo_iata' => 'PSZ', 'nombre' => 'Capitán Germán Quiroga', 'ciudad' => 'Puerto Suárez', 'departamento' => 'Santa Cruz', 'tipo' => 'aeropuerto', 'longitud_pista_m' => 1600, 'superficie_pista' => 'asfalto', 'elevacion_pies' => 439],
            ['codigo_oaci' => 'SLOR', 'codigo_iata' => 'ORU', 'nombre' => 'Juan Mendoza', 'ciudad' => 'Oruro', 'departamento' => 'Oruro', 'tipo' => 'aeropuerto', 'longitud_pista_m' => 3000, 'superficie_pista' => 'asfalto', 'elevacion_pies' => 12152],
            ['codigo_oaci' => 'SLJV', 'codigo_iata' => null, 'nombre' => 'Pista San Ignacio de Velasco', 'ciudad' => 'San Ignacio de Velasco', 'departamento' => 'Santa Cruz', 'tipo' => 'pista', 'longitud_pista_m' => 1300, 'superficie_pista' => 'tierra', 'elevacion_pies' => 1365],
        ];
        foreach ($aeropuertos as $a) {
            Aeropuerto::create($a);
        }

        // ---------- Flota de avionetas (matrícula boliviana CP-) ----------
        $aeronaves = [
            ['matricula' => 'CP-2521', 'modelo' => 'Cessna 208 Caravan', 'fabricante' => 'Cessna', 'ano_fabricacion' => 2018, 'capacidad_pasajeros' => 9, 'capacidad_carga_kg' => 1300, 'peso_vacio_kg' => 2145, 'peso_maximo_despegue_kg' => 3629, 'autonomia_km' => 1982, 'velocidad_crucero_kmh' => 344, 'horas_vuelo_totales' => 4820.5, 'estado' => 'activa'],
            ['matricula' => 'CP-1987', 'modelo' => 'Cessna 206 Stationair', 'fabricante' => 'Cessna', 'ano_fabricacion' => 2015, 'capacidad_pasajeros' => 5, 'capacidad_carga_kg' => 700, 'peso_vacio_kg' => 1020, 'peso_maximo_despegue_kg' => 1633, 'autonomia_km' => 1352, 'velocidad_crucero_kmh' => 264, 'horas_vuelo_totales' => 6210.0, 'estado' => 'activa'],
            ['matricula' => 'CP-2765', 'modelo' => 'Piper PA-31 Navajo', 'fabricante' => 'Piper', 'ano_fabricacion' => 2012, 'capacidad_pasajeros' => 7, 'capacidad_carga_kg' => 900, 'peso_vacio_kg' => 1810, 'peso_maximo_despegue_kg' => 2950, 'autonomia_km' => 1770, 'velocidad_crucero_kmh' => 400, 'horas_vuelo_totales' => 8955.3, 'estado' => 'mantenimiento'],
            ['matricula' => 'CP-3140', 'modelo' => 'Beechcraft King Air C90', 'fabricante' => 'Beechcraft', 'ano_fabricacion' => 2019, 'capacidad_pasajeros' => 8, 'capacidad_carga_kg' => 1100, 'peso_vacio_kg' => 3130, 'peso_maximo_despegue_kg' => 4581, 'autonomia_km' => 2446, 'velocidad_crucero_kmh' => 496, 'horas_vuelo_totales' => 2310.8, 'estado' => 'activa'],
            ['matricula' => 'CP-1899', 'modelo' => 'Cessna 172 Skyhawk', 'fabricante' => 'Cessna', 'ano_fabricacion' => 2010, 'capacidad_pasajeros' => 3, 'capacidad_carga_kg' => 350, 'peso_vacio_kg' => 767, 'peso_maximo_despegue_kg' => 1157, 'autonomia_km' => 1272, 'velocidad_crucero_kmh' => 226, 'horas_vuelo_totales' => 9800.0, 'estado' => 'activa'],
        ];
        foreach ($aeronaves as $a) {
            Aeronave::create($a);
        }

        // ---------- Empleados ----------
        $empleadosData = [
            ['nombres' => 'Carlos Andrés', 'apellidos' => 'Justiniano Áñez', 'numero_documento' => '7845123', 'cargo' => 'piloto', 'telefono' => '70012345', 'email' => 'cjustiniano@trompillo.bo', 'salario' => 12500],
            ['nombres' => 'Diana Marcela', 'apellidos' => 'Suárez Rojas', 'numero_documento' => '5289456', 'cargo' => 'piloto', 'telefono' => '71123456', 'email' => 'dsuarez@trompillo.bo', 'salario' => 12000],
            ['nombres' => 'Jhonny Fredy', 'apellidos' => 'Callaú Mendoza', 'numero_documento' => '9234567', 'cargo' => 'copiloto', 'telefono' => '72234567', 'email' => 'jcallau@trompillo.bo', 'salario' => 7500],
            ['nombres' => 'Andrés Felipe', 'apellidos' => 'Terrazas Melgar', 'numero_documento' => '8034578', 'cargo' => 'tecnico', 'telefono' => '73345678', 'email' => 'aterrazas@trompillo.bo', 'salario' => 5800],
            ['nombres' => 'Laura Sofía', 'apellidos' => 'Vaca Áñez', 'numero_documento' => '10987654', 'cargo' => 'ventas', 'telefono' => '74456789', 'email' => 'lvaca@trompillo.bo', 'salario' => 4200],
            ['nombres' => 'Ricardo', 'apellidos' => 'Antelo Bernal', 'numero_documento' => '7045612', 'cargo' => 'gerente', 'telefono' => '75567890', 'email' => 'rantelo@trompillo.bo', 'salario' => 18000],
        ];
        $empleados = [];
        foreach ($empleadosData as $e) {
            $empleados[] = Empleado::create(array_merge($e, [
                'fecha_contratacion' => Carbon::parse('2021-03-15'),
            ]));
        }

        // ---------- Pilotos ----------
        $piloto1 = Piloto::create([
            'empleado_id' => $empleados[0]->id, 'licencia_numero' => 'DGAC-BO-14785', 'tipo_licencia' => 'ATPL',
            'horas_vuelo' => 6400, 'vencimiento_licencia' => Carbon::parse('2027-06-30'),
            'vencimiento_medico' => Carbon::parse('2026-11-30'), 'habilitaciones' => 'C208, PA31, BE9L',
        ]);
        $piloto2 = Piloto::create([
            'empleado_id' => $empleados[1]->id, 'licencia_numero' => 'DGAC-BO-15992', 'tipo_licencia' => 'CPL',
            'horas_vuelo' => 3100, 'vencimiento_licencia' => Carbon::parse('2027-02-28'),
            'vencimiento_medico' => Carbon::parse('2026-09-30'), 'habilitaciones' => 'C206, C172',
        ]);
        $copiloto1 = Piloto::create([
            'empleado_id' => $empleados[2]->id, 'licencia_numero' => 'DGAC-BO-16640', 'tipo_licencia' => 'CPL',
            'horas_vuelo' => 950, 'vencimiento_licencia' => Carbon::parse('2028-01-31'),
            'vencimiento_medico' => Carbon::parse('2027-01-31'), 'habilitaciones' => 'C208',
        ]);

        // ---------- Mantenimiento ----------
        Mantenimiento::create([
            'aeronave_id' => 3, 'tecnico_id' => $empleados[3]->id, 'tipo' => 'revision_100h',
            'descripcion' => 'Revisión de 100 horas: motores, tren de aterrizaje y aviónica.',
            'fecha_inicio' => Carbon::parse('2026-07-20'), 'horas_vuelo_aeronave' => 8955.3,
            'costo' => 42000, 'estado' => 'en_proceso',
        ]);
        Mantenimiento::create([
            'aeronave_id' => 1, 'tecnico_id' => $empleados[3]->id, 'tipo' => 'preventivo',
            'descripcion' => 'Cambio de aceite y filtros, inspección visual general.',
            'fecha_inicio' => Carbon::parse('2026-06-10'), 'fecha_fin' => Carbon::parse('2026-06-11'),
            'horas_vuelo_aeronave' => 4700, 'costo' => 9500, 'estado' => 'completado',
        ]);

        // ---------- Rutas (domésticas, hub en El Trompillo / Viru Viru) ----------
        $rutasData = [
            ['origen_id' => 1, 'destino_id' => 7, 'distancia_km' => 380, 'duracion_estimada_min' => 70, 'precio_base' => 620],
            ['origen_id' => 1, 'destino_id' => 8, 'distancia_km' => 640, 'duracion_estimada_min' => 130, 'precio_base' => 980],
            ['origen_id' => 1, 'destino_id' => 5, 'distancia_km' => 360, 'duracion_estimada_min' => 65, 'precio_base' => 560],
            ['origen_id' => 1, 'destino_id' => 4, 'distancia_km' => 380, 'duracion_estimada_min' => 60, 'precio_base' => 520],
            ['origen_id' => 2, 'destino_id' => 3, 'distancia_km' => 550, 'duracion_estimada_min' => 75, 'precio_base' => 720],
            ['origen_id' => 1, 'destino_id' => 10, 'distancia_km' => 560, 'duracion_estimada_min' => 110, 'precio_base' => 850],
        ];
        $rutas = [];
        foreach ($rutasData as $r) {
            $rutas[] = Ruta::create($r);
        }

        // ---------- Vuelos ----------
        $vuelo1 = Vuelo::create([
            'numero_vuelo' => 'TR-101', 'ruta_id' => $rutas[0]->id, 'origen_id' => 1, 'destino_id' => 7,
            'aeronave_id' => 1, 'piloto_id' => $piloto1->id, 'copiloto_id' => $copiloto1->id, 'tipo' => 'regular',
            'salida_programada' => Carbon::parse('2026-07-28 07:30'), 'llegada_programada' => Carbon::parse('2026-07-28 08:40'),
            'asientos_disponibles' => 9, 'precio' => 620, 'estado' => 'programado',
        ]);
        $vuelo2 = Vuelo::create([
            'numero_vuelo' => 'TR-205', 'ruta_id' => $rutas[4]->id, 'origen_id' => 2, 'destino_id' => 3,
            'aeronave_id' => 4, 'piloto_id' => $piloto2->id, 'tipo' => 'regular',
            'salida_programada' => Carbon::parse('2026-07-28 09:00'), 'llegada_programada' => Carbon::parse('2026-07-28 10:15'),
            'asientos_disponibles' => 8, 'precio' => 720, 'estado' => 'confirmado',
        ]);
        $vuelo3 = Vuelo::create([
            'numero_vuelo' => null, 'ruta_id' => null, 'origen_id' => 1, 'destino_id' => 12,
            'aeronave_id' => 2, 'piloto_id' => $piloto2->id, 'tipo' => 'charter',
            'salida_programada' => Carbon::parse('2026-07-29 06:00'), 'llegada_programada' => Carbon::parse('2026-07-29 07:10'),
            'asientos_disponibles' => 5, 'precio' => 4200, 'estado' => 'programado',
            'observaciones' => 'Vuelo charter a pista de tierra en San Ignacio de Velasco. Verificar peso y balance.',
        ]);

        // ---------- Pasajeros (bolivianos) ----------
        $pasajerosData = [
            ['nombres' => 'María Fernanda', 'apellidos' => 'Áñez Suárez', 'numero_documento' => '8123456', 'telefono' => '76012345', 'email' => 'mfanez@correo.bo', 'peso_kg' => 62.5],
            ['nombres' => 'Julián David', 'apellidos' => 'Justiniano Roca', 'numero_documento' => '9045678', 'telefono' => '76123456', 'email' => 'jjustiniano@correo.bo', 'peso_kg' => 78.0],
            ['nombres' => 'Sandra Milena', 'apellidos' => 'Vaca Áñez', 'numero_documento' => '7056789', 'telefono' => '76234567', 'email' => 'svaca@correo.bo', 'peso_kg' => 58.3],
            ['nombres' => 'Óscar Iván', 'apellidos' => 'Melgar Áñez', 'numero_documento' => '6987654', 'telefono' => '76345678', 'email' => 'omelgar@correo.bo', 'peso_kg' => 85.2],
        ];
        $pasajeros = [];
        foreach ($pasajerosData as $p) {
            $pasajeros[] = Pasajero::create($p);
        }

        // ---------- Usuario agente de ventas ----------
        $agente = User::firstOrCreate(
            ['email' => 'ventas@aerolinea.test'],
            ['name' => 'Laura Vaca', 'password' => bcrypt('password'), 'rol' => 'vendedor', 'empleado_id' => $empleados[4]->id]
        );

        // ---------- Reservas + Boletos + Pagos (en Bs) ----------
        $reserva1 = Reserva::create([
            'codigo' => 'TRP001', 'vuelo_id' => $vuelo1->id, 'usuario_id' => $agente->id, 'pasajero_id' => $pasajeros[0]->id,
            'estado' => 'confirmada', 'total' => 1240, 'fecha_reserva' => Carbon::parse('2026-07-25 14:20'),
        ]);
        Boleto::create(['numero_boleto' => 'TRP001-1', 'reserva_id' => $reserva1->id, 'pasajero_id' => $pasajeros[0]->id, 'vuelo_id' => $vuelo1->id, 'asiento' => '2A', 'precio' => 620, 'equipaje_kg' => 12, 'estado' => 'emitido']);
        Boleto::create(['numero_boleto' => 'TRP001-2', 'reserva_id' => $reserva1->id, 'pasajero_id' => $pasajeros[1]->id, 'vuelo_id' => $vuelo1->id, 'asiento' => '2B', 'precio' => 620, 'equipaje_kg' => 15, 'estado' => 'emitido']);
        Pago::create(['reserva_id' => $reserva1->id, 'monto' => 1240, 'metodo' => 'tarjeta_credito', 'estado' => 'pagado', 'referencia' => 'APRV-889231', 'fecha_pago' => Carbon::parse('2026-07-25 14:22')]);

        $reserva2 = Reserva::create([
            'codigo' => 'TRP002', 'vuelo_id' => $vuelo2->id, 'usuario_id' => $agente->id, 'pasajero_id' => $pasajeros[2]->id,
            'estado' => 'pendiente', 'total' => 720, 'fecha_reserva' => Carbon::parse('2026-07-26 09:10'),
        ]);
        Boleto::create(['numero_boleto' => 'TRP002-1', 'reserva_id' => $reserva2->id, 'pasajero_id' => $pasajeros[2]->id, 'vuelo_id' => $vuelo2->id, 'precio' => 720, 'equipaje_kg' => 20, 'estado' => 'emitido']);
        Pago::create(['reserva_id' => $reserva2->id, 'monto' => 360, 'metodo' => 'transferencia', 'estado' => 'pagado', 'referencia' => 'QR-556677', 'fecha_pago' => Carbon::parse('2026-07-26 09:15')]);

        // ---------- Carga ----------
        EnvioCarga::create([
            'guia' => 'CG-000145', 'vuelo_id' => $vuelo1->id, 'remitente' => 'Distribuidora del Oriente S.R.L.',
            'remitente_documento' => '1023456789', 'destinatario' => 'Farmacia San Rafael', 'destinatario_documento' => '9876543210',
            'descripcion' => 'Repuestos y medicamentos (3 cajas)', 'peso_kg' => 85.5, 'valor_declarado' => 18000,
            'costo_envio' => 650, 'estado' => 'registrado',
        ]);
    }
}

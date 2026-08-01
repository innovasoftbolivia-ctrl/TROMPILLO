<?php

namespace Database\Seeders;

use App\Models\Aeronave;
use App\Models\Aeropuerto;
use App\Models\Ciudad;
use App\Models\Departamento;
use App\Models\ModeloAeronave;
use App\Models\Pais;
use App\Models\Pasajero;
use App\Models\Permiso;
use App\Models\Piloto;
use App\Models\Rol;
use App\Models\TripulacionVuelo;
use App\Models\User;
use App\Models\Vuelo;
use Illuminate\Database\Seeder;

class CatalogosSeeder extends Seeder
{
    public function run(): void
    {
        $this->geografia();
        $this->modelosAeronave();
        $this->tripulacion();
        $this->rbac();
    }

    private function geografia(): void
    {
        $paises = [
            ['nombre' => 'Bolivia', 'codigo_iso' => 'BOL', 'gentilicio' => 'Boliviana'],
            ['nombre' => 'Argentina', 'codigo_iso' => 'ARG', 'gentilicio' => 'Argentina'],
            ['nombre' => 'Brasil', 'codigo_iso' => 'BRA', 'gentilicio' => 'Brasileña'],
            ['nombre' => 'Chile', 'codigo_iso' => 'CHL', 'gentilicio' => 'Chilena'],
            ['nombre' => 'Perú', 'codigo_iso' => 'PER', 'gentilicio' => 'Peruana'],
            ['nombre' => 'Paraguay', 'codigo_iso' => 'PRY', 'gentilicio' => 'Paraguaya'],
        ];
        foreach ($paises as $p) {
            Pais::firstOrCreate(['nombre' => $p['nombre']], $p);
        }
        $bolivia = Pais::where('nombre', 'Bolivia')->first();

        // Departamentos de Bolivia (catálogo completo)
        foreach (['Santa Cruz', 'La Paz', 'Cochabamba', 'Chuquisaca', 'Tarija', 'Beni', 'Pando', 'Oruro', 'Potosí'] as $dep) {
            Departamento::firstOrCreate(['nombre' => $dep]);
        }

        // Ciudades + backfill de aeropuertos.ciudad_id (derivado de los datos existentes)
        foreach (Aeropuerto::all() as $aeropuerto) {
            $dep = Departamento::firstOrCreate(
                ['nombre' => $aeropuerto->departamento ?: 'Sin departamento']
            );
            $ciudad = Ciudad::firstOrCreate(['departamento_id' => $dep->id, 'nombre' => $aeropuerto->ciudad]);

            if (! $aeropuerto->ciudad_id) {
                $aeropuerto->ciudad_id = $ciudad->id;
                $aeropuerto->save();
            }
        }

        // Nacionalidad de pasajeros -> país (por gentilicio; por defecto Bolivia)
        foreach (Pasajero::whereNull('pais_id')->get() as $pasajero) {
            $pais = Pais::where('gentilicio', $pasajero->nacionalidad)->first() ?? $bolivia;
            $pasajero->pais_id = $pais->id;
            $pasajero->save();
        }
    }

    private function modelosAeronave(): void
    {
        foreach (Aeronave::all() as $a) {
            $modelo = ModeloAeronave::firstOrCreate(
                ['fabricante' => $a->fabricante, 'modelo' => $a->modelo],
                [
                    'capacidad_pasajeros'     => $a->capacidad_pasajeros,
                    'capacidad_carga_kg'      => $a->capacidad_carga_kg,
                    'peso_vacio_kg'           => $a->peso_vacio_kg,
                    'peso_maximo_despegue_kg' => $a->peso_maximo_despegue_kg,
                    'autonomia_km'            => $a->autonomia_km,
                    'velocidad_crucero_kmh'   => $a->velocidad_crucero_kmh,
                ]
            );
            if (! $a->modelo_aeronave_id) {
                $a->modelo_aeronave_id = $modelo->id;
                $a->save();
            }
        }
    }

    private function tripulacion(): void
    {
        foreach (Vuelo::all() as $v) {
            if ($v->piloto_id) {
                $empId = Piloto::find($v->piloto_id)?->empleado_id;
                if ($empId) {
                    TripulacionVuelo::firstOrCreate(
                        ['vuelo_id' => $v->id, 'empleado_id' => $empId],
                        ['rol' => 'comandante']
                    );
                }
            }
            if ($v->copiloto_id) {
                $empId = Piloto::find($v->copiloto_id)?->empleado_id;
                if ($empId) {
                    TripulacionVuelo::firstOrCreate(
                        ['vuelo_id' => $v->id, 'empleado_id' => $empId],
                        ['rol' => 'primer_oficial']
                    );
                }
            }
        }
    }

    private function rbac(): void
    {
        $roles = [
            'administrador' => 'Acceso total al sistema.',
            'gerente'       => 'Supervisión, reportes y operación.',
            'operador'      => 'Operaciones de vuelo y despacho.',
            'vendedor'      => 'Gestión de reservas y boletos.',
            'piloto'        => 'Consulta de vuelos asignados.',
        ];
        foreach ($roles as $nombre => $desc) {
            Rol::firstOrCreate(['nombre' => $nombre], ['descripcion' => $desc]);
        }

        $permisos = [
            'vuelos.gestionar'         => 'Gestionar vuelos',
            'operaciones.despachar'    => 'Despachar / cerrar / aterrizar vuelos',
            'reservas.gestionar'       => 'Gestionar reservas y boletos',
            'flota.gestionar'          => 'Gestionar flota y aeronaves',
            'mantenimiento.gestionar'  => 'Gestionar mantenimientos',
            'personal.gestionar'       => 'Gestionar empleados y pilotos',
            'catalogos.gestionar'      => 'Gestionar catálogos (rutas, aeropuertos, pasajeros)',
            'reportes.ver'             => 'Ver reportes y operaciones',
            'usuarios.gestionar'       => 'Gestionar usuarios y roles',
        ];
        foreach ($permisos as $clave => $nombre) {
            Permiso::firstOrCreate(['clave' => $clave], ['nombre' => $nombre]);
        }

        // Asignación de permisos por rol
        $asignaciones = [
            'administrador' => array_keys($permisos),
            'gerente'       => ['vuelos.gestionar', 'operaciones.despachar', 'reservas.gestionar', 'flota.gestionar', 'mantenimiento.gestionar', 'personal.gestionar', 'catalogos.gestionar', 'reportes.ver'],
            'operador'      => ['vuelos.gestionar', 'operaciones.despachar', 'reportes.ver'],
            'vendedor'      => ['reservas.gestionar', 'reportes.ver'],
            'piloto'        => ['reportes.ver'],
        ];
        foreach ($asignaciones as $rolNombre => $claves) {
            $rol = Rol::where('nombre', $rolNombre)->first();
            $ids = Permiso::whereIn('clave', $claves)->pluck('id');
            $rol->permisos()->syncWithoutDetaching($ids);
        }

        // Backfill users.role_id desde el enum `rol`
        $mapa = ['admin' => 'administrador', 'operador' => 'operador', 'vendedor' => 'vendedor', 'piloto' => 'piloto'];
        foreach (User::whereNull('role_id')->get() as $user) {
            $nombreRol = $mapa[$user->rol] ?? 'vendedor';
            $rol = Rol::where('nombre', $nombreRol)->first();
            if ($rol) {
                $user->role_id = $rol->id;
                $user->save();
            }
        }
    }
}

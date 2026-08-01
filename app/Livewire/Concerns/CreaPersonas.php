<?php

namespace App\Livewire\Concerns;

use App\Models\Pasajero;
use App\Models\Persona;
use App\Models\PersonaJuridica;
use App\Models\PersonaNatural;
use Illuminate\Support\Facades\DB;

/**
 * Permite crear una persona (natural/jurídica) "al vuelo" desde otros formularios
 * (Venta, Reserva) sin salir de ellos. Si es natural, sincroniza su ficha de pasajero.
 */
trait CreaPersonas
{
    /**
     * @return array{0:int,1:?int}  [personaId, pasajeroId|null]
     */
    protected function crearPersonaRapida(array $d): array
    {
        return DB::transaction(function () use ($d) {
            $persona = Persona::create([
                'tipo_persona'     => $d['tipo_persona'],
                'tipo_documento'   => $d['tipo_documento'],
                'numero_documento' => $d['numero_documento'],
                'telefono'         => $d['telefono'] ?? null,
                'email'            => $d['email'] ?? null,
            ]);

            $pasajeroId = null;

            if ($d['tipo_persona'] === 'natural') {
                PersonaNatural::create([
                    'persona_id' => $persona->id,
                    'nombres'    => $d['nombres'],
                    'apellidos'  => $d['apellidos'],
                ]);

                $pas = Pasajero::where('tipo_documento', $d['tipo_documento'])
                    ->where('numero_documento', $d['numero_documento'])->first() ?? new Pasajero();
                $pas->persona_id        = $persona->id;
                $pas->nombres           = $d['nombres'];
                $pas->apellidos         = $d['apellidos'];
                $pas->tipo_documento    = $d['tipo_documento'];
                $pas->numero_documento  = $d['numero_documento'];
                $pas->nacionalidad      = $d['nacionalidad'] ?? 'Boliviana';
                $pas->telefono          = $d['telefono'] ?? null;
                $pas->email             = $d['email'] ?? null;
                $pas->peso_kg           = ($d['peso_kg'] ?? null) ?: null;
                $pas->save();
                $pasajeroId = $pas->id;
            } else {
                PersonaJuridica::create([
                    'persona_id'   => $persona->id,
                    'razon_social' => $d['razon_social'],
                    'nit'          => $d['nit'] ?? null,
                ]);
            }

            return [$persona->id, $pasajeroId];
        });
    }
}

<?php

namespace App\Livewire;

use App\Models\Pasajero;
use App\Models\Persona;
use App\Models\PersonaNatural;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PasajeroForm extends Component
{
    public ?Pasajero $pasajero = null;

    public string $nombres = '';
    public string $apellidos = '';
    public string $tipo_documento = 'CI';
    public string $numero_documento = '';
    public ?string $fecha_nacimiento = null;
    public string $nacionalidad = 'Boliviana';
    public ?string $telefono = null;
    public ?string $email = null;
    public ?string $peso_kg = null;
    public ?string $contacto_emergencia = null;
    public ?string $telefono_emergencia = null;

    public function mount($pasajero = null): void
    {
        if ($pasajero && ! $pasajero instanceof Pasajero) {
            $pasajero = Pasajero::findOrFail($pasajero);
        }
        if ($pasajero && $pasajero->exists) {
            $this->pasajero = $pasajero;
            $this->nombres = $pasajero->nombres ?? '';
            $this->apellidos = $pasajero->apellidos ?? '';
            $this->tipo_documento = $pasajero->tipo_documento ?? 'CI';
            $this->numero_documento = $pasajero->numero_documento ?? '';
            $this->fecha_nacimiento = $pasajero->fecha_nacimiento;
            $this->nacionalidad = $pasajero->nacionalidad ?? 'Boliviana';
            $this->telefono = $pasajero->telefono;
            $this->email = $pasajero->email;
            $this->peso_kg = $pasajero->peso_kg;
            $this->contacto_emergencia = $pasajero->contacto_emergencia;
            $this->telefono_emergencia = $pasajero->telefono_emergencia;
        }
    }

    public function rules(): array
    {
        return [
            'nombres'             => ['required', 'string', 'max:100'],
            'apellidos'           => ['required', 'string', 'max:100'],
            'tipo_documento'      => ['required', 'string', 'max:20'],
            'numero_documento'    => ['required', 'string', 'max:30',
                Rule::unique('pasajeros')->where(fn ($q) => $q->where('tipo_documento', $this->tipo_documento))->ignore($this->pasajero?->id)],
            'fecha_nacimiento'    => ['nullable', 'date'],
            'nacionalidad'        => ['required', 'string', 'max:50'],
            'telefono'            => ['nullable', 'string', 'max:30'],
            'email'               => ['nullable', 'email', 'max:100'],
            'peso_kg'             => ['nullable', 'numeric', 'min:0', 'max:500'],
            'contacto_emergencia' => ['nullable', 'string', 'max:120'],
            'telefono_emergencia' => ['nullable', 'string', 'max:30'],
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'numero_documento' => 'número de documento', 'tipo_documento' => 'tipo de documento',
            'fecha_nacimiento' => 'fecha de nacimiento', 'peso_kg' => 'peso',
            'contacto_emergencia' => 'contacto de emergencia', 'telefono_emergencia' => 'teléfono de emergencia',
        ];
    }

    public function guardar()
    {
        $this->validate();
        try {
            if ($this->pasajero) {
                DB::statement('CALL sp_pasajeros_update(?,?,?,?,?,?,?,?,?,?,?,?)', [
                    $this->pasajero->id, $this->nombres, $this->apellidos, $this->tipo_documento,
                    $this->numero_documento, $this->fecha_nacimiento ?: null, $this->nacionalidad,
                    $this->telefono ?: null, $this->email ?: null, $this->peso_kg ?: null,
                    $this->contacto_emergencia ?: null, $this->telefono_emergencia ?: null,
                ]);
                $personaId = $this->sincronizarPersona();
                DB::table('pasajeros')->where('id', $this->pasajero->id)->update(['persona_id' => $personaId]);
            } else {
                DB::statement('CALL sp_pasajeros_insert(?,?,?,?,?,?,?,?,?,?,?)', [
                    $this->nombres, $this->apellidos, $this->tipo_documento, $this->numero_documento,
                    $this->fecha_nacimiento ?: null, $this->nacionalidad, $this->telefono ?: null,
                    $this->email ?: null, $this->peso_kg ?: null, $this->contacto_emergencia ?: null,
                    $this->telefono_emergencia ?: null,
                ]);
                $id = DB::selectOne('SELECT LAST_INSERT_ID() AS id')->id;
                $personaId = $this->sincronizarPersona();
                DB::table('pasajeros')->where('id', $id)->update(['persona_id' => $personaId]);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Error: ' . ($e->errorInfo[2] ?? 'no se pudo guardar'));
            return;
        }
        return redirect()->route('pasajeros.index')
            ->with('success', $this->pasajero ? 'Pasajero actualizado correctamente.' : 'Pasajero creado correctamente.');
    }

    private function sincronizarPersona(): int
    {
        $persona = Persona::firstOrCreate(
            ['tipo_documento' => $this->tipo_documento, 'numero_documento' => $this->numero_documento],
            ['tipo_persona' => 'natural', 'telefono' => $this->telefono, 'email' => $this->email]
        );
        PersonaNatural::updateOrCreate(
            ['persona_id' => $persona->id],
            ['nombres' => $this->nombres, 'apellidos' => $this->apellidos, 'fecha_nacimiento' => $this->fecha_nacimiento]
        );
        return $persona->id;
    }

    public function render()
    {
        return view('livewire.pasajero-form');
    }
}

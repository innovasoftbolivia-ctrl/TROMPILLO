<?php

namespace App\Livewire;

use App\Models\Pais;
use App\Models\Pasajero;
use App\Models\Persona;
use App\Models\PersonaJuridica;
use App\Models\PersonaNatural;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PersonaForm extends Component
{
    public ?Persona $persona = null;

    public string $tipo_persona = 'natural';
    public string $tipo_documento = 'CI';
    public string $numero_documento = '';
    public ?string $telefono = null;
    public ?string $email = null;
    public ?string $direccion = null;
    public string $pais_id = '';
    // Natural
    public string $nombres = '';
    public string $apellidos = '';
    public ?string $fecha_nacimiento = null;
    public string $sexo = '';
    // Datos de pasajero (una persona natural puede viajar)
    public string $nacionalidad = 'Boliviana';
    public ?string $peso_kg = null;
    public ?string $contacto_emergencia = null;
    public ?string $telefono_emergencia = null;
    // Jurídica
    public string $razon_social = '';
    public string $nit = '';
    public string $representante_legal = '';

    public function mount($persona = null): void
    {
        if ($persona && ! $persona instanceof Persona) {
            $persona = Persona::findOrFail($persona);
        }
        if ($persona && $persona->exists) {
            $persona->load(['natural', 'juridica']);
            $this->persona = $persona;
            $this->tipo_persona = $persona->tipo_persona;
            $this->tipo_documento = $persona->tipo_documento ?? 'CI';
            $this->numero_documento = $persona->numero_documento ?? '';
            $this->telefono = $persona->telefono;
            $this->email = $persona->email;
            $this->direccion = $persona->direccion;
            $this->pais_id = (string) ($persona->pais_id ?? '');
            if ($persona->natural) {
                $this->nombres = $persona->natural->nombres ?? '';
                $this->apellidos = $persona->natural->apellidos ?? '';
                $this->fecha_nacimiento = $persona->natural->fecha_nacimiento;
                $this->sexo = $persona->natural->sexo ?? '';
                // Datos de pasajero (si esta persona ya viaja)
                $pasajero = Pasajero::where('persona_id', $persona->id)->first();
                if ($pasajero) {
                    $this->nacionalidad = $pasajero->nacionalidad ?? 'Boliviana';
                    $this->peso_kg = $pasajero->peso_kg !== null ? (string) $pasajero->peso_kg : null;
                    $this->contacto_emergencia = $pasajero->contacto_emergencia;
                    $this->telefono_emergencia = $pasajero->telefono_emergencia;
                }
            }
            if ($persona->juridica) {
                $this->razon_social = $persona->juridica->razon_social ?? '';
                $this->nit = $persona->juridica->nit ?? '';
                $this->representante_legal = $persona->juridica->representante_legal ?? '';
            }
        }
    }

    public function rules(): array
    {
        $r = [
            'tipo_persona'     => ['required', 'in:natural,juridica'],
            'tipo_documento'   => ['required', 'string', 'max:20'],
            'numero_documento' => ['required', 'string', 'max:30',
                Rule::unique('personas')->ignore($this->persona?->id)],
            'telefono'         => ['nullable', 'string', 'max:30'],
            'email'            => ['nullable', 'email', 'max:120'],
            'direccion'        => ['nullable', 'string', 'max:255'],
            'pais_id'          => ['nullable', 'exists:paises,id'],
        ];
        if ($this->tipo_persona === 'natural') {
            $r['nombres'] = ['required', 'string', 'max:100'];
            $r['apellidos'] = ['required', 'string', 'max:100'];
            $r['fecha_nacimiento'] = ['nullable', 'date'];
            $r['sexo'] = ['nullable', 'in:M,F'];
            $r['nacionalidad'] = ['nullable', 'string', 'max:50'];
            $r['peso_kg'] = ['nullable', 'numeric', 'min:0', 'max:500'];
            $r['contacto_emergencia'] = ['nullable', 'string', 'max:120'];
            $r['telefono_emergencia'] = ['nullable', 'string', 'max:30'];
        } else {
            $r['razon_social'] = ['required', 'string', 'max:200'];
            $r['nit'] = ['nullable', 'string', 'max:30'];
            $r['representante_legal'] = ['nullable', 'string', 'max:150'];
        }
        return $r;
    }

    public function guardar()
    {
        $this->validate();
        try {
            DB::transaction(function () {
                if ($this->persona) {
                    $this->persona->update([
                        'tipo_persona' => $this->tipo_persona, 'tipo_documento' => $this->tipo_documento,
                        'numero_documento' => $this->numero_documento, 'telefono' => $this->telefono ?: null,
                        'email' => $this->email ?: null, 'direccion' => $this->direccion ?: null,
                        'pais_id' => $this->pais_id ?: null,
                    ]);
                    $p = $this->persona;
                } else {
                    $p = Persona::create([
                        'tipo_persona' => $this->tipo_persona, 'tipo_documento' => $this->tipo_documento,
                        'numero_documento' => $this->numero_documento, 'telefono' => $this->telefono ?: null,
                        'email' => $this->email ?: null, 'direccion' => $this->direccion ?: null,
                        'pais_id' => $this->pais_id ?: null,
                    ]);
                }
                if ($this->tipo_persona === 'natural') {
                    PersonaNatural::updateOrCreate(['persona_id' => $p->id], [
                        'nombres' => $this->nombres, 'apellidos' => $this->apellidos,
                        'fecha_nacimiento' => $this->fecha_nacimiento ?: null, 'sexo' => $this->sexo ?: null,
                    ]);

                    // Sincroniza la ficha de pasajero (una persona natural puede viajar).
                    $pasajero = Pasajero::where('persona_id', $p->id)->first()
                        ?? Pasajero::where('tipo_documento', $this->tipo_documento)
                            ->where('numero_documento', $this->numero_documento)->first()
                        ?? new Pasajero();
                    $pasajero->persona_id = $p->id;
                    $pasajero->nombres = $this->nombres;
                    $pasajero->apellidos = $this->apellidos;
                    $pasajero->tipo_documento = $this->tipo_documento;
                    $pasajero->numero_documento = $this->numero_documento;
                    $pasajero->fecha_nacimiento = $this->fecha_nacimiento ?: null;
                    $pasajero->nacionalidad = $this->nacionalidad ?: 'Boliviana';
                    $pasajero->telefono = $this->telefono ?: null;
                    $pasajero->email = $this->email ?: null;
                    $pasajero->peso_kg = $this->peso_kg !== '' ? ($this->peso_kg ?: null) : null;
                    $pasajero->contacto_emergencia = $this->contacto_emergencia ?: null;
                    $pasajero->telefono_emergencia = $this->telefono_emergencia ?: null;
                    $pasajero->save();
                } else {
                    PersonaJuridica::updateOrCreate(['persona_id' => $p->id], [
                        'razon_social' => $this->razon_social, 'nit' => $this->nit ?: null,
                        'representante_legal' => $this->representante_legal ?: null,
                    ]);
                }
            });
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Error: ' . ($e->errorInfo[2] ?? 'no se pudo guardar'));
            return;
        }
        return redirect()->route('personas.index')
            ->with('success', $this->persona ? 'Persona actualizada correctamente.' : 'Persona creada correctamente.');
    }

    public function render()
    {
        return view('livewire.persona-form', ['paises' => Pais::orderBy('nombre')->get()]);
    }
}

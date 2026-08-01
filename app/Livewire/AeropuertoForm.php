<?php

namespace App\Livewire;

use App\Models\Aeropuerto;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AeropuertoForm extends Component
{
    public ?Aeropuerto $aeropuerto = null;

    public string $codigo_oaci = '';
    public string $codigo_iata = '';
    public string $nombre = '';
    public string $ciudad = '';
    public string $departamento = '';
    public string $tipo = 'aerodromo';
    public ?string $latitud = null;
    public ?string $longitud_coord = null;
    public ?string $elevacion_pies = null;
    public ?string $longitud_pista_m = null;
    public string $superficie_pista = '';
    public bool $activo = true;

    public function mount($aeropuerto = null): void
    {
        if ($aeropuerto && ! $aeropuerto instanceof Aeropuerto) {
            $aeropuerto = Aeropuerto::findOrFail($aeropuerto);
        }
        if ($aeropuerto && $aeropuerto->exists) {
            $this->aeropuerto = $aeropuerto;
            $this->codigo_oaci = $aeropuerto->codigo_oaci ?? '';
            $this->codigo_iata = $aeropuerto->codigo_iata ?? '';
            $this->nombre = $aeropuerto->nombre ?? '';
            $this->ciudad = $aeropuerto->ciudad ?? '';
            $this->departamento = $aeropuerto->departamento ?? '';
            $this->tipo = $aeropuerto->tipo ?? 'aerodromo';
            $this->latitud = $aeropuerto->latitud;
            $this->longitud_coord = $aeropuerto->longitud;
            $this->elevacion_pies = $aeropuerto->elevacion_pies;
            $this->longitud_pista_m = $aeropuerto->longitud_pista_m;
            $this->superficie_pista = $aeropuerto->superficie_pista ?? '';
            $this->activo = (bool) $aeropuerto->activo;
        }
    }

    public function rules(): array
    {
        return [
            'codigo_oaci'      => ['required', 'string', 'max:4', Rule::unique('aeropuertos')->ignore($this->aeropuerto?->id)],
            'codigo_iata'      => ['nullable', 'string', 'max:3'],
            'nombre'           => ['required', 'string', 'max:255'],
            'ciudad'           => ['required', 'string', 'max:255'],
            'departamento'     => ['nullable', 'string', 'max:255'],
            'tipo'             => ['required', 'in:aeropuerto,aerodromo,pista'],
            'latitud'          => ['nullable', 'numeric'],
            'longitud_coord'   => ['nullable', 'numeric'],
            'elevacion_pies'   => ['nullable', 'integer'],
            'longitud_pista_m' => ['nullable', 'integer'],
            'superficie_pista' => ['nullable', 'in:asfalto,concreto,tierra,pasto'],
            'activo'           => ['boolean'],
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'codigo_oaci'      => 'código OACI',
            'codigo_iata'      => 'código IATA',
            'longitud_coord'   => 'longitud',
            'elevacion_pies'   => 'elevación',
            'longitud_pista_m' => 'longitud de pista',
            'superficie_pista' => 'superficie de pista',
        ];
    }

    public function guardar()
    {
        $this->validate();

        try {
            if ($this->aeropuerto) {
                DB::statement('CALL sp_aeropuertos_update(?,?,?,?,?,?,?,?,?,?,?,?,?,?)', [
                    $this->aeropuerto->id,
                    $this->codigo_oaci,
                    $this->codigo_iata ?: null,
                    $this->nombre,
                    $this->ciudad,
                    $this->departamento ?: null,
                    'Bolivia',
                    $this->tipo,
                    $this->latitud ?: null,
                    $this->longitud_coord ?: null,
                    $this->elevacion_pies ?: null,
                    $this->longitud_pista_m ?: null,
                    $this->superficie_pista ?: null,
                    (int) $this->activo,
                ]);
            } else {
                DB::statement('CALL sp_aeropuertos_insert(?,?,?,?,?,?,?,?,?,?,?,?,?)', [
                    $this->codigo_oaci,
                    $this->codigo_iata ?: null,
                    $this->nombre,
                    $this->ciudad,
                    $this->departamento ?: null,
                    'Bolivia',
                    $this->tipo,
                    $this->latitud ?: null,
                    $this->longitud_coord ?: null,
                    $this->elevacion_pies ?: null,
                    $this->longitud_pista_m ?: null,
                    $this->superficie_pista ?: null,
                    (int) $this->activo,
                ]);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Error: ' . ($e->errorInfo[2] ?? 'no se pudo guardar'));
            return;
        }

        return redirect()->route('aeropuertos.index')
            ->with('success', $this->aeropuerto ? 'Aeropuerto actualizado correctamente.' : 'Aeropuerto creado correctamente.');
    }

    public function render()
    {
        return view('livewire.aeropuerto-form');
    }
}

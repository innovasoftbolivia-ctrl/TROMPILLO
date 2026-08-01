<?php

namespace App\Livewire;

use App\Models\Aeropuerto;
use App\Models\Ruta;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class RutaForm extends Component
{
    public ?Ruta $ruta = null;

    public string $origen_id = '';
    public string $destino_id = '';
    public ?string $distancia_km = null;
    public ?string $duracion_estimada_min = null;
    public string $precio_base = '0';
    public bool $activa = true;

    public function mount($ruta = null): void
    {
        if ($ruta && ! $ruta instanceof Ruta) {
            $ruta = Ruta::findOrFail($ruta);
        }
        if ($ruta && $ruta->exists) {
            $this->ruta = $ruta;
            $this->origen_id = (string) $ruta->origen_id;
            $this->destino_id = (string) $ruta->destino_id;
            $this->distancia_km = $ruta->distancia_km;
            $this->duracion_estimada_min = $ruta->duracion_estimada_min;
            $this->precio_base = (string) $ruta->precio_base;
            $this->activa = (bool) $ruta->activa;
        }
    }

    public function rules(): array
    {
        return [
            'origen_id'             => ['required', 'exists:aeropuertos,id'],
            'destino_id'            => ['required', 'exists:aeropuertos,id', 'different:origen_id'],
            'distancia_km'          => ['nullable', 'integer', 'min:0'],
            'duracion_estimada_min' => ['nullable', 'integer', 'min:0'],
            'precio_base'           => ['required', 'numeric', 'min:0'],
            'activa'                => ['boolean'],
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'origen_id'             => 'aeropuerto de origen',
            'destino_id'            => 'aeropuerto de destino',
            'distancia_km'          => 'distancia',
            'duracion_estimada_min' => 'duración estimada',
            'precio_base'           => 'precio base',
        ];
    }

    public function messages(): array
    {
        return ['destino_id.different' => 'El destino debe ser diferente del origen.'];
    }

    public function guardar()
    {
        $this->validate();

        try {
            if ($this->ruta) {
                DB::statement('CALL sp_rutas_update(?,?,?,?,?,?,?)', [
                    $this->ruta->id, $this->origen_id, $this->destino_id,
                    $this->distancia_km ?: null, $this->duracion_estimada_min ?: null,
                    $this->precio_base, (int) $this->activa,
                ]);
            } else {
                DB::statement('CALL sp_rutas_insert(?,?,?,?,?,?)', [
                    $this->origen_id, $this->destino_id,
                    $this->distancia_km ?: null, $this->duracion_estimada_min ?: null,
                    $this->precio_base, (int) $this->activa,
                ]);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Error: ' . ($e->errorInfo[2] ?? 'no se pudo guardar'));
            return;
        }

        return redirect()->route('rutas.index')
            ->with('success', $this->ruta ? 'Ruta actualizada correctamente.' : 'Ruta creada correctamente.');
    }

    public function render()
    {
        return view('livewire.ruta-form', [
            'aeropuertos' => Aeropuerto::orderBy('ciudad')->get(),
        ]);
    }
}

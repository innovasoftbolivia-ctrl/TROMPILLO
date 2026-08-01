<?php

namespace App\Livewire;

use App\Models\Aeronave;
use App\Models\Aeropuerto;
use App\Models\Piloto;
use App\Models\Ruta;
use App\Models\Vuelo;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class VueloForm extends Component
{
    public ?Vuelo $vuelo = null;

    public string $numero_vuelo = '';
    public string $ruta_id = '';
    public string $origen_id = '';
    public string $destino_id = '';
    public string $aeronave_id = '';
    public string $piloto_id = '';
    public string $copiloto_id = '';
    public string $tipo = 'regular';
    public ?string $salida_programada = null;
    public ?string $llegada_programada = null;
    public ?string $salida_real = null;
    public ?string $llegada_real = null;
    public string $asientos_disponibles = '14';
    public string $precio = '0';
    public string $estado = 'programado';
    public string $observaciones = '';

    public function mount($vuelo = null): void
    {
        if ($vuelo) {
            $vuelo = $vuelo instanceof Vuelo ? $vuelo : Vuelo::findOrFail($vuelo);
            $this->vuelo = $vuelo;
            $this->numero_vuelo = $vuelo->numero_vuelo ?? '';
            $this->ruta_id = (string) ($vuelo->ruta_id ?? '');
            $this->origen_id = (string) $vuelo->origen_id;
            $this->destino_id = (string) $vuelo->destino_id;
            $this->aeronave_id = (string) ($vuelo->aeronave_id ?? '');
            $this->piloto_id = (string) ($vuelo->piloto_id ?? '');
            $this->copiloto_id = (string) ($vuelo->copiloto_id ?? '');
            $this->tipo = $vuelo->tipo ?? 'regular';
            $this->salida_programada = $vuelo->salida_programada ? \Carbon\Carbon::parse($vuelo->salida_programada)->format('Y-m-d\TH:i') : null;
            $this->llegada_programada = $vuelo->llegada_programada ? \Carbon\Carbon::parse($vuelo->llegada_programada)->format('Y-m-d\TH:i') : null;
            $this->salida_real = $vuelo->salida_real ? \Carbon\Carbon::parse($vuelo->salida_real)->format('Y-m-d\TH:i') : null;
            $this->llegada_real = $vuelo->llegada_real ? \Carbon\Carbon::parse($vuelo->llegada_real)->format('Y-m-d\TH:i') : null;
            $this->asientos_disponibles = (string) ($vuelo->asientos_disponibles ?? 14);
            $this->precio = (string) ($vuelo->precio ?? 0);
            $this->estado = $vuelo->estado ?? 'programado';
            $this->observaciones = $vuelo->observaciones ?? '';
        } else {
            // Nuevo vuelo: numeramos automáticamente siguiendo el orden existente.
            $this->numero_vuelo = $this->proximoNumeroVuelo();
        }
    }

    /**
     * Al elegir una ruta, autocompleta el precio del boleto con el precio base
     * establecido en la ruta, y también su origen y destino.
     */
    public function updatedRutaId($value): void
    {
        if (! $value) {
            return;
        }

        $ruta = Ruta::find($value);

        if ($ruta) {
            $this->precio = (string) $ruta->precio_base;
            $this->origen_id = (string) $ruta->origen_id;
            $this->destino_id = (string) $ruta->destino_id;
        }
    }

    /**
     * Al elegir una aeronave, autocompleta los asientos disponibles con la
     * capacidad de pasajeros de esa aeronave.
     */
    public function updatedAeronaveId($value): void
    {
        if (! $value) {
            return;
        }

        $capacidad = Aeronave::whereKey($value)->value('capacidad_pasajeros');

        if ($capacidad !== null) {
            $this->asientos_disponibles = (string) $capacidad;
        }
    }

    /**
     * Genera el siguiente número de vuelo (formato TRP-###) tomando el
     * mayor correlativo existente y sumándole 1. Así el usuario no lo escribe.
     */
    private function proximoNumeroVuelo(string $prefijo = 'TRP-'): string
    {
        $max = Vuelo::where('numero_vuelo', 'like', $prefijo . '%')
            ->get('numero_vuelo')
            ->map(fn ($v) => (int) preg_replace('/\D/', '', str_replace($prefijo, '', $v->numero_vuelo)))
            ->max();

        $siguiente = ($max ?? 100) + 1;

        // Evita colisiones si ese número ya estuviera tomado.
        while (Vuelo::where('numero_vuelo', $prefijo . str_pad((string) $siguiente, 3, '0', STR_PAD_LEFT))->exists()) {
            $siguiente++;
        }

        return $prefijo . str_pad((string) $siguiente, 3, '0', STR_PAD_LEFT);
    }

    public function rules(): array
    {
        return [
            'numero_vuelo' => ['nullable', 'string', 'max:15'],
            'ruta_id' => [$this->vuelo ? 'nullable' : 'required', 'exists:rutas,id'],
            'origen_id' => ['required', 'exists:aeropuertos,id'],
            'destino_id' => ['required', 'exists:aeropuertos,id', 'different:origen_id'],
            'aeronave_id' => ['required', 'exists:aeronaves,id'],
            'piloto_id' => ['required', 'exists:pilotos,id'],
            'copiloto_id' => ['nullable', 'exists:pilotos,id', 'different:piloto_id'],
            'tipo' => ['required', 'in:regular,charter,carga,ambulancia'],
            'salida_programada' => ['required', 'date'],
            'llegada_programada' => ['nullable', 'date', 'after_or_equal:salida_programada'],
            'salida_real' => ['nullable', 'date'],
            'llegada_real' => ['nullable', 'date', 'after_or_equal:salida_real'],
            'asientos_disponibles' => ['required', 'integer', 'min:0', 'max:60'],
            'precio' => ['required', 'numeric', 'min:0'],
            'estado' => ['required', 'in:programado,confirmado,abordando,en_vuelo,aterrizado,cancelado,retrasado'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'ruta_id.required' => 'Seleccioná una ruta (define origen, destino y precio).',
            'origen_id.required' => 'Elegí una ruta para definir el origen.',
            'destino_id.required' => 'Elegí una ruta para definir el destino.',
            'aeronave_id.required' => 'Seleccioná la aeronave.',
            'piloto_id.required' => 'Seleccioná el piloto.',
        ];
    }

    public function guardar()
    {
        $this->validate();

        // El número es automático: si es un vuelo nuevo y no hay número (o quedó
        // vacío), generamos el siguiente correlativo justo antes de guardar.
        if (! $this->vuelo && trim($this->numero_vuelo) === '') {
            $this->numero_vuelo = $this->proximoNumeroVuelo();
        }

        $dt = fn ($c) => ! empty($c) ? \Carbon\Carbon::parse($c)->format('Y-m-d H:i:s') : null;
        try {
            if ($this->vuelo) {
                DB::statement('CALL sp_vuelos_update(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', [
                    $this->vuelo->id, $this->numero_vuelo, $this->ruta_id ?: null, $this->origen_id, $this->destino_id,
                    $this->aeronave_id ?: null, $this->piloto_id ?: null, $this->copiloto_id ?: null, $this->tipo,
                    $dt($this->salida_programada), $dt($this->llegada_programada), $dt($this->salida_real), $dt($this->llegada_real),
                    $this->asientos_disponibles, $this->precio, $this->estado, $this->observaciones ?: null,
                ]);
            } else {
                DB::statement('CALL sp_vuelos_insert(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', [
                    $this->numero_vuelo, $this->ruta_id ?: null, $this->origen_id, $this->destino_id,
                    $this->aeronave_id ?: null, $this->piloto_id ?: null, $this->copiloto_id ?: null, $this->tipo,
                    $dt($this->salida_programada), $dt($this->llegada_programada), $dt($this->salida_real), $dt($this->llegada_real),
                    $this->asientos_disponibles, $this->precio, $this->estado, $this->observaciones ?: null,
                ]);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Error: ' . ($e->errorInfo[2] ?? 'no se pudo guardar'));
            return;
        }
        return redirect()->route('vuelos.index')
            ->with('success', $this->vuelo ? 'Vuelo actualizado.' : 'Vuelo creado.');
    }

    public function render()
    {
        return view('livewire.vuelo-form', [
            'aeropuertos' => Aeropuerto::orderBy('ciudad')->get(),
            'aeronaves' => Aeronave::orderBy('matricula')->get(),
            'pilotos' => Piloto::with('empleado')->get(),
            'rutas' => Ruta::with(['origen', 'destino'])->get(),
        ]);
    }
}

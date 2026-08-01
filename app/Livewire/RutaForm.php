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
        } else {
            // Todas las rutas salen de la base: El Trompillo (Santa Cruz).
            $this->origen_id = (string) (Aeropuerto::where('codigo_iata', 'SRZ')->value('id') ?? '');
        }
    }

    /** Aeropuerto(s) válidos como origen: solo Santa Cruz (base de la aerolínea). */
    private function aeropuertosOrigen()
    {
        return Aeropuerto::where('ciudad', 'Santa Cruz de la Sierra')
            ->orderBy('nombre')
            ->get();
    }

    /** Velocidad crucero promedio de la flota (avionetas), en km/h. */
    private const VELOCIDAD_CRUCERO_KMH = 300;

    /** Minutos fijos que suman rodaje, despegue y aproximación. */
    private const MINUTOS_MANIOBRA = 15;

    /** Tarifa fija por boleto (Bs) que se cobra en toda ruta. */
    private const TARIFA_BASE_BS = 150;

    /** Tarifa variable por kilómetro recorrido (Bs). */
    private const TARIFA_POR_KM = 1.40;

    public function updatedOrigenId(): void
    {
        $this->calcularDistanciaYDuracion();
    }

    public function updatedDestinoId(): void
    {
        $this->calcularDistanciaYDuracion();
    }

    /**
     * Calcula la distancia (Haversine) y la duración estimada entre el
     * aeropuerto de origen y el de destino usando sus coordenadas.
     */
    private function calcularDistanciaYDuracion(): void
    {
        if (! $this->origen_id || ! $this->destino_id || $this->origen_id === $this->destino_id) {
            return;
        }

        $origen  = Aeropuerto::find($this->origen_id);
        $destino = Aeropuerto::find($this->destino_id);

        if (! $origen || ! $destino || $origen->latitud === null || $destino->latitud === null) {
            return;
        }

        // --- Fórmula de Haversine (distancia sobre la esfera terrestre) ---
        $radioTierra = 6371; // km
        $dLat = deg2rad((float) $destino->latitud - (float) $origen->latitud);
        $dLon = deg2rad((float) $destino->longitud - (float) $origen->longitud);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad((float) $origen->latitud)) * cos(deg2rad((float) $destino->latitud))
            * sin($dLon / 2) ** 2;

        $km = (int) round(2 * $radioTierra * asin(min(1, sqrt($a))));

        // Duración = tiempo de crucero + maniobras (despegue/aterrizaje).
        $min = (int) round($km / self::VELOCIDAD_CRUCERO_KMH * 60) + self::MINUTOS_MANIOBRA;

        // Precio = tarifa base + tarifa por km, redondeado a la decena.
        $precio = (int) (round((self::TARIFA_BASE_BS + $km * self::TARIFA_POR_KM) / 10) * 10);

        $this->distancia_km          = (string) $km;
        $this->duracion_estimada_min = (string) $min;
        $this->precio_base           = (string) $precio;
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
            'origenes'    => $this->aeropuertosOrigen(),
        ]);
    }
}

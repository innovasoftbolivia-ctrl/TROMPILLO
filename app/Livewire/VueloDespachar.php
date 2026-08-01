<?php

namespace App\Livewire;

use App\Events\EstadoVueloCambiado;
use App\Models\Aeronave;
use App\Models\Boleto;
use App\Models\EnvioCarga;
use App\Models\Piloto;
use App\Models\TripulacionVuelo;
use App\Models\Vuelo;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class VueloDespachar extends Component
{
    public Vuelo $vuelo;

    public string $aeronave_id = '';
    public string $piloto_id = '';
    public string $copiloto_id = '';

    public int $numPax = 0;
    public float $pesoPax = 0;
    public float $pesoEquipaje = 0;
    public float $pesoCarga = 0;
    public float $payload = 0;

    public function mount(Vuelo $vuelo): void
    {
        $vuelo->load(['origen', 'destino']);
        $this->vuelo = $vuelo;

        $this->numPax = Boleto::where('vuelo_id', $vuelo->id)->count();
        $this->pesoPax = (float) Boleto::where('vuelo_id', $vuelo->id)
            ->join('pasajeros', 'pasajeros.id', '=', 'boletos.pasajero_id')
            ->sum('pasajeros.peso_kg');
        $this->pesoEquipaje = (float) Boleto::where('vuelo_id', $vuelo->id)->sum('equipaje_kg');
        $this->pesoCarga = (float) EnvioCarga::where('vuelo_id', $vuelo->id)->sum('peso_kg');
        $this->payload = $this->pesoPax + $this->pesoEquipaje + $this->pesoCarga;

        $this->aeronave_id = (string) $vuelo->aeronave_id;
        $this->piloto_id = (string) $vuelo->piloto_id;
        $this->copiloto_id = (string) $vuelo->copiloto_id;
    }

    public function rules(): array
    {
        return [
            'aeronave_id' => ['required', 'exists:aeronaves,id'],
            'piloto_id' => ['required', 'exists:pilotos,id'],
            'copiloto_id' => ['nullable', 'exists:pilotos,id', 'different:piloto_id'],
        ];
    }

    public function despachar()
    {
        $this->validate();
        try {
            DB::statement('CALL sp_despachar_vuelo(?, ?, ?, ?)', [
                $this->vuelo->id, $this->aeronave_id, $this->piloto_id, $this->copiloto_id ?: null,
            ]);

            TripulacionVuelo::where('vuelo_id', $this->vuelo->id)->delete();
            $empPiloto = Piloto::find($this->piloto_id)?->empleado_id;
            if ($empPiloto) TripulacionVuelo::create(['vuelo_id' => $this->vuelo->id, 'empleado_id' => $empPiloto, 'rol' => 'comandante']);
            
            if ($this->copiloto_id) {
                $empCo = Piloto::find($this->copiloto_id)?->empleado_id;
                if ($empCo) TripulacionVuelo::create(['vuelo_id' => $this->vuelo->id, 'empleado_id' => $empCo, 'rol' => 'primer_oficial']);
            }

            EstadoVueloCambiado::dispatch($this->vuelo->id, 'abordando');

            session()->flash('success', 'Vuelo despachado correctamente. Estado: abordando.');
            return $this->redirect(route('vuelos.show', $this->vuelo->id), navigate: true);
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', "No se pudo despachar: " . ($e->errorInfo[2] ?? 'error'));
        }
    }

    public function render()
    {
        return view('livewire.vuelo-despachar', [
            'aeronaves' => Aeronave::where('estado', 'activa')->orderBy('matricula')->get(),
            'pilotos' => Piloto::with('empleado')->get(),
        ]);
    }
}

<?php

namespace App\Livewire;

use App\Models\Boleto;
use App\Models\Vuelo;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class OperacionesReporte extends Component
{
    #[Url] public string $desde = '';
    #[Url] public string $hasta = '';
    #[Url] public string $estado = '';
    #[Url] public string $tipo = '';

    public function mount(): void
    {
        if ($this->desde === '') $this->desde = today()->startOfMonth()->toDateString();
        if ($this->hasta === '') $this->hasta = today()->toDateString();
    }

    public function render()
    {
        $d = Carbon::parse($this->desde)->startOfDay();
        $h = Carbon::parse($this->hasta)->endOfDay();

        $query = Vuelo::with(['origen', 'destino', 'aeronave', 'piloto.empleado'])
            ->withCount('boletos')->whereBetween('salida_programada', [$d, $h]);

        if ($this->estado !== '') $query->where('estado', $this->estado);
        if ($this->tipo !== '') $query->where('tipo', $this->tipo);

        $vuelos = $query->orderBy('salida_programada')->get();

        $ocupaciones = $vuelos->filter(fn ($v) => $v->aeronave && $v->aeronave->capacidad_pasajeros > 0)
            ->map(fn ($v) => $v->boletos_count / $v->aeronave->capacidad_pasajeros);

        $resumen = [
            'vuelos' => $vuelos->count(),
            'pasajeros' => $vuelos->sum('boletos_count'),
            'ingresos' => (float) Boleto::whereIn('vuelo_id', $vuelos->pluck('id'))->sum('precio'),
            'ocupacion' => $ocupaciones->count() ? (int) round($ocupaciones->avg() * 100) : 0,
        ];

        return view('livewire.operaciones-reporte', ['vuelos' => $vuelos, 'resumen' => $resumen]);
    }
}

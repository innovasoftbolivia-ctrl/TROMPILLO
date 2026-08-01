<?php

namespace App\Livewire;

use App\Models\Vuelo;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class VueloIndex extends Component
{
    use WithPagination;

    #[Url] public string $buscar = '';
    #[Url] public string $estado = '';
    #[Url] public string $tipo = '';
    public ?string $flashOk = null;
    public ?string $flashError = null;

    public function updatingBuscar(): void { $this->resetPage(); }
    public function updatingEstado(): void { $this->resetPage(); }
    public function updatingTipo(): void { $this->resetPage(); }
    public function limpiarFiltros(): void { $this->reset(['buscar', 'estado', 'tipo']); $this->resetPage(); }

    public function eliminar(int $id): void
    {
        try { DB::statement('CALL sp_vuelos_delete(?)', [$id]); $this->flashOk = 'Vuelo eliminado.'; $this->flashError = null; }
        catch (\Illuminate\Database\QueryException $e) { $this->flashError = 'No se pudo eliminar: ' . ($e->errorInfo[2] ?? 'error'); $this->flashOk = null; }
    }

    public function render()
    {
        $query = Vuelo::with(['origen', 'destino', 'aeronave', 'piloto.empleado']);
        if ($this->estado !== '') $query->where('estado', $this->estado);
        if ($this->tipo !== '') $query->where('tipo', $this->tipo);
        if ($this->buscar !== '') {
            $buscar = $this->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('numero_vuelo', 'like', "%{$buscar}%")
                  ->orWhereHas('origen', fn ($s) => $s->where('ciudad', 'like', "%{$buscar}%"))
                  ->orWhereHas('destino', fn ($s) => $s->where('ciudad', 'like', "%{$buscar}%"));
            });
        }
        return view('livewire.vuelo-index', ['vuelos' => $query->orderByDesc('salida_programada')->paginate(10)]);
    }
}

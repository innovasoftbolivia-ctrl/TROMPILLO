<?php

namespace App\Livewire;

use App\Models\Reserva;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ReservaIndex extends Component
{
    use WithPagination;

    #[Url] public string $buscar = '';
    #[Url] public string $estado = '';
    public ?string $flashOk = null;
    public ?string $flashError = null;

    public function updatingBuscar(): void { $this->resetPage(); }
    public function updatingEstado(): void { $this->resetPage(); }
    public function limpiarFiltros(): void { $this->reset(['buscar', 'estado']); $this->resetPage(); }

    public function eliminar(int $id): void
    {
        try { DB::statement('CALL sp_reservas_delete(?)', [$id]); $this->flashOk = 'Reserva eliminada.'; $this->flashError = null; }
        catch (\Illuminate\Database\QueryException $e) { $this->flashError = 'No se pudo eliminar: ' . ($e->errorInfo[2] ?? 'error'); $this->flashOk = null; }
    }

    public function render()
    {
        $query = Reserva::with(['vuelo.origen', 'vuelo.destino', 'titular']);
        if ($this->estado !== '') $query->where('estado', $this->estado);
        if ($this->buscar !== '') $query->where('codigo', 'like', "%{$this->buscar}%");
        
        return view('livewire.reserva-index', ['reservas' => $query->orderByDesc('fecha_reserva')->paginate(10)]);
    }
}

<?php

namespace App\Livewire;

use App\Models\Mantenimiento;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class MantenimientoIndex extends Component
{
    use WithPagination;

    #[Url] public string $estado = '';
    #[Url] public string $tipo = '';
    public ?string $flashOk = null;
    public ?string $flashError = null;

    public function updatingEstado(): void { $this->resetPage(); }
    public function updatingTipo(): void { $this->resetPage(); }
    public function limpiarFiltros(): void { $this->reset(['estado', 'tipo']); $this->resetPage(); }

    public function eliminar(int $id): void
    {
        try { DB::statement('CALL sp_mantenimientos_delete(?)', [$id]); $this->flashOk = 'Mantenimiento eliminado.'; $this->flashError = null; }
        catch (\Illuminate\Database\QueryException $e) { $this->flashError = 'No se pudo eliminar: ' . ($e->errorInfo[2] ?? 'error'); $this->flashOk = null; }
    }

    public function render()
    {
        $query = Mantenimiento::with(['aeronave', 'tecnico']);
        if ($this->estado !== '') $query->where('estado', $this->estado);
        if ($this->tipo !== '') $query->where('tipo', $this->tipo);
        return view('livewire.mantenimiento-index', ['mantenimientos' => $query->orderByDesc('fecha_inicio')->paginate(10)]);
    }
}

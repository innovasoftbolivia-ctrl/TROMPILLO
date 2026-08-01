<?php

namespace App\Livewire;

use App\Models\Aeronave;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AeronaveIndex extends Component
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
        try {
            DB::statement('CALL sp_aeronaves_delete(?)', [$id]);
            $this->flashOk = 'Aeronave eliminada correctamente.';
            $this->flashError = null;
        } catch (\Illuminate\Database\QueryException $e) {
            $this->flashError = 'No se pudo eliminar: ' . ($e->errorInfo[2] ?? 'error');
            $this->flashOk = null;
        }
    }

    public function render()
    {
        $query = Aeronave::query();
        if ($this->estado !== '') $query->where('estado', $this->estado);
        if ($this->buscar !== '') {
            $buscar = $this->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('matricula', 'like', "%{$buscar}%")
                    ->orWhere('modelo', 'like', "%{$buscar}%")
                    ->orWhere('fabricante', 'like', "%{$buscar}%");
            });
        }
        return view('livewire.aeronave-index', [
            'aeronaves' => $query->orderBy('matricula')->paginate(10),
        ]);
    }
}

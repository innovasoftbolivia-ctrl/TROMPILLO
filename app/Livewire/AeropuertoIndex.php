<?php

namespace App\Livewire;

use App\Models\Aeropuerto;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AeropuertoIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $buscar = '';

    #[Url]
    public string $tipo = '';

    public ?string $flashOk = null;
    public ?string $flashError = null;

    public function updatingBuscar(): void
    {
        $this->resetPage();
    }

    public function updatingTipo(): void
    {
        $this->resetPage();
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['buscar', 'tipo']);
        $this->resetPage();
    }

    public function eliminar(int $id): void
    {
        try {
            DB::statement('CALL sp_aeropuertos_delete(?)', [$id]);
            $this->flashOk = 'Aeropuerto eliminado correctamente.';
            $this->flashError = null;
        } catch (\Illuminate\Database\QueryException $e) {
            $this->flashError = 'No se pudo eliminar: ' . ($e->errorInfo[2] ?? 'error');
            $this->flashOk = null;
        }
    }

    public function render()
    {
        $query = Aeropuerto::query();

        if ($this->tipo !== '') {
            $query->where('tipo', $this->tipo);
        }

        if ($this->buscar !== '') {
            $buscar = $this->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo_oaci', 'like', "%{$buscar}%")
                    ->orWhere('nombre', 'like', "%{$buscar}%")
                    ->orWhere('ciudad', 'like', "%{$buscar}%");
            });
        }

        return view('livewire.aeropuerto-index', [
            'aeropuertos' => $query->orderBy('ciudad')->paginate(10),
        ]);
    }
}

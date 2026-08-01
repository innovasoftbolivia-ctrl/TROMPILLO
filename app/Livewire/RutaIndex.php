<?php

namespace App\Livewire;

use App\Models\Aeropuerto;
use App\Models\Ruta;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class RutaIndex extends Component
{
    use WithPagination;

    #[Url] public string $buscar = '';
    #[Url] public string $activa = '';

    public ?string $flashOk = null;
    public ?string $flashError = null;

    public function updatingBuscar(): void { $this->resetPage(); }
    public function updatingActiva(): void { $this->resetPage(); }

    public function limpiarFiltros(): void
    {
        $this->reset(['buscar', 'activa']);
        $this->resetPage();
    }

    public function eliminar(int $id): void
    {
        try {
            DB::statement('CALL sp_rutas_delete(?)', [$id]);
            $this->flashOk = 'Ruta eliminada correctamente.';
            $this->flashError = null;
        } catch (\Illuminate\Database\QueryException $e) {
            $this->flashError = 'No se pudo eliminar: ' . ($e->errorInfo[2] ?? 'error');
            $this->flashOk = null;
        }
    }

    public function render()
    {
        $query = Ruta::with(['origen', 'destino']);

        if ($this->activa !== '' && in_array($this->activa, ['0', '1'], true)) {
            $query->where('activa', $this->activa);
        }

        if ($this->buscar !== '') {
            $buscar = $this->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->whereHas('origen', fn ($s) => $s->where('ciudad', 'like', "%{$buscar}%"))
                    ->orWhereHas('destino', fn ($s) => $s->where('ciudad', 'like', "%{$buscar}%"));
            });
        }

        return view('livewire.ruta-index', [
            'rutas' => $query->orderBy('id')->paginate(10),
        ]);
    }
}

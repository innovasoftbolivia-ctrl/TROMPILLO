<?php

namespace App\Livewire;

use App\Models\Empleado;
use App\Models\Piloto;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class PilotoIndex extends Component
{
    use WithPagination;

    #[Url] public string $buscar = '';
    #[Url] public string $tipo_licencia = '';
    public ?string $flashOk = null;
    public ?string $flashError = null;

    public function updatingBuscar(): void { $this->resetPage(); }
    public function updatingTipoLicencia(): void { $this->resetPage(); }
    public function limpiarFiltros(): void { $this->reset(['buscar', 'tipo_licencia']); $this->resetPage(); }

    public function eliminar(int $id): void
    {
        try {
            DB::statement('CALL sp_pilotos_delete(?)', [$id]);
            $this->flashOk = 'Piloto eliminado correctamente.';
            $this->flashError = null;
        } catch (\Illuminate\Database\QueryException $e) {
            $this->flashError = 'No se pudo eliminar: ' . ($e->errorInfo[2] ?? 'error');
            $this->flashOk = null;
        }
    }

    public function render()
    {
        $query = Piloto::with('empleado');
        if ($this->tipo_licencia !== '') $query->where('tipo_licencia', $this->tipo_licencia);
        if ($this->buscar !== '') $query->where('licencia_numero', 'like', "%{$this->buscar}%");
        return view('livewire.piloto-index', ['pilotos' => $query->orderBy('licencia_numero')->paginate(10)]);
    }
}

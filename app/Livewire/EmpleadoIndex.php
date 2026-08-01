<?php

namespace App\Livewire;

use App\Models\Empleado;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class EmpleadoIndex extends Component
{
    use WithPagination;

    #[Url] public string $buscar = '';
    #[Url] public string $cargo = '';
    public ?string $flashOk = null;
    public ?string $flashError = null;

    public function updatingBuscar(): void { $this->resetPage(); }
    public function updatingCargo(): void { $this->resetPage(); }
    public function limpiarFiltros(): void { $this->reset(['buscar', 'cargo']); $this->resetPage(); }

    public function eliminar(int $id): void
    {
        try {
            DB::statement('CALL sp_empleados_delete(?)', [$id]);
            $this->flashOk = 'Empleado eliminado correctamente.';
            $this->flashError = null;
        } catch (\Illuminate\Database\QueryException $e) {
            $this->flashError = 'No se pudo eliminar: ' . ($e->errorInfo[2] ?? 'error');
            $this->flashOk = null;
        }
    }

    public function render()
    {
        $query = Empleado::query();
        if ($this->cargo !== '') $query->where('cargo', $this->cargo);
        if ($this->buscar !== '') {
            $buscar = $this->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombres', 'like', "%{$buscar}%")->orWhere('apellidos', 'like', "%{$buscar}%")->orWhere('numero_documento', 'like', "%{$buscar}%");
            });
        }
        return view('livewire.empleado-index', ['empleados' => $query->orderBy('apellidos')->paginate(10)]);
    }
}

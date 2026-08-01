<?php

namespace App\Livewire;

use App\Models\Persona;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class PersonaIndex extends Component
{
    use WithPagination;

    #[Url] public string $buscar = '';
    #[Url] public string $tipo_persona = '';
    public ?string $flashOk = null;
    public ?string $flashError = null;

    public function updatingBuscar(): void { $this->resetPage(); }
    public function updatingTipoPersona(): void { $this->resetPage(); }
    public function limpiarFiltros(): void { $this->reset(['buscar', 'tipo_persona']); $this->resetPage(); }

    public function eliminar(int $id): void
    {
        try {
            Persona::findOrFail($id)->delete();
            $this->flashOk = 'Persona eliminada correctamente.';
            $this->flashError = null;
        } catch (\Illuminate\Database\QueryException $e) {
            $this->flashError = 'No se pudo eliminar: ' . ($e->errorInfo[2] ?? 'error');
            $this->flashOk = null;
        }
    }

    public function render()
    {
        $query = Persona::with(['natural', 'juridica', 'pais']);
        if ($this->tipo_persona !== '') $query->where('tipo_persona', $this->tipo_persona);
        if ($this->buscar !== '') {
            $buscar = $this->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('numero_documento', 'like', "%{$buscar}%")
                    ->orWhereHas('natural', fn ($s) => $s->where('nombres', 'like', "%{$buscar}%")->orWhere('apellidos', 'like', "%{$buscar}%"))
                    ->orWhereHas('juridica', fn ($s) => $s->where('razon_social', 'like', "%{$buscar}%"));
            });
        }
        return view('livewire.persona-index', ['personas' => $query->orderByDesc('id')->paginate(10)]);
    }
}

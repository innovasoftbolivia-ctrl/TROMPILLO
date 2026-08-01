<?php

namespace App\Livewire;

use App\Models\Rol;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class UsuarioIndex extends Component
{
    use WithPagination;

    #[Url] public string $buscar = '';
    #[Url] public string $role_id = '';
    public ?string $flashOk = null;
    public ?string $flashError = null;

    public function updatingBuscar(): void { $this->resetPage(); }
    public function updatingRoleId(): void { $this->resetPage(); }
    public function limpiarFiltros(): void { $this->reset(['buscar', 'role_id']); $this->resetPage(); }

    public function eliminar(int $id): void
    {
        if ($id === auth()->id()) { $this->flashError = 'No puedes eliminar tu propia cuenta.'; return; }
        try {
            User::findOrFail($id)->delete();
            $this->flashOk = 'Usuario eliminado.';
            $this->flashError = null;
        } catch (\Exception $e) {
            $this->flashError = 'No se pudo eliminar: ' . $e->getMessage();
            $this->flashOk = null;
        }
    }

    public function render()
    {
        $query = User::with('role');
        if ($this->role_id !== '') $query->where('role_id', $this->role_id);
        if ($this->buscar !== '') {
            $buscar = $this->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('name', 'like', "%{$buscar}%")->orWhere('email', 'like', "%{$buscar}%");
            });
        }
        
        return view('livewire.usuario-index', [
            'usuarios' => $query->orderBy('name')->paginate(10),
            'roles' => Rol::orderBy('nombre')->get()
        ]);
    }
}

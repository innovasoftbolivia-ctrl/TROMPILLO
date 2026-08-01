<?php

namespace App\Livewire;

use App\Models\Permiso;
use App\Models\Rol;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class RolIndex extends Component
{
    public ?string $flashOk = null;
    public ?string $flashError = null;

    public function eliminar(int $id): void
    {
        try {
            $rol = Rol::findOrFail($id);
            if ($rol->nombre === 'administrador') { $this->flashError = 'No se puede eliminar administrador.'; return; }
            if ($rol->usuarios()->count() > 0) { $this->flashError = 'El rol tiene usuarios asignados.'; return; }
            $rol->delete();
            $this->flashOk = 'Rol eliminado.';
            $this->flashError = null;
        } catch (\Exception $e) {
            $this->flashError = 'No se pudo eliminar: ' . $e->getMessage();
            $this->flashOk = null;
        }
    }

    public function render()
    {
        return view('livewire.rol-index', ['roles' => Rol::withCount(['permisos', 'usuarios'])->orderBy('nombre')->get()]);
    }
}

<?php

namespace App\Livewire;

use App\Models\Permiso;
use App\Models\Rol;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class RolForm extends Component
{
    public ?Rol $rol = null;
    public bool $isEdit = false;

    public string $nombre = '';
    public string $descripcion = '';
    public array $permisosSeleccionados = [];

    public function mount($rol = null): void
    {
        if ($rol && ! $rol instanceof Rol) {
            $rol = Rol::findOrFail($rol);
        }
        if ($rol && $rol->exists) {
            $this->rol = $rol;
            $this->isEdit = true;
            $this->nombre = $rol->nombre;
            $this->descripcion = $rol->descripcion ?? '';
            $this->permisosSeleccionados = $rol->permisos->pluck('id')->map(fn($id) => (string)$id)->toArray();
        }
    }

    protected function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:50', 'unique:roles,nombre,' . ($this->rol ? $this->rol->id : 'NULL')],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'permisosSeleccionados' => ['array'],
            'permisosSeleccionados.*' => ['exists:permisos,id'],
        ];
    }

    public function guardar()
    {
        $this->validate();

        try {
            if ($this->isEdit) {
                $this->rol->update(['nombre' => $this->nombre, 'descripcion' => $this->descripcion ?: null]);
                $this->rol->permisos()->sync($this->permisosSeleccionados);
            } else {
                $rol = Rol::create(['nombre' => $this->nombre, 'descripcion' => $this->descripcion ?: null]);
                $rol->permisos()->sync($this->permisosSeleccionados);
            }

            return redirect()->route('roles.index')->with('success', $this->isEdit ? 'Rol actualizado.' : 'Rol creado.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.rol-form', ['permisos' => Permiso::orderBy('clave')->get()]);
    }
}

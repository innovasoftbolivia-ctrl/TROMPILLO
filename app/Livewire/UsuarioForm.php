<?php

namespace App\Livewire;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class UsuarioForm extends Component
{
    public ?User $usuario = null;
    public bool $isEdit = false;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role_id = '';

    public function mount($usuario = null): void
    {
        if ($usuario && ! $usuario instanceof User) {
            $usuario = User::findOrFail($usuario);
        }
        if ($usuario && $usuario->exists) {
            $this->usuario = $usuario;
            $this->isEdit = true;
            $this->name = $usuario->name;
            $this->email = $usuario->email;
            $this->role_id = (string) $usuario->role_id;
        }
    }

    protected function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . ($this->usuario ? $this->usuario->id : 'NULL')],
            'role_id' => ['required', 'exists:roles,id'],
        ];

        if (!$this->isEdit) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        } else {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }

    private function mapearRolEnum(?Rol $rol): string
    {
        $nombre = strtolower($rol->nombre ?? '');
        $mapa = ['administrador' => 'admin', 'admin' => 'admin', 'operador' => 'operador', 'vendedor' => 'vendedor', 'piloto' => 'piloto'];
        return $mapa[$nombre] ?? 'vendedor';
    }

    public function guardar()
    {
        $this->validate();

        try {
            $rol = Rol::find($this->role_id);
            $enumRol = $this->mapearRolEnum($rol);

            if ($this->isEdit) {
                $this->usuario->name = $this->name;
                $this->usuario->email = $this->email;
                $this->usuario->role_id = $this->role_id;
                $this->usuario->rol = $enumRol;
                if (!empty($this->password)) $this->usuario->password = Hash::make($this->password);
                $this->usuario->save();
            } else {
                User::create([
                    'name' => $this->name, 'email' => $this->email,
                    'password' => Hash::make($this->password),
                    'role_id' => $this->role_id, 'rol' => $enumRol
                ]);
            }

            return redirect()->route('usuarios.index')->with('success', $this->isEdit ? 'Usuario actualizado.' : 'Usuario creado.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.usuario-form', ['roles' => Rol::orderBy('nombre')->get()]);
    }
}

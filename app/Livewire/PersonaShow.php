<?php

namespace App\Livewire;

use App\Models\Persona;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PersonaShow extends Component
{
    public Persona $persona;

    public function mount(Persona $persona): void
    {
        $persona->load(['natural', 'juridica', 'pais']);
        $this->persona = $persona;
    }

    public function eliminar(): void
    {
        $this->persona->delete();
        redirect()->route('personas.index')->with('success', 'Persona eliminada correctamente.');
    }

    public function render() { return view('livewire.persona-show'); }
}

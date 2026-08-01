<?php

namespace App\Livewire;

use App\Models\Piloto;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PilotoShow extends Component
{
    public Piloto $piloto;

    public function mount(Piloto $piloto): void
    {
        $piloto->load('empleado');
        $this->piloto = $piloto;
    }

    public function eliminar(): void
    {
        try { DB::statement('CALL sp_pilotos_delete(?)', [$this->piloto->id]); }
        catch (\Illuminate\Database\QueryException $e) { session()->flash('error', 'No se pudo eliminar: ' . ($e->errorInfo[2] ?? 'error')); return; }
        redirect()->route('pilotos.index')->with('success', 'Piloto eliminado correctamente.');
    }

    public function render() { return view('livewire.piloto-show'); }
}

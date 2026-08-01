<?php

namespace App\Livewire;

use App\Models\Ruta;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class RutaShow extends Component
{
    public Ruta $ruta;

    public function mount(Ruta $ruta): void
    {
        $ruta->load(['origen', 'destino']);
        $this->ruta = $ruta;
    }

    public function eliminar(): void
    {
        try {
            DB::statement('CALL sp_rutas_delete(?)', [$this->ruta->id]);
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'No se pudo eliminar: ' . ($e->errorInfo[2] ?? 'error'));
            return;
        }
        redirect()->route('rutas.index')->with('success', 'Ruta eliminada correctamente.');
    }

    public function render()
    {
        return view('livewire.ruta-show');
    }
}

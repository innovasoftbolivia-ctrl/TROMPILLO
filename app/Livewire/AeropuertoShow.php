<?php

namespace App\Livewire;

use App\Models\Aeropuerto;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AeropuertoShow extends Component
{
    public Aeropuerto $aeropuerto;

    public function eliminar(): void
    {
        try {
            DB::statement('CALL sp_aeropuertos_delete(?)', [$this->aeropuerto->id]);
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'No se pudo eliminar: ' . ($e->errorInfo[2] ?? 'error'));
            return;
        }

        redirect()->route('aeropuertos.index')->with('success', 'Aeropuerto eliminado correctamente.');
    }

    public function render()
    {
        return view('livewire.aeropuerto-show');
    }
}

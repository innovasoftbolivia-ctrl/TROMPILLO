<?php

namespace App\Livewire;

use App\Models\Pasajero;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PasajeroShow extends Component
{
    public Pasajero $pasajero;

    public function eliminar(): void
    {
        try {
            DB::statement('CALL sp_pasajeros_delete(?)', [$this->pasajero->id]);
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'No se pudo eliminar: ' . ($e->errorInfo[2] ?? 'error'));
            return;
        }
        redirect()->route('pasajeros.index')->with('success', 'Pasajero eliminado correctamente.');
    }

    public function render() { return view('livewire.pasajero-show'); }
}

<?php

namespace App\Livewire;

use App\Models\Aeronave;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AeronaveShow extends Component
{
    public Aeronave $aeronave;

    public function eliminar(): void
    {
        try {
            DB::statement('CALL sp_aeronaves_delete(?)', [$this->aeronave->id]);
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'No se pudo eliminar: ' . ($e->errorInfo[2] ?? 'error'));
            return;
        }
        redirect()->route('aeronaves.index')->with('success', 'Aeronave eliminada correctamente.');
    }

    public function render() { return view('livewire.aeronave-show'); }
}

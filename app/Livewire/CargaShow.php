<?php

namespace App\Livewire;

use App\Models\EnvioCarga;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CargaShow extends Component
{
    public EnvioCarga $envio;

    public function mount($carga): void
    {
        $this->envio = EnvioCarga::with('vuelo.origen', 'vuelo.destino')->findOrFail($carga);
    }

    public function eliminar(): void
    {
        try { DB::statement('CALL sp_envios_carga_delete(?)', [$this->envio->id]); }
        catch (\Illuminate\Database\QueryException $e) { session()->flash('error', 'No se pudo eliminar: ' . ($e->errorInfo[2] ?? 'error')); return; }
        redirect()->route('carga.index')->with('success', 'Envío eliminado.');
    }

    public function render() { return view('livewire.carga-show'); }
}

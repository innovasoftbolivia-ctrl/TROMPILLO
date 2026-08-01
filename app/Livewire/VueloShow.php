<?php

namespace App\Livewire;

use App\Models\Vuelo;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class VueloShow extends Component
{
    public Vuelo $vuelo;

    public function mount(Vuelo $vuelo): void
    {
        $vuelo->load(['origen', 'destino', 'aeronave', 'piloto.empleado', 'copiloto.empleado', 'ruta', 'reservas', 'enviosCarga']);
        $this->vuelo = $vuelo;
    }

    public function cerrar()
    {
        try {
            DB::statement('CALL sp_cerrar_vuelo(?, ?, @usados, @no_show)', [
                $this->vuelo->id, now()->format('Y-m-d H:i:s'),
            ]);
            $r = DB::selectOne('SELECT @usados AS usados, @no_show AS no_show');
            session()->flash('success', "Vuelo cerrado (en vuelo). {$r->usados} abordaron, {$r->no_show} no-show.");
            return $this->redirect(route('vuelos.show', $this->vuelo->id), navigate: true);
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', "No se pudo cerrar: " . ($e->errorInfo[2] ?? 'error'));
        }
    }

    public function aterrizar()
    {
        try {
            DB::statement('CALL sp_aterrizar_vuelo(?, ?)', [
                $this->vuelo->id, now()->format('Y-m-d H:i:s'),
            ]);
            session()->flash('success', 'Aterrizaje registrado. Vuelo en estado aterrizado.');
            return $this->redirect(route('vuelos.show', $this->vuelo->id), navigate: true);
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', "No se pudo aterrizar: " . ($e->errorInfo[2] ?? 'error'));
        }
    }

    public function render() { return view('livewire.vuelo-show'); }
}

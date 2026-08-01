<?php

namespace App\Livewire;

use App\Models\Mantenimiento;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MantenimientoShow extends Component
{
    public Mantenimiento $mantenimiento;

    public function mount(Mantenimiento $mantenimiento): void
    {
        $mantenimiento->load(['aeronave', 'tecnico']);
        $this->mantenimiento = $mantenimiento;
    }

    public function eliminar(): void
    {
        try { DB::statement('CALL sp_mantenimientos_delete(?)', [$this->mantenimiento->id]); }
        catch (\Illuminate\Database\QueryException $e) { session()->flash('error', 'No se pudo eliminar: ' . ($e->errorInfo[2] ?? 'error')); return; }
        redirect()->route('mantenimientos.index')->with('success', 'Mantenimiento eliminado.');
    }

    public function render() { return view('livewire.mantenimiento-show'); }
}

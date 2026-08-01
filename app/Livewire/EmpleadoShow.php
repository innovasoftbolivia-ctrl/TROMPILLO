<?php

namespace App\Livewire;

use App\Models\Empleado;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class EmpleadoShow extends Component
{
    public Empleado $empleado;

    public function eliminar(): void
    {
        try { DB::statement('CALL sp_empleados_delete(?)', [$this->empleado->id]); }
        catch (\Illuminate\Database\QueryException $e) { session()->flash('error', 'No se pudo eliminar: ' . ($e->errorInfo[2] ?? 'error')); return; }
        redirect()->route('empleados.index')->with('success', 'Empleado eliminado correctamente.');
    }

    public function render() { return view('livewire.empleado-show'); }
}

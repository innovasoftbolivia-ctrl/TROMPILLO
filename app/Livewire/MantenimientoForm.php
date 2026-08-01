<?php

namespace App\Livewire;

use App\Models\Aeronave;
use App\Models\Empleado;
use App\Models\Mantenimiento;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MantenimientoForm extends Component
{
    public ?Mantenimiento $mantenimiento = null;

    public string $aeronave_id = '';
    public string $tecnico_id = '';
    public string $tipo = 'preventivo';
    public string $descripcion = '';
    public ?string $fecha_inicio = null;
    public ?string $fecha_fin = null;
    public ?string $horas_vuelo_aeronave = null;
    public string $costo = '0';
    public string $estado = 'programado';

    public function mount($mantenimiento = null): void
    {
        if ($mantenimiento && ! $mantenimiento instanceof Mantenimiento) {
            $mantenimiento = Mantenimiento::findOrFail($mantenimiento);
        }
        if ($mantenimiento && $mantenimiento->exists) {
            $this->mantenimiento = $mantenimiento;
            $this->aeronave_id = (string) $mantenimiento->aeronave_id;
            $this->tecnico_id = (string) ($mantenimiento->tecnico_id ?? '');
            $this->tipo = $mantenimiento->tipo ?? 'preventivo';
            $this->descripcion = $mantenimiento->descripcion ?? '';
            $this->fecha_inicio = $mantenimiento->fecha_inicio;
            $this->fecha_fin = $mantenimiento->fecha_fin;
            $this->horas_vuelo_aeronave = $mantenimiento->horas_vuelo_aeronave;
            $this->costo = (string) ($mantenimiento->costo ?? 0);
            $this->estado = $mantenimiento->estado ?? 'programado';
        }
    }

    public function rules(): array
    {
        return [
            'aeronave_id' => ['required', 'exists:aeronaves,id'],
            'tecnico_id' => ['nullable', 'exists:empleados,id'],
            'tipo' => ['required', 'in:preventivo,correctivo,inspeccion,revision_100h,revision_anual'],
            'descripcion' => ['required', 'string', 'max:500'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'horas_vuelo_aeronave' => ['nullable', 'numeric', 'min:0'],
            'costo' => ['required', 'numeric', 'min:0'],
            'estado' => ['required', 'in:programado,en_proceso,completado,cancelado'],
        ];
    }

    public function guardar()
    {
        $this->validate();
        try {
            if ($this->mantenimiento) {
                DB::statement('CALL sp_mantenimientos_update(?,?,?,?,?,?,?,?,?,?)', [
                    $this->mantenimiento->id, $this->aeronave_id, $this->tecnico_id ?: null, $this->tipo,
                    $this->descripcion, $this->fecha_inicio, $this->fecha_fin ?: null,
                    $this->horas_vuelo_aeronave ?: null, $this->costo, $this->estado,
                ]);
            } else {
                DB::statement('CALL sp_mantenimientos_insert(?,?,?,?,?,?,?,?,?)', [
                    $this->aeronave_id, $this->tecnico_id ?: null, $this->tipo, $this->descripcion,
                    $this->fecha_inicio, $this->fecha_fin ?: null, $this->horas_vuelo_aeronave ?: null,
                    $this->costo, $this->estado,
                ]);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Error: ' . ($e->errorInfo[2] ?? 'no se pudo guardar'));
            return;
        }
        return redirect()->route('mantenimientos.index')
            ->with('success', $this->mantenimiento ? 'Mantenimiento actualizado.' : 'Mantenimiento creado.');
    }

    public function render()
    {
        return view('livewire.mantenimiento-form', [
            'aeronaves' => Aeronave::orderBy('matricula')->get(),
            'empleados' => Empleado::orderBy('apellidos')->get(),
        ]);
    }
}

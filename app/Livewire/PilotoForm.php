<?php

namespace App\Livewire;

use App\Models\Empleado;
use App\Models\Piloto;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PilotoForm extends Component
{
    public ?Piloto $piloto = null;

    public string $empleado_id = '';
    public string $licencia_numero = '';
    public string $tipo_licencia = 'CPL';
    public string $horas_vuelo = '0';
    public ?string $vencimiento_licencia = null;
    public ?string $vencimiento_medico = null;
    public ?string $habilitaciones = null;

    public function mount($piloto = null): void
    {
        if ($piloto && ! $piloto instanceof Piloto) {
            $piloto = Piloto::findOrFail($piloto);
        }
        if ($piloto && $piloto->exists) {
            $this->piloto = $piloto;
            $this->empleado_id = (string) $piloto->empleado_id;
            $this->licencia_numero = $piloto->licencia_numero ?? '';
            $this->tipo_licencia = $piloto->tipo_licencia ?? 'CPL';
            $this->horas_vuelo = (string) ($piloto->horas_vuelo ?? 0);
            $this->vencimiento_licencia = $piloto->vencimiento_licencia;
            $this->vencimiento_medico = $piloto->vencimiento_medico;
            $this->habilitaciones = $piloto->habilitaciones;
        }
    }

    public function rules(): array
    {
        return [
            'empleado_id' => ['required', 'exists:empleados,id'],
            'licencia_numero' => ['required', 'string', 'max:50', Rule::unique('pilotos')->ignore($this->piloto?->id)],
            'tipo_licencia' => ['required', 'in:PPL,CPL,ATPL,PCA'],
            'horas_vuelo' => ['required', 'numeric', 'min:0'],
            'vencimiento_licencia' => ['nullable', 'date'],
            'vencimiento_medico' => ['nullable', 'date'],
            'habilitaciones' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function guardar()
    {
        $this->validate();
        try {
            if ($this->piloto) {
                DB::statement('CALL sp_pilotos_update(?,?,?,?,?,?,?,?)', [
                    $this->piloto->id, $this->empleado_id, $this->licencia_numero, $this->tipo_licencia,
                    $this->horas_vuelo, $this->vencimiento_licencia ?: null, $this->vencimiento_medico ?: null, $this->habilitaciones ?: null,
                ]);
            } else {
                DB::statement('CALL sp_pilotos_insert(?,?,?,?,?,?,?)', [
                    $this->empleado_id, $this->licencia_numero, $this->tipo_licencia,
                    $this->horas_vuelo, $this->vencimiento_licencia ?: null, $this->vencimiento_medico ?: null, $this->habilitaciones ?: null,
                ]);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Error: ' . ($e->errorInfo[2] ?? 'no se pudo guardar'));
            return;
        }
        return redirect()->route('pilotos.index')
            ->with('success', $this->piloto ? 'Piloto actualizado correctamente.' : 'Piloto creado correctamente.');
    }

    public function render()
    {
        return view('livewire.piloto-form', ['empleados' => Empleado::orderBy('apellidos')->get()]);
    }
}

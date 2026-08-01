<?php

namespace App\Livewire;

use App\Models\Empleado;
use App\Models\Persona;
use App\Models\PersonaNatural;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class EmpleadoForm extends Component
{
    public ?Empleado $empleado = null;

    public string $nombres = '';
    public string $apellidos = '';
    public string $tipo_documento = 'CI';
    public string $numero_documento = '';
    public string $cargo = 'administrativo';
    public ?string $telefono = null;
    public ?string $email = null;
    public ?string $fecha_nacimiento = null;
    public ?string $fecha_contratacion = null;
    public ?string $salario = null;
    public bool $activo = true;

    public function mount($empleado = null): void
    {
        if ($empleado && ! $empleado instanceof Empleado) {
            $empleado = Empleado::findOrFail($empleado);
        }
        if ($empleado && $empleado->exists) {
            $this->empleado = $empleado;
            $this->nombres = $empleado->nombres ?? '';
            $this->apellidos = $empleado->apellidos ?? '';
            $this->tipo_documento = $empleado->tipo_documento ?? 'CI';
            $this->numero_documento = $empleado->numero_documento ?? '';
            $this->cargo = $empleado->cargo ?? 'administrativo';
            $this->telefono = $empleado->telefono;
            $this->email = $empleado->email;
            $this->fecha_nacimiento = $empleado->fecha_nacimiento;
            $this->fecha_contratacion = $empleado->fecha_contratacion;
            $this->salario = $empleado->salario;
            $this->activo = (bool) $empleado->activo;
        }
    }

    public function rules(): array
    {
        return [
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'tipo_documento' => ['nullable', 'string', 'max:10'],
            'numero_documento' => ['required', 'string', 'max:30', Rule::unique('empleados')->ignore($this->empleado?->id)],
            'cargo' => ['required', 'in:piloto,copiloto,tecnico,despachador,administrativo,ventas,gerente'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'fecha_contratacion' => ['nullable', 'date'],
            'salario' => ['nullable', 'numeric', 'min:0'],
            'activo' => ['boolean'],
        ];
    }

    public function guardar()
    {
        $this->validate();
        try {
            if ($this->empleado) {
                DB::statement('CALL sp_empleados_update(?,?,?,?,?,?,?,?,?,?,?,?)', [
                    $this->empleado->id, $this->nombres, $this->apellidos, $this->tipo_documento,
                    $this->numero_documento, $this->cargo, $this->telefono ?: null, $this->email ?: null,
                    $this->fecha_nacimiento ?: null, $this->fecha_contratacion ?: null, $this->salario ?: null, (int) $this->activo,
                ]);
                $personaId = $this->sincronizarPersona();
                DB::table('empleados')->where('id', $this->empleado->id)->update(['persona_id' => $personaId]);
            } else {
                DB::statement('CALL sp_empleados_insert(?,?,?,?,?,?,?,?,?,?,?)', [
                    $this->nombres, $this->apellidos, $this->tipo_documento, $this->numero_documento,
                    $this->cargo, $this->telefono ?: null, $this->email ?: null,
                    $this->fecha_nacimiento ?: null, $this->fecha_contratacion ?: null, $this->salario ?: null, (int) $this->activo,
                ]);
                $id = DB::selectOne('SELECT LAST_INSERT_ID() AS id')->id;
                $personaId = $this->sincronizarPersona();
                DB::table('empleados')->where('id', $id)->update(['persona_id' => $personaId]);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Error: ' . ($e->errorInfo[2] ?? 'no se pudo guardar'));
            return;
        }
        return redirect()->route('empleados.index')
            ->with('success', $this->empleado ? 'Empleado actualizado correctamente.' : 'Empleado creado correctamente.');
    }

    private function sincronizarPersona(): int
    {
        $persona = Persona::firstOrCreate(
            ['tipo_documento' => $this->tipo_documento ?: 'CI', 'numero_documento' => $this->numero_documento],
            ['tipo_persona' => 'natural', 'telefono' => $this->telefono, 'email' => $this->email]
        );
        PersonaNatural::updateOrCreate(
            ['persona_id' => $persona->id],
            ['nombres' => $this->nombres, 'apellidos' => $this->apellidos, 'fecha_nacimiento' => $this->fecha_nacimiento]
        );
        return $persona->id;
    }

    public function render() { return view('livewire.empleado-form'); }
}

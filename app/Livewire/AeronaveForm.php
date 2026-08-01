<?php

namespace App\Livewire;

use App\Models\Aeronave;
use App\Models\ModeloAeronave;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AeronaveForm extends Component
{
    public ?Aeronave $aeronave = null;

    public string $matricula = '';
    public string $modelo = '';
    public string $fabricante = '';
    public ?string $ano_fabricacion = null;
    public ?string $capacidad_pasajeros = null;
    public string $capacidad_carga_kg = '0';
    public ?string $peso_vacio_kg = null;
    public ?string $peso_maximo_despegue_kg = null;
    public ?string $autonomia_km = null;
    public ?string $velocidad_crucero_kmh = null;
    public string $horas_vuelo_totales = '0';
    public ?string $fecha_ultima_revision = null;
    public ?string $fecha_proxima_revision = null;
    public string $estado = 'activa';

    public function mount($aeronave = null): void
    {
        if ($aeronave && ! $aeronave instanceof Aeronave) {
            $aeronave = Aeronave::findOrFail($aeronave);
        }
        if ($aeronave && $aeronave->exists) {
            $this->aeronave = $aeronave;
            $this->matricula = $aeronave->matricula ?? '';
            $this->modelo = $aeronave->modelo ?? '';
            $this->fabricante = $aeronave->fabricante ?? '';
            $this->ano_fabricacion = $aeronave->ano_fabricacion;
            $this->capacidad_pasajeros = $aeronave->capacidad_pasajeros;
            $this->capacidad_carga_kg = (string) ($aeronave->capacidad_carga_kg ?? 0);
            $this->peso_vacio_kg = $aeronave->peso_vacio_kg;
            $this->peso_maximo_despegue_kg = $aeronave->peso_maximo_despegue_kg;
            $this->autonomia_km = $aeronave->autonomia_km;
            $this->velocidad_crucero_kmh = $aeronave->velocidad_crucero_kmh;
            $this->horas_vuelo_totales = (string) ($aeronave->horas_vuelo_totales ?? 0);
            $this->fecha_ultima_revision = $aeronave->fecha_ultima_revision;
            $this->fecha_proxima_revision = $aeronave->fecha_proxima_revision;
            $this->estado = $aeronave->estado ?? 'activa';
        }
    }

    public function rules(): array
    {
        return [
            'matricula'               => ['required', 'string', 'max:10', Rule::unique('aeronaves')->ignore($this->aeronave?->id)],
            'modelo'                  => ['required', 'string', 'max:50'],
            'fabricante'              => ['required', 'string', 'max:50'],
            'ano_fabricacion'         => ['nullable', 'integer', 'min:1900', 'max:2099'],
            'capacidad_pasajeros'     => ['nullable', 'integer', 'min:0'],
            'capacidad_carga_kg'      => ['required', 'numeric', 'min:0'],
            'peso_vacio_kg'           => ['nullable', 'numeric', 'min:0'],
            'peso_maximo_despegue_kg' => ['nullable', 'numeric', 'min:0'],
            'autonomia_km'            => ['nullable', 'integer', 'min:0'],
            'velocidad_crucero_kmh'   => ['nullable', 'integer', 'min:0'],
            'horas_vuelo_totales'     => ['required', 'numeric', 'min:0'],
            'fecha_ultima_revision'   => ['nullable', 'date'],
            'fecha_proxima_revision'  => ['nullable', 'date'],
            'estado'                  => ['required', 'in:activa,mantenimiento,inactiva,retirada'],
        ];
    }

    public function guardar()
    {
        $this->validate();
        try {
            if ($this->aeronave) {
                DB::statement('CALL sp_aeronaves_update(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', [
                    $this->aeronave->id, $this->matricula, $this->modelo, $this->fabricante,
                    $this->ano_fabricacion ?: null, $this->capacidad_pasajeros ?: null,
                    $this->capacidad_carga_kg, $this->peso_vacio_kg ?: null,
                    $this->peso_maximo_despegue_kg ?: null, $this->autonomia_km ?: null,
                    $this->velocidad_crucero_kmh ?: null, $this->horas_vuelo_totales,
                    $this->fecha_ultima_revision ?: null, $this->fecha_proxima_revision ?: null, $this->estado,
                ]);
                $mid = $this->sincronizarModelo();
                DB::table('aeronaves')->where('id', $this->aeronave->id)->update(['modelo_aeronave_id' => $mid]);
            } else {
                DB::statement('CALL sp_aeronaves_insert(?,?,?,?,?,?,?,?,?,?,?,?,?,?)', [
                    $this->matricula, $this->modelo, $this->fabricante, $this->ano_fabricacion ?: null,
                    $this->capacidad_pasajeros ?: null, $this->capacidad_carga_kg, $this->peso_vacio_kg ?: null,
                    $this->peso_maximo_despegue_kg ?: null, $this->autonomia_km ?: null,
                    $this->velocidad_crucero_kmh ?: null, $this->horas_vuelo_totales,
                    $this->fecha_ultima_revision ?: null, $this->fecha_proxima_revision ?: null, $this->estado,
                ]);
                $id = DB::selectOne('SELECT LAST_INSERT_ID() AS id')->id;
                $mid = $this->sincronizarModelo();
                DB::table('aeronaves')->where('id', $id)->update(['modelo_aeronave_id' => $mid]);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Error: ' . ($e->errorInfo[2] ?? 'no se pudo guardar'));
            return;
        }
        return redirect()->route('aeronaves.index')
            ->with('success', $this->aeronave ? 'Aeronave actualizada correctamente.' : 'Aeronave creada correctamente.');
    }

    private function sincronizarModelo(): int
    {
        return ModeloAeronave::updateOrCreate(
            ['fabricante' => $this->fabricante, 'modelo' => $this->modelo],
            [
                'capacidad_pasajeros' => $this->capacidad_pasajeros ?? 0,
                'capacidad_carga_kg' => $this->capacidad_carga_kg ?? 0,
                'peso_vacio_kg' => $this->peso_vacio_kg ?: null,
                'peso_maximo_despegue_kg' => $this->peso_maximo_despegue_kg ?: null,
                'autonomia_km' => $this->autonomia_km ?: null,
                'velocidad_crucero_kmh' => $this->velocidad_crucero_kmh ?: null,
            ]
        )->id;
    }

    public function render() { return view('livewire.aeronave-form'); }
}

<?php

namespace App\Livewire;

use App\Events\EstadoVueloCambiado;
use App\Models\Aeronave;
use App\Models\Vuelo;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class OperacionesPanel extends Component
{
    #[Url]
    public string $fecha = '';

    #[Url]
    public string $tipo = '';

    #[Url]
    public string $aeronaveId = '';

    #[Url]
    public string $buscar = '';

    public ?string $flashOk = null;
    public ?string $flashError = null;

    public function mount(): void
    {
        if ($this->fecha === '') {
            $prox = Vuelo::whereDate('salida_programada', '>=', today())
                ->orderBy('salida_programada')
                ->value('salida_programada');
            $this->fecha = $prox ? Carbon::parse($prox)->toDateString() : today()->toDateString();
        }
    }

    public function irDia(int $delta): void
    {
        $this->fecha = Carbon::parse($this->fecha)->addDays($delta)->toDateString();
        $this->limpiarFlash();
    }

    public function hoy(): void
    {
        $this->fecha = today()->toDateString();
        $this->limpiarFlash();
    }

    public function cerrar(int $vueloId): void
    {
        if (! auth()->user()->can('operaciones.despachar')) {
            $this->flashError = 'No tienes permiso para operar vuelos.';
            return;
        }
        try {
            DB::statement('CALL sp_cerrar_vuelo(?, ?, @u, @n)', [$vueloId, now()->format('Y-m-d H:i:s')]);
            $r = DB::selectOne('SELECT @u AS u, @n AS n');
            
            EstadoVueloCambiado::dispatch($vueloId, 'en_vuelo');
            
            $this->flashOk = "Vuelo cerrado: {$r->u} abordaron, {$r->n} no-show.";
            $this->flashError = null;
        } catch (QueryException $e) {
            $this->flashError = $e->errorInfo[2] ?? 'No se pudo cerrar el vuelo.';
            $this->flashOk = null;
        }
    }

    public function aterrizar(int $vueloId): void
    {
        if (! auth()->user()->can('operaciones.despachar')) {
            $this->flashError = 'No tienes permiso para operar vuelos.';
            return;
        }
        try {
            DB::statement('CALL sp_aterrizar_vuelo(?, ?)', [$vueloId, now()->format('Y-m-d H:i:s')]);
            
            EstadoVueloCambiado::dispatch($vueloId, 'aterrizado');
            
            $this->flashOk = 'Aterrizaje registrado correctamente.';
            $this->flashError = null;
        } catch (QueryException $e) {
            $this->flashError = $e->errorInfo[2] ?? 'No se pudo registrar el aterrizaje.';
            $this->flashOk = null;
        }
    }

    private function limpiarFlash(): void
    {
        $this->flashOk = null;
        $this->flashError = null;
    }

    public function render()
    {
        $fecha = Carbon::parse($this->fecha)->startOfDay();

        $query = Vuelo::with(['origen', 'destino', 'aeronave', 'piloto.empleado'])
            ->withCount('boletos')
            ->whereDate('salida_programada', $fecha);

        if ($this->tipo !== '') {
            $query->where('tipo', $this->tipo);
        }
        if ($this->aeronaveId !== '') {
            $query->where('aeronave_id', $this->aeronaveId);
        }
        if ($this->buscar !== '') {
            $query->where('numero_vuelo', 'like', "%{$this->buscar}%");
        }

        $vuelos = $query->orderBy('salida_programada')->get();

        $ocupaciones = $vuelos
            ->filter(fn ($v) => $v->aeronave && $v->aeronave->capacidad_pasajeros > 0)
            ->map(fn ($v) => $v->boletos_count / $v->aeronave->capacidad_pasajeros);

        $kpis = [
            'total'        => $vuelos->count(),
            'en_operacion' => $vuelos->whereIn('estado', ['abordando', 'en_vuelo'])->count(),
            'aterrizados'  => $vuelos->where('estado', 'aterrizado')->count(),
            'pasajeros'    => $vuelos->sum('boletos_count'),
            'ocupacion'    => $ocupaciones->count() ? (int) round($ocupaciones->avg() * 100) : 0,
        ];

        $orden = ['programado', 'retrasado', 'confirmado', 'abordando', 'en_vuelo', 'aterrizado', 'cancelado'];
        $porEstado = $vuelos->groupBy('estado');
        $columnas = collect($orden)
            ->filter(fn ($e) => $porEstado->has($e))
            ->mapWithKeys(fn ($e) => [$e => $porEstado->get($e)]);

        return view('livewire.operaciones-panel', [
            'fechaObj'           => $fecha,
            'kpis'               => $kpis,
            'columnas'           => $columnas,
            'aeronaves'          => Aeronave::orderBy('matricula')->get(),
            'flotaMantenimiento' => Aeronave::where('estado', 'mantenimiento')->count(),
            'flotaActiva'        => Aeronave::where('estado', 'activa')->count(),
        ]);
    }
}

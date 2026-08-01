<?php

namespace App\Livewire;

use App\Livewire\Concerns\CreaPersonas;
use App\Models\Boleto;
use App\Models\Pasajero;
use App\Models\Reserva;
use App\Models\Vuelo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class ReservaForm extends Component
{
    use CreaPersonas;

    public ?Reserva $reserva = null;
    public bool $isEdit = false;

    // Alta rápida de persona/pasajero (modal, sin salir de la reserva)
    public bool $mostrarPersona = false;
    public string $np_tipo_documento = 'CI';
    public string $np_numero_documento = '';
    public string $np_nombres = '';
    public string $np_apellidos = '';
    public string $np_nacionalidad = 'Boliviana';
    public string $np_peso_kg = '';
    public string $np_telefono = '';
    public string $np_email = '';

    public string $codigo = '';
    public string $vuelo_id = '';
    public string $estado = 'pendiente';
    public ?string $fecha_reserva = null;
    public string $notas = '';

    public bool $reservaBloqueada = false;
    public string $mensajeBloqueo = '';

    // Boletos dynamic array for CREATE only
    public array $boletos = [];

    // Pagos
    public string $pago_monto = '';
    public string $pago_metodo = 'efectivo';
    public string $pago_estado = 'pendiente';
    public string $pago_referencia = '';

    public function mount($reserva = null): void
    {
        if ($reserva && ! $reserva instanceof Reserva) {
            $reserva = Reserva::findOrFail($reserva);
        }
        if ($reserva && $reserva->exists) {
            $this->reserva = $reserva;
            $this->isEdit = true;
            $this->codigo = $reserva->codigo;
            $this->vuelo_id = (string) $reserva->vuelo_id;
            $this->estado = $reserva->estado;
            $this->fecha_reserva = $reserva->fecha_reserva ? \Carbon\Carbon::parse($reserva->fecha_reserva)->format('Y-m-d\TH:i') : null;
            $this->notas = $reserva->notas ?? '';
        } else {
            $this->codigo = strtoupper(\Illuminate\Support\Str::random(6));
            $this->fecha_reserva = now()->format('Y-m-d\TH:i');
            $this->agregarBoleto();
        }
    }

    public function agregarBoleto(): void
    {
        $precio = $this->precioDelVuelo();
        $this->boletos[] = ['pasajero_id' => '', 'carnet' => '', 'nombre' => '', 'asiento' => '', 'precio' => $precio > 0 ? (string) $precio : '0', 'equipaje_kg' => '0'];
    }

    // Buscador inline por carnet: al escribir el documento en una línea, resuelve la persona.
    public function updatedBoletos($value, $key): void
    {
        $parts = explode('.', (string) $key);
        if (count($parts) < 2 || $parts[1] !== 'carnet') {
            return;
        }
        $i = (int) $parts[0];
        $this->boletos[$i]['pasajero_id'] = '';
        $this->boletos[$i]['nombre'] = '';

        $doc = trim((string) $value);
        if ($doc === '') {
            return;
        }
        $pas = Pasajero::where('numero_documento', $doc)->first();
        if ($pas) {
            $this->boletos[$i]['pasajero_id'] = (string) $pas->id;
            $this->boletos[$i]['nombre'] = trim($pas->nombres . ' ' . $pas->apellidos);
            $precio = $this->precioDelVuelo();
            if ($precio > 0 && (float) ($this->boletos[$i]['precio'] ?? 0) <= 0) {
                $this->boletos[$i]['precio'] = (string) $precio;
            }
        }
    }

    public function abrirPersona(): void
    {
        $this->reset(['np_tipo_documento', 'np_numero_documento', 'np_nombres', 'np_apellidos', 'np_peso_kg', 'np_telefono', 'np_email']);
        $this->np_tipo_documento = 'CI';
        $this->np_nacionalidad = 'Boliviana';
        $this->resetValidation();
        $this->mostrarPersona = true;
    }

    // Registra una persona nueva y la coloca en la reserva (con su carnet).
    public function guardarPersona(): void
    {
        $this->validate([
            'np_tipo_documento'   => ['required', 'string', 'max:20'],
            'np_numero_documento' => ['required', 'string', 'max:30', Rule::unique('personas', 'numero_documento')],
            'np_nombres'          => ['required', 'string', 'max:100'],
            'np_apellidos'        => ['required', 'string', 'max:100'],
            'np_nacionalidad'     => ['nullable', 'string', 'max:50'],
            'np_peso_kg'          => ['nullable', 'numeric', 'min:0', 'max:500'],
            'np_telefono'         => ['nullable', 'string', 'max:30'],
            'np_email'            => ['nullable', 'email', 'max:120'],
        ]);

        [, $pasajeroId] = $this->crearPersonaRapida([
            'tipo_persona' => 'natural', 'tipo_documento' => $this->np_tipo_documento,
            'numero_documento' => $this->np_numero_documento, 'nombres' => $this->np_nombres,
            'apellidos' => $this->np_apellidos, 'nacionalidad' => $this->np_nacionalidad ?: 'Boliviana',
            'peso_kg' => $this->np_peso_kg ?: null, 'telefono' => $this->np_telefono ?: null,
            'email' => $this->np_email ?: null,
        ]);

        $this->colocarPasajero((int) $pasajeroId, $this->np_numero_documento, trim($this->np_nombres . ' ' . $this->np_apellidos));
        $this->mostrarPersona = false;
        session()->flash('success', 'Persona registrada y agregada a la reserva.');
    }

    // Coloca un pasajero en la primera línea vacía (o agrega una), con su carnet y nombre.
    protected function colocarPasajero(int $pasajeroId, string $carnet, string $nombre): void
    {
        $idx = null;
        foreach ($this->boletos as $i => $b) {
            if (empty($b['pasajero_id'])) { $idx = $i; break; }
        }
        if ($idx === null) {
            $this->agregarBoleto();
            $idx = count($this->boletos) - 1;
        }
        $this->boletos[$idx]['pasajero_id'] = (string) $pasajeroId;
        $this->boletos[$idx]['carnet'] = $carnet;
        $this->boletos[$idx]['nombre'] = $nombre;
        $precio = $this->precioDelVuelo();
        if ($precio > 0 && (float) ($this->boletos[$idx]['precio'] ?? 0) <= 0) {
            $this->boletos[$idx]['precio'] = (string) $precio;
        }
    }

    /** Precio del vuelo seleccionado (0 si no hay vuelo). */
    protected function precioDelVuelo(): float
    {
        return $this->vuelo_id ? (float) (Vuelo::whereKey($this->vuelo_id)->value('precio') ?? 0) : 0.0;
    }

    /** Al elegir vuelo, autocompleta el precio de los boletos que estén en 0. */
    public function updatedVueloId(): void
    {
        $precio = $this->precioDelVuelo();
        if ($precio <= 0) {
            return;
        }
        foreach ($this->boletos as $i => $b) {
            if ((float) ($b['precio'] ?? 0) <= 0) {
                $this->boletos[$i]['precio'] = (string) $precio;
            }
        }
    }

    public function removerBoleto(int $index): void
    {
        unset($this->boletos[$index]);
        $this->boletos = array_values($this->boletos);
    }

    protected function rules(): array
    {
        if ($this->isEdit) {
            return [
                'codigo' => ['required', 'string', 'max:10'],
                'vuelo_id' => ['required', 'exists:vuelos,id'],
                'estado' => ['required', 'in:pendiente,confirmada,cancelada,completada'],
                'fecha_reserva' => ['nullable', 'date'],
                'notas' => ['nullable', 'string', 'max:1000'],
            ];
        }

        return [
            'codigo' => ['required', 'string', 'max:10', 'unique:reservas,codigo'],
            'vuelo_id' => ['required', 'exists:vuelos,id'],
            'estado' => ['required', 'in:pendiente,confirmada,cancelada,completada'],
            'fecha_reserva' => ['nullable', 'date'],
            'notas' => ['nullable', 'string', 'max:1000'],
            'boletos' => ['required', 'array', 'min:1'],
            'boletos.*.pasajero_id' => ['required', 'exists:pasajeros,id'],
            'boletos.*.asiento' => ['nullable', 'string', 'max:5'],
            'boletos.*.precio' => ['required', 'numeric', 'min:0'],
            'boletos.*.equipaje_kg' => ['nullable', 'numeric', 'min:0'],
            'pago_monto' => ['nullable', 'numeric', 'min:0'],
            'pago_metodo' => ['nullable', 'in:efectivo,tarjeta_credito,tarjeta_debito,transferencia,pse,nequi'],
            'pago_estado' => ['nullable', 'in:pendiente,pagado,rechazado,reembolsado'],
            'pago_referencia' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function guardar()
    {
        $this->validate();
        $dt = fn ($c) => ! empty($c) ? \Carbon\Carbon::parse($c)->format('Y-m-d H:i:s') : null;

        try {
            if ($this->isEdit) {
                // Update basic info
                DB::statement('CALL sp_reservas_update(?,?,?,?,?,?,?,?,?)', [
                    $this->reserva->id, $this->codigo, $this->vuelo_id, $this->reserva->usuario_id,
                    $this->reserva->pasajero_id ?? null, $this->estado, $this->reserva->total ?? 0,
                    $dt($this->fecha_reserva), $this->notas ?: null,
                ]);
                return redirect()->route('reservas.index')->with('success', 'Reserva actualizada correctamente.');
            } else {
                // Create transaccional (Opción A: genera venta)
                $titular = $this->boletos[0]['pasajero_id'] ?? null;
                // Red de seguridad: si un boleto quedó en 0, usa el precio del vuelo (evita factura en 0)
                $precioVuelo = $this->precioDelVuelo();
                $boletosJson = json_encode(array_map(function ($b) use ($precioVuelo) {
                    $precio = (float) $b['precio'];
                    if ($precio <= 0 && $precioVuelo > 0) {
                        $precio = $precioVuelo;
                    }
                    return [
                        'pasajero_id' => (int) $b['pasajero_id'],
                        'asiento' => $b['asiento'] ?: null,
                        'precio' => $precio,
                        'equipaje_kg' => (float) ($b['equipaje_kg'] ?? 0),
                    ];
                }, $this->boletos));

                DB::statement('CALL sp_crear_reserva_completa(?,?,?,?,?,?,?,?,?,?,?,?, @rid)', [
                    $this->codigo, $this->vuelo_id, auth()->id(), $titular, $this->estado, $dt($this->fecha_reserva),
                    $this->notas ?: null, $boletosJson, $this->pago_monto ?: null, $this->pago_metodo ?: null,
                    $this->pago_estado ?: null, $this->pago_referencia ?: null,
                ]);
                $id = DB::selectOne('SELECT @rid AS id')->id;

                // La venta (ingreso) se genera SOLO si hubo pago. Sin pago, la reserva
                // queda pendiente de cobro y la venta se creará al registrar el cobro.
                $ventaGenerada = false;
                if ((float) $this->pago_monto > 0) {
                    try {
                        DB::statement('CALL sp_confirmar_reserva(?, ?, @vid)', [$id, auth()->id()]);
                        $ventaGenerada = true;
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning("No se pudo confirmar automáticamente la reserva #{$id}: " . $e->getMessage());
                    }
                }

                $mensaje = $ventaGenerada
                    ? 'Reserva creada y cobrada: la venta se generó correctamente.'
                    : 'Reserva creada (sin pago). La venta se generará cuando registres el cobro desde el detalle de la reserva.';

                return redirect()->route('reservas.show', $id)->with($ventaGenerada ? 'success' : 'warning', $mensaje);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Error: ' . ($e->errorInfo[2] ?? 'no se pudo guardar'));
            return;
        }
    }

    #[On('echo:vuelos,EstadoVueloCambiado')]
    public function vueloEstadoCambiado($event)
    {
        $vueloId = $event['vueloId'] ?? null;
        if (!$vueloId) return;

        if ($this->vuelo_id == $vueloId) {
            $this->reservaBloqueada = true;
            $this->mensajeBloqueo = '¡ATENCIÓN! El vuelo seleccionado acaba de ser despachado o cambió de estado. La reserva ha sido bloqueada.';
        }
    }

    /** Asigna/quita un asiento haciendo clic en el mapa. */
    public function elegirAsiento(string $codigo): void
    {
        $codigo = strtoupper($codigo);
        if (! $this->vuelo_id) {
            return;
        }
        // Ocupado por otra reserva -> no se puede.
        if (Boleto::where('vuelo_id', $this->vuelo_id)->whereRaw('UPPER(asiento) = ?', [$codigo])->exists()) {
            return;
        }
        // Ya seleccionado en esta reserva -> lo libera.
        foreach ($this->boletos as $i => $b) {
            if (strtoupper((string) ($b['asiento'] ?? '')) === $codigo) {
                $this->boletos[$i]['asiento'] = '';
                return;
            }
        }
        // Lo asigna al primer pasajero que no tenga asiento.
        foreach ($this->boletos as $i => $b) {
            if (empty($b['asiento'])) {
                $this->boletos[$i]['asiento'] = $codigo;
                return;
            }
        }
    }

    /** Construye el mapa de asientos del avión del vuelo elegido. */
    protected function mapaAsientos(): ?array
    {
        if (! $this->vuelo_id) {
            return null;
        }
        $vuelo = Vuelo::with('aeronave')->find($this->vuelo_id);
        $cap = (int) ($vuelo?->aeronave?->capacidad_pasajeros ?? 0);
        if ($cap <= 0) {
            return null;
        }

        $ocupados = Boleto::where('vuelo_id', $this->vuelo_id)->pluck('asiento')
            ->filter()->map(fn ($a) => strtoupper($a))->all();
        $seleccionados = collect($this->boletos)->pluck('asiento')
            ->filter()->map(fn ($a) => strtoupper($a))->all();

        $letras = ['A', 'B', 'C', 'D'];
        $porFila = 3;
        $filas = [];
        $n = 0;
        $fila = 1;
        while ($n < $cap) {
            $celdas = [];
            for ($i = 0; $i < $porFila && $n < $cap; $i++, $n++) {
                $codigo = $fila . $letras[$i];
                $estado = in_array($codigo, $ocupados) ? 'ocupado'
                    : (in_array($codigo, $seleccionados) ? 'seleccionado' : 'libre');
                $celdas[] = ['codigo' => $codigo, 'letra' => $letras[$i], 'estado' => $estado];
            }
            $filas[] = ['num' => $fila, 'celdas' => $celdas];
            $fila++;
        }

        return ['filas' => $filas, 'matricula' => $vuelo->aeronave->matricula, 'capacidad' => $cap];
    }

    public function render()
    {
        return view('livewire.reserva-form', [
            'vuelos' => Vuelo::with(['origen', 'destino'])->whereIn('estado', ['programado', 'retrasado', 'confirmado'])->orderByDesc('salida_programada')->get(),
            'pasajeros' => Pasajero::orderBy('apellidos')->get(),
            'mapa' => $this->mapaAsientos(),
        ]);
    }
}

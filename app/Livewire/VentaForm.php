<?php

namespace App\Livewire;

use App\Livewire\Concerns\CreaPersonas;
use App\Models\Boleto;
use App\Models\Persona;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class VentaForm extends Component
{
    use CreaPersonas;

    public string $persona_id = '';
    public string $cliente_carnet = '';
    public string $cliente_nombre = '';
    public string $estado = 'pagada';
    public string $metodo_pago = 'efectivo';
    public string $descuento = '0';
    public array $detalles = [];

    public bool $ventaBloqueada = false;
    public string $mensajeBloqueo = '';

    // Alta rápida de cliente (modal, sin salir de la venta)
    public bool $mostrarCliente = false;
    public string $nc_tipo_persona = 'natural';
    public string $nc_tipo_documento = 'CI';
    public string $nc_numero_documento = '';
    public string $nc_nombres = '';
    public string $nc_apellidos = '';
    public string $nc_razon_social = '';
    public string $nc_nit = '';
    public string $nc_telefono = '';
    public string $nc_email = '';

    // Buscador inline por carnet/NIT del cliente: trae el nombre al escribir el documento.
    public function updatedClienteCarnet(): void
    {
        $this->persona_id = '';
        $this->cliente_nombre = '';
        $doc = trim($this->cliente_carnet);
        if ($doc === '') {
            return;
        }
        $persona = Persona::where('numero_documento', $doc)->first();
        if ($persona) {
            $this->persona_id = (string) $persona->id;
            $this->cliente_nombre = (string) ($persona->nombre_completo ?: $doc);
        }
    }

    public function abrirCliente(): void
    {
        $this->reset(['nc_nombres', 'nc_apellidos', 'nc_razon_social', 'nc_nit', 'nc_telefono', 'nc_email']);
        $this->nc_tipo_persona = 'natural';
        $this->nc_tipo_documento = 'CI';
        $this->nc_numero_documento = $this->cliente_carnet; // prellena con lo que ya escribió
        $this->resetValidation();
        $this->mostrarCliente = true;
    }

    public function guardarCliente(): void
    {
        $reglas = [
            'nc_tipo_persona'     => ['required', 'in:natural,juridica'],
            'nc_tipo_documento'   => ['required', 'string', 'max:20'],
            'nc_numero_documento' => ['required', 'string', 'max:30', Rule::unique('personas', 'numero_documento')],
            'nc_telefono'         => ['nullable', 'string', 'max:30'],
            'nc_email'            => ['nullable', 'email', 'max:120'],
        ];
        if ($this->nc_tipo_persona === 'natural') {
            $reglas['nc_nombres'] = ['required', 'string', 'max:100'];
            $reglas['nc_apellidos'] = ['required', 'string', 'max:100'];
        } else {
            $reglas['nc_razon_social'] = ['required', 'string', 'max:200'];
        }
        $this->validate($reglas);

        [$personaId] = $this->crearPersonaRapida([
            'tipo_persona' => $this->nc_tipo_persona, 'tipo_documento' => $this->nc_tipo_documento,
            'numero_documento' => $this->nc_numero_documento, 'telefono' => $this->nc_telefono ?: null,
            'email' => $this->nc_email ?: null, 'nombres' => $this->nc_nombres, 'apellidos' => $this->nc_apellidos,
            'razon_social' => $this->nc_razon_social, 'nit' => $this->nc_nit ?: null,
        ]);

        $this->persona_id = (string) $personaId;
        $this->cliente_carnet = $this->nc_numero_documento;
        $this->cliente_nombre = trim($this->nc_tipo_persona === 'natural'
            ? ($this->nc_nombres . ' ' . $this->nc_apellidos)
            : $this->nc_razon_social);
        $this->mostrarCliente = false;
        session()->flash('success', 'Cliente creado y seleccionado.');
    }

    public function mount(): void
    {
        $this->agregarDetalle();
    }

    public function agregarDetalle(): void
    {
        $this->detalles[] = ['descripcion' => '', 'cantidad' => '1', 'precio_unitario' => '0'];
    }

    public function removerDetalle(int $index): void
    {
        unset($this->detalles[$index]);
        $this->detalles = array_values($this->detalles);
    }

    protected function rules(): array
    {
        return [
            'persona_id' => ['nullable', 'exists:personas,id'],
            'estado' => ['required', 'in:pendiente,pagada,anulada'],
            'metodo_pago' => ['nullable', 'in:efectivo,tarjeta,transferencia,otro'],
            'descuento' => ['required', 'numeric', 'min:0'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.descripcion' => ['required', 'string', 'max:255'],
            'detalles.*.cantidad' => ['required', 'integer', 'min:1'],
            'detalles.*.precio_unitario' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function guardar()
    {
        $this->validate();

        try {
            $venta = DB::transaction(function () {
                $numero = 'V-' . str_pad((string) ((Venta::max('id') ?? 0) + 1), 5, '0', STR_PAD_LEFT);
                $venta = Venta::create([
                    'numero' => $numero, 'persona_id' => $this->persona_id ?: null, 'usuario_id' => auth()->id(),
                    'fecha' => now(), 'estado' => $this->estado, 'metodo_pago' => $this->metodo_pago ?: null,
                    'descuento' => $this->descuento, 'subtotal' => 0, 'total' => 0,
                ]);

                $subtotal = 0;
                foreach ($this->detalles as $linea) {
                    $lineaSubtotal = $linea['cantidad'] * $linea['precio_unitario'];
                    $subtotal += $lineaSubtotal;
                    VentaDetalle::create([
                        'venta_id' => $venta->id,
                        'descripcion' => $linea['descripcion'], 'cantidad' => $linea['cantidad'],
                        'precio_unitario' => $linea['precio_unitario'], 'subtotal' => $lineaSubtotal,
                    ]);
                }
                $venta->subtotal = $subtotal;
                $venta->total = $subtotal - $this->descuento;
                $venta->save();
                return $venta;
            });

            return redirect()->route('ventas.show', $venta->id)->with('success', 'Venta registrada.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    #[On('echo:vuelos,EstadoVueloCambiado')]
    public function vueloEstadoCambiado($event)
    {
        $vueloId = $event['vueloId'] ?? null;
        if (!$vueloId) return;

        // Check if we have any selected boleto for this flight
        $boletosSeleccionados = array_filter(array_column($this->detalles, 'boleto_id'));
        if (empty($boletosSeleccionados)) return;

        $afectados = Boleto::whereIn('id', $boletosSeleccionados)->where('vuelo_id', $vueloId)->count();
        if ($afectados > 0) {
            $this->ventaBloqueada = true;
            $this->mensajeBloqueo = '¡ATENCIÓN! Un vuelo de los boletos seleccionados acaba de ser despachado. Por seguridad, esta venta ha sido bloqueada. Por favor, refresca tu carrito.';
        }
    }

    public function render()
    {
        return view('livewire.venta-form', [
            'personas' => Persona::with(['natural', 'juridica'])->get(),
        ]);
    }
}

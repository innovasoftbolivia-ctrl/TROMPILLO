<?php

namespace App\Livewire;

use App\Livewire\Concerns\CreaPersonas;
use App\Models\EnvioCarga;
use App\Models\Persona;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Vuelo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CargaForm extends Component
{
    use CreaPersonas;

    public ?EnvioCarga $envio = null;

    public string $guia = '';
    public string $vuelo_id = '';
    public string $remitente = '';
    public string $remitente_documento = '';
    public string $destinatario = '';
    public string $destinatario_documento = '';
    public string $descripcion = '';
    public string $peso_kg = '0';
    public string $valor_declarado = '0';
    public string $costo_envio = '0';
    public string $estado = 'registrado';

    // Alta rápida de remitente (modal, sin salir de la carga)
    public bool $mostrarRemitente = false;
    public string $nr_tipo_persona = 'juridica';
    public string $nr_tipo_documento = 'NIT';
    public string $nr_numero_documento = '';
    public string $nr_nombres = '';
    public string $nr_apellidos = '';
    public string $nr_razon_social = '';
    public string $nr_nit = '';
    public string $nr_telefono = '';
    public string $nr_email = '';

    /** Tarifa del flete por kilogramo y por kilómetro (Bs). */
    private const TARIFA_KG_KM = 0.008;

    /** El flete nunca supera esta fracción del precio del boleto (queda por debajo). */
    private const TOPE_FRACCION_BOLETO = 0.90;

    public function updatedVueloId(): void
    {
        $this->calcularCostoEnvio();
    }

    public function updatedPesoKg(): void
    {
        $this->calcularCostoEnvio();
    }

    /**
     * Costo del flete = peso (kg) × distancia de la ruta (km) × tarifa,
     * pero siempre por debajo del precio del boleto de ese vuelo.
     */
    private function calcularCostoEnvio(): void
    {
        $peso = (float) $this->peso_kg;

        if (! $this->vuelo_id || $peso <= 0) {
            return;
        }

        $vuelo = Vuelo::with(['ruta', 'origen', 'destino'])->find($this->vuelo_id);
        if (! $vuelo) {
            return;
        }

        // Distancia: de la ruta si existe; si no, por coordenadas origen/destino.
        $distancia = (float) ($vuelo->ruta->distancia_km ?? 0);
        if ($distancia <= 0) {
            $distancia = $this->distanciaEntre($vuelo->origen, $vuelo->destino);
        }
        if ($distancia <= 0) {
            return;
        }

        $costo = $peso * $distancia * self::TARIFA_KG_KM;

        // Tope: el flete debe ser MENOR que el precio del boleto del vuelo.
        $precioBoleto = (float) ($vuelo->precio ?? 0);
        if ($precioBoleto > 0) {
            $costo = min($costo, $precioBoleto * self::TOPE_FRACCION_BOLETO);
        }

        $this->costo_envio = (string) round($costo, 2);
    }

    /** Distancia Haversine (km) entre dos aeropuertos, por sus coordenadas. */
    private function distanciaEntre($a, $b): float
    {
        if (! $a || ! $b || $a->latitud === null || $b->latitud === null) {
            return 0;
        }

        $r = 6371;
        $dLat = deg2rad((float) $b->latitud - (float) $a->latitud);
        $dLon = deg2rad((float) $b->longitud - (float) $a->longitud);
        $h = sin($dLat / 2) ** 2
            + cos(deg2rad((float) $a->latitud)) * cos(deg2rad((float) $b->latitud)) * sin($dLon / 2) ** 2;

        return round(2 * $r * asin(min(1, sqrt($h))));
    }

    // Buscador inline: al escribir el documento del remitente, trae su nombre si existe.
    public function updatedRemitenteDocumento(): void
    {
        $doc = trim($this->remitente_documento);
        if ($doc === '') {
            return;
        }
        $persona = Persona::where('numero_documento', $doc)->first();
        if ($persona) {
            $this->remitente = (string) ($persona->nombre_completo ?: $this->remitente);
        }
    }

    public function abrirRemitente(): void
    {
        $this->reset(['nr_nombres', 'nr_apellidos', 'nr_razon_social', 'nr_nit', 'nr_telefono', 'nr_email']);
        $this->nr_tipo_persona = 'juridica';
        $this->nr_tipo_documento = 'NIT';
        $this->nr_numero_documento = $this->remitente_documento; // prellena con lo escrito
        $this->resetValidation();
        $this->mostrarRemitente = true;
    }

    public function guardarRemitente(): void
    {
        $reglas = [
            'nr_tipo_persona'     => ['required', 'in:natural,juridica'],
            'nr_tipo_documento'   => ['required', 'string', 'max:20'],
            'nr_numero_documento' => ['required', 'string', 'max:30', Rule::unique('personas', 'numero_documento')],
            'nr_telefono'         => ['nullable', 'string', 'max:30'],
            'nr_email'            => ['nullable', 'email', 'max:120'],
        ];
        if ($this->nr_tipo_persona === 'natural') {
            $reglas['nr_nombres'] = ['required', 'string', 'max:100'];
            $reglas['nr_apellidos'] = ['required', 'string', 'max:100'];
        } else {
            $reglas['nr_razon_social'] = ['required', 'string', 'max:200'];
        }
        $this->validate($reglas);

        $this->crearPersonaRapida([
            'tipo_persona' => $this->nr_tipo_persona, 'tipo_documento' => $this->nr_tipo_documento,
            'numero_documento' => $this->nr_numero_documento, 'telefono' => $this->nr_telefono ?: null,
            'email' => $this->nr_email ?: null, 'nombres' => $this->nr_nombres, 'apellidos' => $this->nr_apellidos,
            'razon_social' => $this->nr_razon_social, 'nit' => $this->nr_nit ?: null,
        ]);

        $this->remitente_documento = $this->nr_numero_documento;
        $this->remitente = trim($this->nr_tipo_persona === 'natural'
            ? ($this->nr_nombres . ' ' . $this->nr_apellidos)
            : $this->nr_razon_social);
        $this->mostrarRemitente = false;
        session()->flash('success', 'Remitente creado y cargado.');
    }

    /**
     * Genera la siguiente guía de carga (formato TRP-CG-####) tomando el mayor
     * correlativo existente y sumándole 1. Así no se escribe a mano.
     */
    private function proximaGuia(string $prefijo = 'TRP-CG-'): string
    {
        $max = EnvioCarga::where('guia', 'like', $prefijo . '%')
            ->get('guia')
            ->map(fn ($e) => (int) preg_replace('/\D/', '', str_replace($prefijo, '', $e->guia)))
            ->max();

        $siguiente = ($max ?? 1000) + 1;

        while (EnvioCarga::where('guia', $prefijo . str_pad((string) $siguiente, 4, '0', STR_PAD_LEFT))->exists()) {
            $siguiente++;
        }

        return $prefijo . str_pad((string) $siguiente, 4, '0', STR_PAD_LEFT);
    }

    public function mount($carga = null): void
    {
        if ($carga) {
            $envio = EnvioCarga::findOrFail($carga);
            $this->envio = $envio;
            $this->guia = $envio->guia ?? '';
            $this->vuelo_id = (string) ($envio->vuelo_id ?? '');
            $this->remitente = $envio->remitente ?? '';
            $this->remitente_documento = $envio->remitente_documento ?? '';
            $this->destinatario = $envio->destinatario ?? '';
            $this->destinatario_documento = $envio->destinatario_documento ?? '';
            $this->descripcion = $envio->descripcion ?? '';
            $this->peso_kg = (string) ($envio->peso_kg ?? 0);
            $this->valor_declarado = (string) ($envio->valor_declarado ?? 0);
            $this->costo_envio = (string) ($envio->costo_envio ?? 0);
            $this->estado = $envio->estado ?? 'registrado';
        } else {
            // Nuevo envío: la guía se asigna automáticamente.
            $this->guia = $this->proximaGuia();
        }
    }

    public function rules(): array
    {
        return [
            'guia' => ['required', 'string', 'max:30'],
            'vuelo_id' => ['nullable', 'exists:vuelos,id'],
            'remitente' => ['required', 'string', 'max:120'],
            'remitente_documento' => ['nullable', 'string', 'max:30'],
            'destinatario' => ['required', 'string', 'max:120'],
            'destinatario_documento' => ['nullable', 'string', 'max:30'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'peso_kg' => ['required', 'numeric', 'min:0'],
            'valor_declarado' => ['required', 'numeric', 'min:0'],
            'costo_envio' => ['required', 'numeric', 'min:0'],
            'estado' => ['required', 'in:registrado,en_transito,entregado,devuelto'],
        ];
    }

    public function guardar()
    {
        // La guía es automática: si es un envío nuevo sin guía, la generamos.
        if (! $this->envio && trim($this->guia) === '') {
            $this->guia = $this->proximaGuia();
        }

        $this->validate();
        try {
            DB::transaction(function () {
                if ($this->envio) {
                    DB::statement('CALL sp_envios_carga_update(?,?,?,?,?,?,?,?,?,?,?,?)', [
                        $this->envio->id, $this->guia, $this->vuelo_id ?: null, $this->remitente,
                        $this->remitente_documento ?: null, $this->destinatario, $this->destinatario_documento ?: null,
                        $this->descripcion ?: null, $this->peso_kg, $this->valor_declarado, $this->costo_envio, $this->estado,
                    ]);
                    $envioId = $this->envio->id;
                } else {
                    DB::statement('CALL sp_envios_carga_insert(?,?,?,?,?,?,?,?,?,?,?)', [
                        $this->guia, $this->vuelo_id ?: null, $this->remitente, $this->remitente_documento ?: null,
                        $this->destinatario, $this->destinatario_documento ?: null, $this->descripcion ?: null,
                        $this->peso_kg, $this->valor_declarado, $this->costo_envio, $this->estado,
                    ]);
                    $envioId = (int) DB::selectOne('SELECT LAST_INSERT_ID() AS id')->id;
                }
                $this->generarOActualizarVentaCarga($envioId);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Error: ' . ($e->errorInfo[2] ?? 'no se pudo guardar'));
            return;
        }
        return redirect()->route('carga.index')
            ->with('success', $this->envio ? 'Envío actualizado.' : 'Envío creado.');
    }

    /**
     * Genera (o actualiza) la venta del flete: el costo_envio se registra como
     * ingreso, igual que las reservas. Cliente = remitente si existe como persona.
     */
    protected function generarOActualizarVentaCarga(int $envioId): void
    {
        $costo = (float) $this->costo_envio;
        if ($costo <= 0) {
            return; // sin flete, no se crea venta
        }

        $personaId = $this->remitente_documento
            ? Persona::where('numero_documento', $this->remitente_documento)->value('id')
            : null;
        $descripcion = 'Flete carga guía ' . $this->guia . ' (' . $this->remitente . ' → ' . $this->destinatario . ')';

        $detalle = VentaDetalle::where('envio_carga_id', $envioId)->first();

        if ($detalle) {
            $detalle->update([
                'descripcion' => $descripcion, 'cantidad' => 1,
                'precio_unitario' => $costo, 'subtotal' => $costo,
            ]);
            if ($detalle->venta) {
                $detalle->venta->update([
                    'subtotal'   => $costo,
                    'total'      => $costo - (float) $detalle->venta->descuento,
                    'persona_id' => $personaId,
                ]);
            }
            return;
        }

        $numero = 'V-' . str_pad((string) ((Venta::max('id') ?? 0) + 1), 5, '0', STR_PAD_LEFT);
        $venta = Venta::create([
            'numero' => $numero, 'persona_id' => $personaId, 'usuario_id' => auth()->id(),
            'fecha' => now(), 'subtotal' => $costo, 'descuento' => 0, 'total' => $costo,
            'estado' => 'pagada', 'metodo_pago' => null,
        ]);
        VentaDetalle::create([
            'venta_id' => $venta->id, 'envio_carga_id' => $envioId, 'boleto_id' => null,
            'descripcion' => $descripcion, 'cantidad' => 1,
            'precio_unitario' => $costo, 'subtotal' => $costo,
        ]);
    }

    public function render()
    {
        return view('livewire.carga-form', [
            'vuelos' => Vuelo::with(['origen', 'destino'])->orderByDesc('salida_programada')->get(),
        ]);
    }
}

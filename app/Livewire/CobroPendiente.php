<?php

namespace App\Livewire;

use App\Models\Factura;
use App\Models\Pago;
use App\Models\Reserva;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CobroPendiente extends Component
{
    /** Carnet (número de documento) del cliente a buscar. */
    public string $carnet = '';

    /** Método de pago con el que se cobrará. */
    public string $metodo = 'efectivo';

    /** Se marca true después de una búsqueda para saber si mostrar "sin resultados". */
    public bool $busco = false;

    public ?string $flashError = null;

    /**
     * Reservas con cobro pendiente del cliente: no canceladas y sin pagar
     * (sin venta todavía, o con la venta en estado pendiente).
     */
    private function pendientes()
    {
        $carnet = trim($this->carnet);

        if ($carnet === '') {
            return collect();
        }

        return Reserva::query()
            ->whereHas('titular', fn ($q) => $q->where('numero_documento', $carnet))
            ->where('estado', '!=', 'cancelada')
            ->where(function ($q) {
                $q->whereDoesntHave('venta')
                    ->orWhereHas('venta', fn ($v) => $v->where('estado', 'pendiente'));
            })
            ->with(['vuelo.origen', 'vuelo.destino', 'titular', 'boletos', 'venta'])
            ->orderByDesc('fecha_reserva')
            ->get();
    }

    public function buscar(): void
    {
        $this->busco = true;
        $this->flashError = null;
    }

    /** Monto a cobrar por una reserva (suma de boletos, o total, o precio del vuelo). */
    private function montoReserva(Reserva $reserva): float
    {
        $monto = (float) $reserva->boletos->sum('precio');

        if ($monto <= 0) {
            $monto = (float) $reserva->total;
        }
        if ($monto <= 0) {
            $monto = (float) ($reserva->vuelo->precio ?? 0);
        }

        return round($monto, 2);
    }

    /**
     * Cobra la reserva, genera la venta (SP), emite la factura y lleva a la
     * venta, donde se pueden imprimir factura y boleto.
     */
    public function cobrarYFacturar(int $reservaId)
    {
        $reserva = Reserva::with(['boletos', 'vuelo', 'venta'])->find($reservaId);

        if (! $reserva) {
            $this->flashError = 'La reserva ya no existe.';
            return;
        }
        if ($reserva->estado === 'cancelada') {
            $this->flashError = 'No se puede cobrar una reserva cancelada.';
            return;
        }
        // Evita cobrar dos veces: si ya tiene venta pagada, llevamos directo a ella.
        if ($reserva->venta && $reserva->venta->estado === 'pagada') {
            session()->flash('success', 'Esta reserva ya estaba cobrada. Podés imprimir su factura y boleto.');
            return $this->redirect(route('ventas.show', $reserva->venta->id), navigate: true);
        }

        $monto = $this->montoReserva($reserva);

        if ($monto <= 0) {
            $this->flashError = 'La reserva no tiene un monto a cobrar.';
            return;
        }

        try {
            // 1) Registrar el pago asociado a la reserva.
            Pago::create([
                'reserva_id' => $reserva->id,
                'monto'      => $monto,
                'metodo'     => $this->metodo,
                'estado'     => 'pagado',
                'fecha_pago' => now(),
            ]);

            // 2) Confirmar la reserva → genera venta, boletos y la marca pagada.
            //    El SP maneja su propia transacción, por eso no lo envolvemos.
            DB::statement('CALL sp_confirmar_reserva(?, ?, @vid)', [$reserva->id, auth()->id()]);
            $ventaId = DB::selectOne('SELECT @vid AS id')->id;

            // 3) Emitir la factura de esa venta (si aún no tiene).
            $this->emitirFactura((int) $ventaId);
        } catch (\Illuminate\Database\QueryException $e) {
            $this->flashError = 'No se pudo procesar el cobro: ' . ($e->errorInfo[2] ?? 'error');
            return;
        }

        session()->flash('success', 'Cobro procesado, venta y factura generadas. Ya podés imprimir la factura y el boleto.');

        return $this->redirect(route('ventas.show', $ventaId), navigate: true);
    }

    /** Crea la factura de la venta si todavía no existe. */
    private function emitirFactura(int $ventaId): void
    {
        $venta = \App\Models\Venta::with('cliente')->find($ventaId);

        if (! $venta || $venta->factura()->exists()) {
            return;
        }

        $subtotal = round($venta->total / 1.13, 2);
        $iva      = round($venta->total - $subtotal, 2);
        $numero   = 'F-' . str_pad((string) ((Factura::max('id') ?? 0) + 1), 6, '0', STR_PAD_LEFT);

        Factura::create([
            'numero_factura' => $numero,
            'venta_id'       => $venta->id,
            'persona_id'     => $venta->persona_id,
            'nit'            => $venta->cliente?->numero_documento,
            'razon_social'   => $venta->cliente?->nombre_completo,
            'fecha_emision'  => now(),
            'subtotal'       => $subtotal,
            'impuesto_iva'   => $iva,
            'total'          => $venta->total,
            'estado'         => 'emitida',
        ]);
    }

    public function render()
    {
        $reservas = $this->busco ? $this->pendientes() : collect();

        // Adjuntamos el monto calculado para mostrarlo en la vista.
        $reservas->each(fn ($r) => $r->monto_cobrar = $this->montoReserva($r));

        return view('livewire.cobro-pendiente', ['reservas' => $reservas]);
    }
}

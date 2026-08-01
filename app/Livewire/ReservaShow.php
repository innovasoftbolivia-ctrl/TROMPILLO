<?php

namespace App\Livewire;

use App\Models\Boleto;
use App\Models\Pago;
use App\Models\Reserva;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ReservaShow extends Component
{
    public Reserva $reserva;

    public string $cobroMonto = '';
    public string $cobroMetodo = 'efectivo';

    public function mount(Reserva $reserva): void
    {
        $reserva->load(['vuelo.origen', 'vuelo.destino', 'titular', 'boletos.pasajero', 'venta']);
        $this->reserva = $reserva;
        $this->cobroMonto = (string) ($reserva->total ?? 0);
    }

    /**
     * Registra el cobro de la reserva y, con ello, genera la venta (Opción A):
     * el ingreso nace del pago, no de la mera reserva.
     */
    public function cobrar()
    {
        if ((float) $this->cobroMonto <= 0) {
            session()->flash('error', 'El monto del cobro debe ser mayor a 0.');
            return;
        }

        try {
            Pago::create([
                'reserva_id' => $this->reserva->id,
                'monto'      => $this->cobroMonto,
                'metodo'     => $this->cobroMetodo,
                'estado'     => 'pagado',
                'fecha_pago' => now(),
            ]);

            DB::statement('CALL sp_confirmar_reserva(?, ?, @vid)', [$this->reserva->id, auth()->id()]);
            $vid = DB::selectOne('SELECT @vid AS id')->id;
            session()->flash('success', 'Cobro registrado y venta generada correctamente.');

            return $this->redirect(route('ventas.show', $vid), navigate: true);
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'No se pudo registrar el cobro: ' . ($e->errorInfo[2] ?? 'error'));
        }
    }

    public function checkinBoleto(int $boletoId): void
    {
        try {
            DB::statement('CALL sp_checkin_boleto(?)', [$boletoId]);
            session()->flash('success', 'Check-in realizado correctamente.');
            $this->reserva->refresh();
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', "Check-in rechazado: " . ($e->errorInfo[2] ?? 'error'));
        }
    }

    public function render() { return view('livewire.reserva-show'); }
}

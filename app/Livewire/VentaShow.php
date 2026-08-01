<?php

namespace App\Livewire;

use App\Models\Factura;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class VentaShow extends Component
{
    public Venta $venta;

    public function mount(Venta $venta): void
    {
        $venta->load(['cliente.natural', 'cliente.juridica', 'vendedor', 'detalles.boleto', 'factura', 'pagos', 'reserva']);
        $this->venta = $venta;
    }

    public function generarFactura(): void
    {
        if ($this->venta->factura) { session()->flash('error', 'La venta ya tiene factura.'); return; }

        try {
            DB::transaction(function () {
                $subtotal = round($this->venta->total / 1.13, 2);
                $iva = round($this->venta->total - $subtotal, 2);
                $numeroFactura = 'F-' . str_pad((string) ((Factura::max('id') ?? 0) + 1), 6, '0', STR_PAD_LEFT);
                $factura = Factura::create([
                    'numero_factura' => $numeroFactura, 'venta_id' => $this->venta->id,
                    'persona_id' => $this->venta->persona_id,
                    'nit' => $this->venta->cliente?->numero_documento,
                    'razon_social' => $this->venta->cliente?->nombre_completo,
                    'fecha_emision' => now(), 'subtotal' => $subtotal, 'impuesto_iva' => $iva,
                    'total' => $this->venta->total, 'estado' => 'emitida',
                ]);
                $this->redirect(route('facturas.show', $factura->id), navigate: true);
            });
        } catch (\Exception $e) {
            session()->flash('error', 'Error al facturar: ' . $e->getMessage());
        }
    }

    public function render() { return view('livewire.venta-show'); }
}

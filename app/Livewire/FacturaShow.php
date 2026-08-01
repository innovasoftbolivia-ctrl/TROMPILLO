<?php

namespace App\Livewire;

use App\Models\Factura;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class FacturaShow extends Component
{
    public Factura $factura;

    public function mount(Factura $factura): void
    {
        $factura->load(['venta.detalles.boleto', 'persona.natural', 'persona.juridica']);
        $this->factura = $factura;
    }

    public function anular(): void
    {
        if ($this->factura->estado === 'anulada') { session()->flash('error', 'La factura ya está anulada.'); return; }
        $this->factura->update(['estado' => 'anulada']);
        session()->flash('success', "Factura {$this->factura->numero_factura} anulada.");
    }

    public function render() { return view('livewire.factura-show'); }
}

<?php

namespace App\Livewire;

use App\Models\Factura;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class FacturaIndex extends Component
{
    use WithPagination;

    #[Url] public string $buscar = '';
    #[Url] public string $estado = '';
    public ?string $flashOk = null;
    public ?string $flashError = null;

    public function updatingBuscar(): void { $this->resetPage(); }
    public function updatingEstado(): void { $this->resetPage(); }
    public function limpiarFiltros(): void { $this->reset(['buscar', 'estado']); $this->resetPage(); }

    public function anular(int $id): void
    {
        try {
            $factura = Factura::findOrFail($id);
            if ($factura->estado === 'anulada') { $this->flashError = 'La factura ya está anulada.'; return; }
            $factura->update(['estado' => 'anulada']);
            $this->flashOk = "Factura {$factura->numero_factura} anulada.";
            $this->flashError = null;
        } catch (\Exception $e) {
            $this->flashError = 'No se pudo anular: ' . $e->getMessage();
            $this->flashOk = null;
        }
    }

    public function render()
    {
        $query = Factura::with(['venta.cliente.natural', 'venta.cliente.juridica']);
        if ($this->estado !== '') $query->where('estado', $this->estado);
        if ($this->buscar !== '') $query->where('numero_factura', 'like', "%{$this->buscar}%")->orWhere('nit', 'like', "%{$this->buscar}%")->orWhere('razon_social', 'like', "%{$this->buscar}%");
        
        return view('livewire.factura-index', ['facturas' => $query->orderByDesc('fecha_emision')->paginate(10)]);
    }
}

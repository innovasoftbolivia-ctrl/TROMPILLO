<?php

namespace App\Livewire;

use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class VentaIndex extends Component
{
    use WithPagination;

    #[Url] public string $buscar = '';
    #[Url] public string $estado = '';
    public ?string $flashOk = null;
    public ?string $flashError = null;

    public function updatingBuscar(): void { $this->resetPage(); }
    public function updatingEstado(): void { $this->resetPage(); }
    public function limpiarFiltros(): void { $this->reset(['buscar', 'estado']); $this->resetPage(); }

    public function eliminar(int $id): void
    {
        try { Venta::findOrFail($id)->delete(); $this->flashOk = 'Venta eliminada.'; $this->flashError = null; }
        catch (\Exception $e) { $this->flashError = 'No se pudo eliminar: ' . $e->getMessage(); $this->flashOk = null; }
    }

    public function render()
    {
        $query = Venta::with(['cliente.natural', 'cliente.juridica', 'vendedor', 'factura'])->withCount('detalles');
        if ($this->estado !== '') $query->where('estado', $this->estado);
        if ($this->buscar !== '') $query->where('numero', 'like', "%{$this->buscar}%");
        
        return view('livewire.venta-index', ['ventas' => $query->orderByDesc('fecha')->paginate(10)]);
    }
}

<?php

namespace App\Livewire;

use App\Support\Reportes;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class ReportesIndex extends Component
{
    #[Url] public string $tipo = 'ventas';
    #[Url] public string $desde = '';
    #[Url] public string $hasta = '';
    #[Url] public string $estado = '';

    public function mount(): void
    {
        if ($this->desde === '') $this->desde = today()->startOfMonth()->toDateString();
        if ($this->hasta === '') $this->hasta = today()->toDateString();
    }

    public function seleccionar(string $tipo): void
    {
        $this->tipo = $tipo;
        $this->estado = '';
    }

    public function render()
    {
        $data = Reportes::build($this->tipo, $this->desde, $this->hasta, $this->estado);

        return view('livewire.reportes-index', [
            'data'    => $data,
            'estados' => Reportes::estados($this->tipo),
        ]);
    }
}

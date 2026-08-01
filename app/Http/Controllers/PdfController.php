<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Models\Factura;
use App\Support\Reportes;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PdfController extends Controller
{
    /** Reporte de Ventas en PDF. */
    public function reporteVentas(Request $request)
    {
        return $this->reporte($request, 'ventas', 'Reporte_Ventas');
    }

    /** Reporte de Reservas en PDF. */
    public function reporteReservas(Request $request)
    {
        return $this->reporte($request, 'reservas', 'Reporte_Reservas');
    }

    /** Reporte de Vuelos en PDF. */
    public function reporteVuelos(Request $request)
    {
        return $this->reporte($request, 'vuelos', 'Reporte_Vuelos');
    }

    private function reporte(Request $request, string $tipo, string $archivo)
    {
        Gate::authorize('reportes.ver');

        $desde  = $request->get('desde', now()->startOfMonth()->toDateString());
        $hasta  = $request->get('hasta', now()->toDateString());
        $estado = (string) $request->get('estado', '');

        $data = Reportes::build($tipo, $desde, $hasta, $estado);

        $pdf = Pdf::loadView('pdf.reporte', $data)->setPaper('letter', 'landscape');

        return $pdf->stream($archivo . '.pdf');
    }

    public function imprimirBoleto(Boleto $boleto)
    {
        Gate::authorize('reservas.gestionar');
        
        $boleto->load(['vuelo.origen', 'vuelo.destino', 'pasajero', 'vuelo.aeronave']);
        
        $pdf = Pdf::loadView('pdf.boleto', compact('boleto'));
        
        // Formato vertical tipo boarding pass
        return $pdf->stream('Boleto_' . $boleto->id . '.pdf');
    }

    public function imprimirFactura(Factura $factura)
    {
        Gate::authorize('reservas.gestionar');
        
        $factura->load(['venta.reserva', 'venta.cliente', 'usuario']);
        
        $pdf = Pdf::loadView('pdf.factura', compact('factura'));
        
        return $pdf->stream('Factura_' . $factura->numero . '.pdf');
    }
}

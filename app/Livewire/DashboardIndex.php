<?php

namespace App\Livewire;

use Illuminate\Support\Carbon;
use App\Models\Aeronave;
use App\Models\Factura;
use App\Models\Pago;
use App\Models\Pasajero;
use App\Models\Reserva;
use App\Models\Venta;
use App\Models\Vuelo;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DashboardIndex extends Component
{
    public function render()
    {
        $hoy = Carbon::today();

        // Una sola consulta agrupada por estado (en vez de varios COUNT sueltos)
        $porEstado = Vuelo::selectRaw('estado, COUNT(*) as total')->groupBy('estado')->pluck('total', 'estado');
        $flotaPorEstado = Aeronave::selectRaw('estado, COUNT(*) as total')->groupBy('estado')->pluck('total', 'estado');
        // COUNT + SUM por estado de ventas en una sola consulta; el resto se deriva de aquí
        $ventasPorEstado = Venta::selectRaw('estado, COUNT(*) c, SUM(total) monto')
            ->groupBy('estado')->get()->keyBy('estado');
        $vpe = fn ($estado, $campo) => $ventasPorEstado[$estado]?->{$campo} ?? 0;
        $vuelosActivos = ['programado', 'confirmado', 'abordando', 'en_vuelo'];

        $stats = [
            'vuelos_total'       => (int) $porEstado->sum(),
            'vuelos_activos'     => (int) collect($vuelosActivos)->sum(fn ($e) => $porEstado[$e] ?? 0),
            'flota_activa'       => (int) ($flotaPorEstado['activa'] ?? 0),
            'flota_total'        => (int) $flotaPorEstado->sum(),
            'flota_mantenimiento'=> (int) ($flotaPorEstado['mantenimiento'] ?? 0),
            'pasajeros'          => Pasajero::count(),
            'reservas'           => Reserva::count(),
            'ingresos'           => (float) Pago::where('estado', 'pagado')->sum('monto'),
        ];

        $proximosVuelos = Vuelo::with(['origen', 'destino', 'aeronave', 'piloto.empleado'])
            ->orderBy('salida_programada')->limit(6)->get();

        $ventas = [
            'total'            => (int) $ventasPorEstado->sum('c'),
            'pagadas'          => (int) $vpe('pagada', 'c'),
            'ingresos'         => (float) $vpe('pagada', 'monto'),
            'pendientes'       => (int) $vpe('pendiente', 'c'),
            'pendientes_monto' => (float) $vpe('pendiente', 'monto'),
            'mes_monto'        => (float) Venta::where('estado', 'pagada')
                                    ->whereYear('fecha', $hoy->year)->whereMonth('fecha', $hoy->month)->sum('total'),
            'ticket_prom'      => (float) (Venta::where('estado', 'pagada')->avg('total') ?? 0),
            'facturas'         => Factura::where('estado', 'emitida')->count(),
            'iva'              => (float) Factura::where('estado', 'emitida')->sum('impuesto_iva'),
        ];

        // Tendencia: ventas pagadas por día (últimos 14 días)
        $desde = $hoy->copy()->subDays(13);
        $porDiaRaw = Venta::where('estado', 'pagada')
            ->whereDate('fecha', '>=', $desde)
            ->selectRaw('DATE(fecha) as d, SUM(total) as monto')
            ->groupBy('d')->pluck('monto', 'd');
        
        $ventasPorDia = [];
        for ($i = 0; $i < 14; $i++) {
            $dia = $desde->copy()->addDays($i);
            $ventasPorDia[] = ['fecha' => $dia, 'monto' => (float) ($porDiaRaw[$dia->toDateString()] ?? 0)];
        }

        $topVendedores = Venta::where('estado', 'pagada')
            ->selectRaw('usuario_id, COUNT(*) c, SUM(total) monto')
            ->groupBy('usuario_id')->orderByDesc('monto')->with('vendedor')->limit(5)->get();

        $ultimasVentas = Venta::with(['cliente.natural', 'cliente.juridica'])->orderByDesc('fecha')->limit(6)->get();

        return view('livewire.dashboard-index', compact(
            'stats', 'porEstado', 'proximosVuelos',
            'ventas', 'ventasPorDia', 'ventasPorEstado', 'topVendedores', 'ultimasVentas'
        ));
    }
}

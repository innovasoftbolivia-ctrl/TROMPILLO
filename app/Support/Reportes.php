<?php

namespace App\Support;

use App\Models\Boleto;
use App\Models\Reserva;
use App\Models\Venta;
use App\Models\Vuelo;
use Illuminate\Support\Carbon;

/**
 * Construye los datos de los reportes (Ventas, Reservas, Vuelos) para mostrarlos
 * en pantalla y para exportarlos a PDF, reutilizando la misma consulta.
 */
class Reportes
{
    public static function build(string $tipo, string $desde, string $hasta, string $estado = ''): array
    {
        $d = Carbon::parse($desde)->startOfDay();
        $h = Carbon::parse($hasta)->endOfDay();

        $data = match ($tipo) {
            'reservas' => self::reservas($d, $h, $estado),
            'vuelos'   => self::vuelos($d, $h, $estado),
            default    => self::ventas($d, $h, $estado),
        };
        $data['periodo'] = $d->format('d/m/Y') . ' al ' . $h->format('d/m/Y');

        return $data;
    }

    protected static function ventas(Carbon $d, Carbon $h, string $estado): array
    {
        $q = Venta::with('cliente')->whereBetween('fecha', [$d, $h]);
        if ($estado !== '') $q->where('estado', $estado);
        $ventas = $q->orderBy('fecha')->get();

        return [
            'titulo'   => 'Reporte de Ventas',
            'columnas' => ['N° Venta', 'Fecha', 'Cliente', 'Estado', 'Método', 'Total (Bs)'],
            'align'    => ['left', 'left', 'left', 'left', 'left', 'right'],
            'filas'    => $ventas->map(fn ($v) => [
                $v->numero,
                optional($v->fecha)->format('d/m/Y'),
                $v->cliente?->nombre_completo ?: 'Cliente casual',
                ucfirst($v->estado),
                $v->metodo_pago ? ucfirst($v->metodo_pago) : '—',
                number_format((float) $v->total, 2),
            ])->all(),
            'totales'  => [
                ['label' => 'Cantidad de ventas', 'valor' => $ventas->count()],
                ['label' => 'Total pagadas', 'valor' => 'Bs ' . number_format((float) $ventas->where('estado', 'pagada')->sum('total'), 2)],
                ['label' => 'Total general', 'valor' => 'Bs ' . number_format((float) $ventas->sum('total'), 2)],
            ],
        ];
    }

    protected static function reservas(Carbon $d, Carbon $h, string $estado): array
    {
        $q = Reserva::with(['vuelo', 'titular'])->whereBetween('fecha_reserva', [$d, $h]);
        if ($estado !== '') $q->where('estado', $estado);
        $reservas = $q->orderBy('fecha_reserva')->get();

        return [
            'titulo'   => 'Reporte de Reservas',
            'columnas' => ['Código', 'Fecha', 'Vuelo', 'Titular', 'Estado', 'Total (Bs)'],
            'align'    => ['left', 'left', 'left', 'left', 'left', 'right'],
            'filas'    => $reservas->map(fn ($r) => [
                $r->codigo,
                optional($r->fecha_reserva)->format('d/m/Y'),
                $r->vuelo?->numero_vuelo ?: 'Charter',
                $r->titular?->nombre_completo ?: '—',
                ucfirst($r->estado),
                number_format((float) $r->total, 2),
            ])->all(),
            'totales'  => [
                ['label' => 'Cantidad de reservas', 'valor' => $reservas->count()],
                ['label' => 'Monto total', 'valor' => 'Bs ' . number_format((float) $reservas->sum('total'), 2)],
            ],
        ];
    }

    protected static function vuelos(Carbon $d, Carbon $h, string $estado): array
    {
        $q = Vuelo::with(['origen', 'destino', 'aeronave'])->withCount('boletos')
            ->whereBetween('salida_programada', [$d, $h]);
        if ($estado !== '') $q->where('estado', $estado);
        $vuelos = $q->orderBy('salida_programada')->get();

        $ingresos = (float) Boleto::whereIn('vuelo_id', $vuelos->pluck('id'))->sum('precio');

        return [
            'titulo'   => 'Reporte de Vuelos',
            'columnas' => ['N° Vuelo', 'Salida', 'Ruta', 'Aeronave', 'Estado', 'Pasajeros', 'Precio (Bs)'],
            'align'    => ['left', 'left', 'left', 'left', 'left', 'right', 'right'],
            'filas'    => $vuelos->map(fn ($v) => [
                $v->numero_vuelo ?: '—',
                optional($v->salida_programada)->format('d/m/Y H:i'),
                ($v->origen?->codigo_oaci ?? '?') . ' → ' . ($v->destino?->codigo_oaci ?? '?'),
                $v->aeronave?->matricula ?: '—',
                ucfirst(str_replace('_', ' ', $v->estado)),
                (string) $v->boletos_count,
                number_format((float) $v->precio, 2),
            ])->all(),
            'totales'  => [
                ['label' => 'Cantidad de vuelos', 'valor' => $vuelos->count()],
                ['label' => 'Pasajeros (boletos)', 'valor' => (int) $vuelos->sum('boletos_count')],
                ['label' => 'Ingresos por boletos', 'valor' => 'Bs ' . number_format($ingresos, 2)],
            ],
        ];
    }

    /** Estados disponibles según el tipo de reporte (para el filtro). */
    public static function estados(string $tipo): array
    {
        return match ($tipo) {
            'reservas' => ['pendiente', 'confirmada', 'cancelada', 'completada'],
            'vuelos'   => ['programado', 'confirmado', 'abordando', 'en_vuelo', 'aterrizado', 'cancelado', 'retrasado'],
            default    => ['pendiente', 'pagada', 'anulada'],
        };
    }
}

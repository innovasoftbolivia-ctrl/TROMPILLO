<?php

namespace Database\Seeders;

use App\Models\Empleado;
use App\Models\Factura;
use App\Models\Pasajero;
use App\Models\Persona;
use App\Models\PersonaNatural;
use App\Models\Reserva;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PersonasVentasSeeder extends Seeder
{
    public function run(): void
    {
        $this->personas();
        $this->ventasYFacturas();
    }

    /**
     * Crea una persona natural por cada pasajero y empleado (deduplicando por
     * documento) y rellena persona_id.
     */
    private function personas(): void
    {
        foreach (Pasajero::whereNull('persona_id')->get() as $p) {
            $persona = Persona::firstOrCreate(
                ['tipo_documento' => $p->tipo_documento, 'numero_documento' => $p->numero_documento],
                ['tipo_persona' => 'natural', 'telefono' => $p->telefono, 'email' => $p->email, 'pais_id' => $p->pais_id]
            );
            PersonaNatural::firstOrCreate(
                ['persona_id' => $persona->id],
                ['nombres' => $p->nombres, 'apellidos' => $p->apellidos, 'fecha_nacimiento' => $p->fecha_nacimiento]
            );
            $p->persona_id = $persona->id;
            $p->save();
        }

        foreach (Empleado::whereNull('persona_id')->get() as $e) {
            $persona = Persona::firstOrCreate(
                ['tipo_documento' => $e->tipo_documento, 'numero_documento' => $e->numero_documento],
                ['tipo_persona' => 'natural', 'telefono' => $e->telefono, 'email' => $e->email]
            );
            PersonaNatural::firstOrCreate(
                ['persona_id' => $persona->id],
                ['nombres' => $e->nombres, 'apellidos' => $e->apellidos, 'fecha_nacimiento' => $e->fecha_nacimiento]
            );
            $e->persona_id = $persona->id;
            $e->save();
        }
    }

    /**
     * Genera una venta (maestro) con sus detalles por cada reserva con boletos,
     * y una factura para las ventas pagadas.
     */
    private function ventasYFacturas(): void
    {
        $mapaEstado = ['pendiente' => 'pendiente', 'confirmada' => 'pagada', 'completada' => 'pagada', 'cancelada' => 'anulada'];
        $nFactura = Factura::count();

        foreach (Reserva::with(['boletos', 'titular'])->get() as $reserva) {
            if ($reserva->boletos->isEmpty()) {
                continue;
            }

            $venta = Venta::firstOrCreate(
                ['numero' => 'V-' . $reserva->codigo],
                [
                    'persona_id'  => $reserva->titular?->persona_id,
                    'usuario_id'  => $reserva->usuario_id,
                    'reserva_id'  => $reserva->id,
                    'fecha'       => $reserva->fecha_reserva ?? now(),
                    'estado'      => $mapaEstado[$reserva->estado] ?? 'pendiente',
                ]
            );

            foreach ($reserva->boletos as $boleto) {
                VentaDetalle::firstOrCreate(
                    ['venta_id' => $venta->id, 'boleto_id' => $boleto->id],
                    [
                        'descripcion'     => 'Boleto ' . $boleto->numero_boleto,
                        'cantidad'        => 1,
                        'precio_unitario' => $boleto->precio,
                        'subtotal'        => $boleto->precio,
                    ]
                );
            }

            // Recalcular totales del maestro desde el detalle
            $subtotal = $venta->detalles()->sum('subtotal');
            $venta->update(['subtotal' => $subtotal, 'total' => $subtotal - $venta->descuento]);

            // Opción A: el dinero vive en la venta. Trasladar los pagos de la reserva a la venta
            // y fijar el estado de la venta según lo pagado.
            \App\Models\Pago::where('reserva_id', $reserva->id)->whereNull('venta_id')->update(['venta_id' => $venta->id]);
            $pagado = (float) \App\Models\Pago::where('venta_id', $venta->id)->where('estado', 'pagado')->sum('monto');
            $estadoVenta = ($venta->total > 0 && $pagado >= $venta->total) ? 'pagada' : 'pendiente';
            $venta->update(['estado' => $estadoVenta]);

            // Factura para ventas pagadas
            if ($estadoVenta === 'pagada' && ! $venta->factura) {
                $cliente = $reserva->titular?->persona;
                $baseSinIva = round($venta->total / 1.13, 2);   // IVA Bolivia 13% incluido en el precio
                $iva = round($venta->total - $baseSinIva, 2);

                Factura::create([
                    'numero_factura' => 'F-' . str_pad((string) (++$nFactura), 6, '0', STR_PAD_LEFT),
                    'venta_id'       => $venta->id,
                    'persona_id'     => $cliente?->id,
                    'nit'            => $cliente?->numero_documento,
                    'razon_social'   => $cliente?->nombre_completo,
                    'fecha_emision'  => $venta->fecha,
                    'subtotal'       => $baseSinIva,
                    'impuesto_iva'   => $iva,
                    'total'          => $venta->total,
                    'estado'         => 'emitida',
                ]);
            }
        }
    }
}

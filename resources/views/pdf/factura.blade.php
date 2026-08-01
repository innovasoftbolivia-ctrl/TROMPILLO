<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $factura->numero_factura }}</title>
    <style>
        * { box-sizing: border-box; }
        @page { margin: 0; }
        body {
            font-family: 'DejaVu Sans', 'Helvetica', sans-serif;
            font-size: 12px; margin: 0; padding: 0; color: #1f2937;
            -webkit-print-color-adjust: exact;
        }
        .page { padding: 34px 40px 40px 40px; position: relative; }

        /* Marca de agua ANULADA */
        .watermark {
            position: fixed; top: 42%; left: 12%;
            font-size: 120px; font-weight: bold; color: #ef4444;
            opacity: 0.12; transform: rotate(-28deg); letter-spacing: 6px;
        }

        /* Cabecera */
        .header-band { background: #0f766e; height: 8px; width: 100%; }
        table { border-collapse: collapse; width: 100%; }
        .header td { vertical-align: top; padding: 0; }
        .brand-name { font-size: 22px; font-weight: bold; color: #0f766e; letter-spacing: .5px; }
        .brand-sub { font-size: 10.5px; color: #6b7280; line-height: 1.55; margin-top: 3px; }
        .doc-title { font-size: 15px; font-weight: bold; color: #111827; letter-spacing: 3px; }
        .doc-meta { font-size: 10.5px; color: #374151; line-height: 1.7; margin-top: 6px; }
        .doc-meta .num { font-size: 15px; font-weight: bold; color: #0f766e; }

        .badge {
            display: inline-block; padding: 3px 12px; border-radius: 20px;
            font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: .5px;
        }
        .badge-emitida { background: #d1fae5; color: #047857; }
        .badge-anulada { background: #fee2e2; color: #b91c1c; }

        /* Cuadro cliente */
        .client-box {
            margin-top: 26px; background: #f0fdfa; border: 1px solid #ccfbf1;
            border-left: 4px solid #0f766e; border-radius: 6px; padding: 14px 16px;
        }
        .client-box .label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #0f766e; font-weight: bold; }
        .client-box .val { font-size: 13px; font-weight: bold; color: #111827; }
        .client-box .small { font-size: 10.5px; color: #6b7280; }

        /* Tabla detalle */
        .items { margin-top: 22px; }
        .items thead th {
            background: #0f766e; color: #ffffff; font-size: 10px; text-transform: uppercase;
            letter-spacing: .5px; padding: 9px 10px; text-align: left; font-weight: bold;
        }
        .items thead th.r { text-align: right; }
        .items thead th.c { text-align: center; }
        .items tbody td { padding: 9px 10px; font-size: 11.5px; border-bottom: 1px solid #e5e7eb; }
        .items tbody tr:nth-child(even) td { background: #f9fafb; }
        .items td.r { text-align: right; }
        .items td.c { text-align: center; }
        .item-sub { font-size: 9.5px; color: #9ca3af; margin-top: 2px; }

        /* Totales */
        .totals { margin-top: 18px; }
        .totals td { padding: 5px 10px; font-size: 12px; }
        .totals .lbl { text-align: right; color: #4b5563; }
        .totals .amt { text-align: right; width: 130px; color: #111827; }
        .totals .discount { color: #dc2626; }
        .totals .grand td { border-top: 2px solid #0f766e; padding-top: 10px; font-size: 15px; font-weight: bold; color: #0f766e; }

        /* Pie */
        .legal { margin-top: 34px; border-top: 1px dashed #d1d5db; padding-top: 14px; }
        .legal p { font-size: 9.5px; color: #9ca3af; text-align: center; margin: 4px 0; font-style: italic; }
        .thanks { text-align: center; font-size: 11px; color: #0f766e; font-weight: bold; margin-top: 8px; }
        .seller { margin-top: 4px; text-align: center; font-size: 9.5px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="header-band"></div>

    @if ($factura->estado === 'anulada')
        <div class="watermark">ANULADA</div>
    @endif

    <div class="page">
        {{-- Cabecera --}}
        <table class="header">
            <tr>
                <td style="width: 58%;">
                    <div class="brand-name">AEROLÍNEA EL TROMPILLO S.A.</div>
                    <div class="brand-sub">
                        NIT: 1029384756<br>
                        Av. Santos Dumont, Santa Cruz de la Sierra — Bolivia<br>
                        Tel: (591) 3-123-4567 · facturacion@trompillo.bo
                    </div>
                </td>
                <td style="width: 42%; text-align: right;">
                    <div class="doc-title">FACTURA</div>
                    <div class="doc-meta">
                        <span class="num">N.º {{ $factura->numero_factura }}</span><br>
                        Emisión: {{ \Carbon\Carbon::parse($factura->fecha_emision)->format('d/m/Y H:i') }}<br>
                        @if ($factura->venta)
                            Venta: {{ $factura->venta->numero }}<br>
                        @endif
                        @if ($factura->venta && $factura->venta->reserva)
                            Reserva (PNR): {{ $factura->venta->reserva->codigo }}<br>
                        @endif
                        <span class="badge {{ $factura->estado === 'anulada' ? 'badge-anulada' : 'badge-emitida' }}">
                            {{ $factura->estado === 'anulada' ? 'Anulada' : 'Emitida' }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Cliente --}}
        <table class="client-box">
            <tr>
                <td style="width: 60%;">
                    <div class="label">Facturado a</div>
                    <div class="val">{{ $factura->razon_social ?: 'S/N' }}</div>
                </td>
                <td style="width: 40%; text-align: right;">
                    <div class="label">NIT / CI</div>
                    <div class="val">{{ $factura->nit ?: '0' }}</div>
                </td>
            </tr>
        </table>

        {{-- Detalle --}}
        <table class="items">
            <thead>
                <tr>
                    <th class="c" style="width: 8%;">Cant.</th>
                    <th style="width: 56%;">Descripción</th>
                    <th class="r" style="width: 18%;">P. Unitario</th>
                    <th class="r" style="width: 18%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($factura->venta?->detalles ?? [] as $d)
                    <tr>
                        <td class="c">{{ $d->cantidad }}</td>
                        <td>
                            {{ $d->descripcion }}
                            @if ($d->boleto)
                                <div class="item-sub">Boleto: {{ $d->boleto->numero_boleto }}</div>
                            @endif
                        </td>
                        <td class="r">Bs {{ number_format($d->precio_unitario, 2) }}</td>
                        <td class="r">Bs {{ number_format($d->subtotal, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="c">1</td>
                        <td>Servicios de transporte aéreo — Venta de boletos</td>
                        <td class="r">Bs {{ number_format($factura->subtotal, 2) }}</td>
                        <td class="r">Bs {{ number_format($factura->subtotal, 2) }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Totales --}}
        <table class="totals">
            @if ($factura->descuento > 0)
                <tr>
                    <td class="lbl">Subtotal</td>
                    <td class="amt">Bs {{ number_format($factura->subtotal + $factura->descuento, 2) }}</td>
                </tr>
                <tr>
                    <td class="lbl">Descuento</td>
                    <td class="amt discount">- Bs {{ number_format($factura->descuento, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td class="lbl">Importe base crédito fiscal</td>
                <td class="amt">Bs {{ number_format($factura->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td class="lbl">IVA (13%)</td>
                <td class="amt">Bs {{ number_format($factura->impuesto_iva, 2) }}</td>
            </tr>
            <tr class="grand">
                <td class="lbl">TOTAL A PAGAR</td>
                <td class="amt">Bs {{ number_format($factura->total, 2) }}</td>
            </tr>
        </table>

        {{-- Pie legal --}}
        <div class="legal">
            <p>"ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAÍS, EL USO ILÍCITO SERÁ SANCIONADO PENALMENTE DE ACUERDO A LEY"</p>
            <div class="thanks">Gracias por volar con Aerolínea El Trompillo</div>
            @if ($factura->venta && $factura->venta->vendedor)
                <div class="seller">Atendido por: {{ $factura->venta->vendedor->name }}</div>
            @endif
        </div>
    </div>
</body>
</html>

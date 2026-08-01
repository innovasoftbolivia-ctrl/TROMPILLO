<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $factura->numero }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; margin: 0; padding: 30px; color: #333; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { color: #2563eb; margin: 0; font-size: 28px; }
        .header p { margin: 5px 0 0 0; color: #6b7280; font-size: 12px; }
        
        .info-container { width: 100%; margin-bottom: 30px; }
        .info-box { display: inline-block; width: 48%; vertical-align: top; }
        .info-box h3 { font-size: 14px; color: #2563eb; margin-bottom: 5px; text-transform: uppercase; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background-color: #f3f4f6; color: #374151; font-weight: bold; text-align: left; padding: 10px; font-size: 12px; text-transform: uppercase; border-bottom: 1px solid #d1d5db; }
        td { padding: 10px; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        
        .totals { width: 40%; margin-left: 60%; }
        .totals-row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 14px; }
        .totals-row.grand-total { font-weight: bold; font-size: 18px; color: #2563eb; border-top: 2px solid #2563eb; padding-top: 10px; margin-top: 5px; }
        
        .footer { text-align: center; margin-top: 50px; font-size: 12px; color: #9ca3af; padding-top: 20px; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>

    <div class="header">
        <h1>AEROLÍNEA TROMPILLO S.A.</h1>
        <p>NIT: 123456789 | Av. Principal, Santa Cruz, Bolivia</p>
    </div>

    <div class="info-container">
        <div class="info-box">
            <h3>Datos del Cliente</h3>
            <strong>Nombre:</strong> {{ $factura->razon_social }}<br>
            <strong>NIT/CI:</strong> {{ $factura->nit_cliente }}<br>
            <strong>Fecha de Emisión:</strong> {{ \Carbon\Carbon::parse($factura->fecha_emision)->format('d/m/Y') }}
        </div>
        <div class="info-box" style="text-align: right;">
            <h3>Detalle de Factura</h3>
            <strong>Nro Factura:</strong> {{ $factura->numero }}<br>
            <strong>Código de Control:</strong> {{ $factura->codigo_control ?? 'N/A' }}<br>
            <strong>Estado:</strong> {{ strtoupper($factura->estado) }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Reserva Asociada</th>
                <th style="text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Servicios de Transporte Aéreo - Venta de Boletos</td>
                <td>{{ $factura->venta->reserva->codigo ?? 'N/A' }}</td>
                <td style="text-align: right;">Bs. {{ number_format($factura->monto_total, 2) }}</td>
            </tr>
            <!-- Aquí podríamos iterar detalles de la venta si los hubiera -->
        </tbody>
    </table>

    <table class="totals" style="border:none;">
        <tr>
            <td style="text-align: right; padding: 5px; border: none;"><strong>Subtotal:</strong></td>
            <td style="text-align: right; padding: 5px; border: none;">Bs. {{ number_format($factura->monto_total, 2) }}</td>
        </tr>
        <tr>
            <td style="text-align: right; padding: 5px; border: none;"><strong>Impuestos (13%):</strong></td>
            <td style="text-align: right; padding: 5px; border: none;">Bs. {{ number_format($factura->impuestos, 2) }}</td>
        </tr>
        <tr>
            <td style="text-align: right; padding: 10px; border: none; border-top: 2px solid #2563eb; font-weight: bold; font-size: 18px; color: #2563eb;">Total a Pagar:</td>
            <td style="text-align: right; padding: 10px; border: none; border-top: 2px solid #2563eb; font-weight: bold; font-size: 18px; color: #2563eb;">Bs. {{ number_format($factura->monto_total, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        "ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAÍS, EL USO ILÍCITO SERÁ SANCIONADO PENALMENTE DE ACUERDO A LEY"<br>
        Gracias por preferir Aerolínea Trompillo.
    </div>

</body>
</html>

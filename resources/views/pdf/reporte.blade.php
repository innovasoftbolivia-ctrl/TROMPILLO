<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; margin: 0; padding: 28px; }
        .header { border-bottom: 3px solid #008225; padding-bottom: 12px; margin-bottom: 16px; }
        .header h1 { color: #008225; margin: 0; font-size: 22px; }
        .header .sub { color: #6b7280; font-size: 12px; margin-top: 5px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.data th { background: #008225; color: #fff; text-align: left; padding: 7px 8px; font-size: 10px; text-transform: uppercase; }
        table.data td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        table.data tr:nth-child(even) td { background: #f9fafb; }
        .r { text-align: right; }
        .totales { margin-top: 18px; width: 46%; margin-left: 54%; border-collapse: collapse; }
        .totales td { padding: 4px 8px; font-size: 12px; }
        .totales .lbl { color: #6b7280; }
        .totales .val { text-align: right; font-weight: bold; color: #111827; }
        .totales tr:last-child .val { color: #008225; font-size: 14px; border-top: 2px solid #008225; }
        .totales tr:last-child .lbl { border-top: 2px solid #008225; }
        .footer { margin-top: 34px; text-align: center; color: #9ca3af; font-size: 10px; border-top: 1px solid #e5e7eb; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>AEROLÍNEA EL TROMPILLO</h1>
        <div class="sub"><strong>{{ $titulo }}</strong> &nbsp;·&nbsp; Período: {{ $periodo }} &nbsp;·&nbsp; Generado: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table class="data">
        <thead>
            <tr>
                @foreach ($columnas as $i => $c)
                    <th class="{{ ($align[$i] ?? 'left') === 'right' ? 'r' : '' }}">{{ $c }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($filas as $fila)
                <tr>
                    @foreach ($fila as $i => $celda)
                        <td class="{{ ($align[$i] ?? 'left') === 'right' ? 'r' : '' }}">{{ $celda }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($columnas) }}" style="text-align:center;color:#9ca3af;padding:22px;">Sin datos en el período seleccionado.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="totales">
        @foreach ($totales as $t)
            <tr><td class="lbl">{{ $t['label'] }}</td><td class="val">{{ $t['valor'] }}</td></tr>
        @endforeach
    </table>

    <div class="footer">Aerolínea El Trompillo S.A. · Santa Cruz de la Sierra, Bolivia · Documento generado por el sistema</div>
</body>
</html>

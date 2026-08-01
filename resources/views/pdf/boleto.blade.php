<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleto {{ $boleto->id }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; margin: 0; padding: 20px; color: #333; }
        .boarding-pass { border: 2px solid #2563eb; border-radius: 10px; padding: 20px; width: 100%; max-width: 600px; margin: 0 auto; }
        .header { background-color: #2563eb; color: white; padding: 10px 20px; text-align: center; border-radius: 5px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; text-transform: uppercase; letter-spacing: 2px; }
        .row { width: 100%; margin-bottom: 15px; }
        .col { display: inline-block; width: 48%; vertical-align: top; }
        .label { font-size: 10px; color: #6b7280; text-transform: uppercase; }
        .value { font-size: 18px; font-weight: bold; color: #1f2937; }
        .route { font-size: 24px; font-weight: bold; text-align: center; margin: 20px 0; color: #2563eb; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #9ca3af; border-top: 1px dashed #ccc; padding-top: 10px; }
        .barcode { text-align: center; margin-top: 20px; font-family: 'Courier New', Courier, monospace; font-size: 20px; letter-spacing: 5px; }
    </style>
</head>
<body>
    <div class="boarding-pass">
        <div class="header">
            <h1>Aerolínea Trompillo</h1>
            <p style="margin:0;font-size:12px;">BOARDING PASS / BOLETO DE EMBARQUE</p>
        </div>
        
        <div class="route">
            {{ $boleto->vuelo->origen->codigo_iata ?? 'ORG' }} ✈ {{ $boleto->vuelo->destino->codigo_iata ?? 'DST' }}
        </div>
        
        <div class="row">
            <div class="col">
                <div class="label">Pasajero / Passenger Name</div>
                <div class="value">{{ $boleto->pasajero->nombre_completo ?? 'N/A' }}</div>
            </div>
            <div class="col">
                <div class="label">Documento / ID</div>
                <div class="value">{{ $boleto->pasajero->numero_documento ?? 'N/A' }}</div>
            </div>
        </div>
        
        <div class="row">
            <div class="col">
                <div class="label">Vuelo / Flight</div>
                <div class="value">{{ $boleto->vuelo->numero_vuelo ?? 'S/N' }}</div>
            </div>
            <div class="col">
                <div class="label">Fecha y Hora / Date & Time</div>
                <div class="value">{{ \Carbon\Carbon::parse($boleto->vuelo->salida_programada)->format('d/m/Y H:i') }}</div>
            </div>
        </div>
        
        <div class="row">
            <div class="col">
                <div class="label">Asiento / Seat</div>
                <div class="value" style="color: #ef4444; font-size: 22px;">{{ $boleto->asiento ?? 'PENDIENTE' }}</div>
            </div>
            <div class="col">
                <div class="label">Puerta / Gate</div>
                <div class="value">TBA</div>
            </div>
        </div>

        <div class="barcode">
            |||| ||||| |||| || ||| |||<br>
            TKT-{{ str_pad($boleto->id, 8, '0', STR_PAD_LEFT) }}
        </div>
        
        <div class="footer">
            Por favor, preséntese en la puerta de embarque 45 minutos antes de la salida.<br>
            El equipaje permitido es de {{ $boleto->equipaje_kg ?? 0 }} kg.
        </div>
    </div>
</body>
</html>

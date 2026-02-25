<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Servicio - {{ $orden->id }}</title>
    <style>
        @page { margin: 0.8cm; }
        body { font-family: 'Helvetica', sans-serif; color: #1a202c; font-size: 11px; line-height: 1.5; }
        
        /* Layout */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .header-left { width: 50%; vertical-align: top; }
        .header-right { width: 50%; vertical-align: top; padding-left: 20px; }
        
        /* Logo */
        .logo-container { margin-bottom: 10px; }
        .logo { max-width: 300px; max-height: 200px; }
        
        /* Caja de Número de Orden */
        .order-box { border: 2px solid #2d3748; border-radius: 12px; padding: 15px; background: #f8fafc; }
        .order-title { font-size: 14px; font-weight: bold; color: #2d3748; margin-bottom: 5px; }
        .order-number { font-size: 18px; color: #e53e3e; font-weight: bold; }
        
        /* Información Taller */
        .taller-name { font-size: 18px; font-weight: 900; color: #1e40af; text-transform: uppercase; margin: 0; }
        .taller-detail { font-size: 15px; color: #000000; margin-top: 5px; }

        /* Bloque Cliente/Vehículo */
        .info-section { background: #edf2f7; padding: 10px; border-radius: 8px; margin-bottom: 20px; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 3px 0; }
        .label { font-weight: bold; color: #000000; text-transform: uppercase; font-size: 9px; }

        /* Tabla de Servicios */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { background: #2d3748; color: white; padding: 10px; text-align: left; text-transform: uppercase; font-size: 10px; }
        .items-table td { padding: 10px; border-bottom: 1px solid #e2e8f0; }

        /* Totales */
        .footer-container { width: 100%; margin-top: 10px; }
        .notes-box { width: 55%; float: left; border: 1px solid #e2e8f0; padding: 10px; border-radius: 8px; min-height: 80px; }
        .totals-box { width: 35%; float: right; }
        .totals-table { width: 100%; border-collapse: collapse; }
        .totals-table td { padding: 6px; border: 1px solid #e2e8f0; }
        .bg-total { background: #2d3748; color: white; font-weight: bold; }

        .clearfix { clear: both; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="header-left">
                <div class="logo-container">
                    @if(file_exists(public_path('img/logo.png')))
                        <img src="{{ public_path('img/logo.png') }}" class="logo">
                    @else
                        <h1 class="taller-name">CarShop & Services</h1>
                    @endif
                </div>
                <div class="taller-detail">
                    <strong>Dirección:</strong> Calle: AV ELOY ALFARO Numero: N45-184 Interseccion:
                        LAS BUGAMBILLAS<br>
                    <strong>Contacto:</strong> +593 98 688 4779<br>
                    <strong>Email:</strong> carshopandservices@gmail.com<br>
                    <strong>Especialistas en:</strong> Mecánica General - Electrónica
                </div>
            </td>
            <td class="header-right">
                <div class="order-box">
                    <div class="order-title">ORDEN DE SERVICIO</div>
                    <div class="order-number">No. {{ str_pad($orden->id, 6, '0', STR_PAD_LEFT) }}</div>
                    <div style="margin-top: 8px;">
                        <strong>Ambiente:</strong> INTERNO<br>
                        <strong>Emisión:</strong> ORIGINAL<br>
                        <strong>Fecha:</strong> {{ $orden->fecha->format('d/m/Y') }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td width="15%" class="label">Cliente:</td>
                <td width="45%">{{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }}</td>
                <td width="15%" class="label">Placa:</td>
                <td width="25%"><strong>{{ $orden->vehiculo->placa }}</strong></td>
            </tr>
            <tr>
                <td class="label">Email:</td>
                <td>{{ $orden->cliente->email }}</td>
                <td class="label">Vehículo:</td>
                <td>{{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}</td>
            </tr>
            <tr>
                <td class="label">Celular:</td>
                <td>{{ $orden->cliente->telefono }}</td>
                <td class="label">Cedula:</td>
                <td>{{ $orden->cliente->cedula }}</td>
            </tr>
        </table>
    </div>


    <table class="items-table">
        <thead>
            <tr>
                <th width="10%">Cant.</th>
                <th width="65%">Descripción del Servicio</th>
                <th width="25%" style="text-align: right;">Precio Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orden->servicios as $servicio)
            <tr>
                <td>1.00</td>
                <td>{{ $servicio->nombre }}</td>
                <td style="text-align: right;">${{ number_format($servicio->pivot->precio, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
        
        <div class="totals-box">
            <table class="totals-table">
                <tr>
                    <td style="font-weight: bold;">Subtotal</td>
                    <td style="text-align: right;">${{ number_format($orden->total, 2) }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Descuento</td>
                    <td style="text-align: right;">$0.00</td>
                </tr>
                <tr class="bg-total">
                    <td style="font-size: 14px;">TOTAL</td>
                    <td style="text-align: right; font-size: 14px;">${{ number_format($orden->total, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="clearfix"></div>

</body>
</html>
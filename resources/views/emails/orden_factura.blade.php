<div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #2d3748; line-height: 1.6; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;">
    
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="color: #1e40af; margin-bottom: 5px;">Comprobante de Servicio Digital</h2>
        <p style="font-size: 14px; color: #718096; margin-top: 0;">CarShop & Services - Quito</p>
    </div>

    <p style="font-size: 16px;">Estimado(a) <strong>{{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }}</strong>,</p>

    <p>Es un gusto saludarte. Adjunto a este mensaje encontrarás el detalle de los servicios realizados a tu vehículo el día <strong>{{ $orden->fecha->format('d/m/Y') }}</strong>.</p>

    <div style="background-color: #f8fafc; border-radius: 8px; padding: 15px; margin: 20px 0;">
        <h3 style="font-size: 14px; color: #4a5568; text-transform: uppercase; margin-top: 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">Resumen del Vehículo</h3>
        <table style="width: 100%; font-size: 14px;">
            <tr>
                <td style="color: #718096; width: 40%;">Marca/Modelo:</td>
                <td><strong>{{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}</strong></td>
            </tr>
            <tr>
                <td style="color: #718096;">Placa:</td>
                <td><span style="background-color: #fef3c7; color: #92400e; padding: 2px 6px; border-radius: 4px; font-weight: bold;">{{ $orden->vehiculo->placa }}</span></td>
            </tr>
            <tr>
                <td style="color: #718096;">Identificación:</td>
                <td>{{ $orden->cliente->cedula ?? 'Consumidor Final' }}</td>
            </tr>
        </table>
    </div>

    <p>Para cualquier duda o consulta sobre la garantía de tus servicios, no dudes en contactarnos vía WhatsApp al <strong>+593 98 688 4779</strong>.</p>

    <p style="margin-top: 30px;">Gracias por confiar en el equipo de <strong>CarShop & Services</strong>.</p>

    <div style="border-top: 1px solid #e2e8f0; margin-top: 30px; padding-top: 15px; text-align: center; font-size: 12px; color: #a0aec0;">
        <p>Este es un correo automático, por favor no respondas directamente a este remitente.</p>
        <p>Quito, Ecuador</p>
    </div>
</div>
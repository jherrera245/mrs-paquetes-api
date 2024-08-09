<!DOCTYPE html>
<html>
<head>
    <title>Orden de Envío</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 100%; padding: 20px; }
        .header, .footer { text-align: center; margin-bottom: 20px; }
        .content { margin-bottom: 40px; }
        .content h3 { margin-bottom: 10px; }
        .content p { margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Detalle de la Orden</h2>
        </div>
        
        <div class="content">
            <h3>Información del Emisor</h3>
            <p><strong>Cliente:</strong> {{ $orden->cliente->nombre ?? 'N/A' }}</p>
            <p><strong>Dirección:</strong> {{ $direccion_emisor->direccion ?? 'N/A' }}</p>
            <p><strong>Referencia:</strong> {{ $direccion_emisor->referencia ?? 'N/A' }}</p>
            <p><strong>Teléfono:</strong> {{ $direccion_emisor->telefono ?? 'N/A' }}</p>

            <h3>Detalles de los Paquetes</h3>
            @foreach ($orden->detalles as $detalle)
                <p><strong>Paquete ID:</strong> {{ $detalle->id_paquete }}</p>
                <p><strong>Descripción:</strong> {{ $detalle->descripcion }}</p>
                <p><strong>Precio:</strong> {{ $detalle->precio }}</p>
            @endforeach

            <h3>Información del Pago</h3>
            <p><strong>Tipo de Pago:</strong> {{ $orden->tipoPago->pago ?? 'N/A' }}</p>
            <p><strong>Total a Pagar:</strong> {{ $orden->total_pagar }}</p>
            <p><strong>Costo Adicional:</strong> {{ $orden->costo_adicional }}</p>
            <p><strong>Concepto:</strong> {{ $orden->concepto }}</p>
        </div>

        <div class="footer">
            <p>Generado el {{ date('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html>

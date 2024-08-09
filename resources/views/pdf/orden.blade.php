<!DOCTYPE html>
<html>
<head>
    <title>Orden #{{ $orden->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            margin: 0 auto;
            padding: 20px;
            width: 80%;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
        }
        .content {
            margin-bottom: 20px;
        }
        .content p {
            margin: 5px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Detalles de la Orden</h1>
        </div>
        <div class="content">
            <p><strong>Orden ID:</strong> {{ $orden->id }}</p>
            <p><strong>Cliente ID:</strong> {{ $orden->id_cliente }}</p>
            <p><strong>Tipo de Pago:</strong> {{ $orden->tipo_pago->pago ?? 'NA' }}</p>
            <p><strong>Total a Pagar:</strong> {{ $orden->total_pagar }}</p>
            <p><strong>Costo Adicional:</strong> {{ $orden->costo_adicional }}</p>
            <p><strong>Concepto:</strong> {{ $orden->concepto }}</p>
            <h3>Dirección del Emisor:</h3>
            <p><strong>Nombre del Contacto:</strong> {{ $direccion_emisor->nombre_contacto }}</p>
            <p><strong>Teléfono:</strong> {{ $direccion_emisor->telefono }}</p>
            <p><strong>Dirección:</strong> {{ $direccion_emisor->direccion }}</p>
            <p><strong>Referencia:</strong> {{ $direccion_emisor->referencia }}</p>
            <h3>Detalles de los Paquetes:</h3>
            @foreach ($orden->detalles as $detalle)
                <p><strong>Paquete ID:</strong> {{ $detalle->id_paquete }}</p>
                <p><strong>Descripción:</strong> {{ $detalle->descripcion }}</p>
                <p><strong>Precio:</strong> {{ $detalle->precio }}</p>
            @endforeach
        </div>
        <div class="footer">
            <p>Generado el {{ date('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html>

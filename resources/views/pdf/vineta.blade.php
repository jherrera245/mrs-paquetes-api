<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viñeta de Envío</title>
    <style>
        body, html { 
            margin: 0; 
            padding: 0; 
            width: 576px;
            height: 576px;
        }
        body { 
            font-family: Arial, sans-serif; 
            font-size: 14px;
            background-color: white;
        }
        .container { 
            width: 576px; 
            height: 576px; 
            position: relative; 
            box-sizing: border-box;
            padding: 20px;
        }
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
        }
        .logo { 
            font-size: 28px;
            font-weight: bold;
            color: #0066cc;
        }
        .contact-info { 
            font-size: 12px; 
            text-align: right; 
            color: #555;
        }
        .qr-code { 
            position: absolute; 
            top: 120px; 
            left: 20px; 
            width: 160px; 
            height: 160px; 
            padding: 5px;
            background-color: white;
            border: 1px solid #e0e0e0;
        }
        .main-info { 
            margin-left: 200px; 
            margin-top: 20px; 
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }
        .tracking { 
            font-size: 24px; 
            font-weight: bold; 
            margin-top: 20px; 
            margin-bottom: 20px;
            text-align: center;
            background-color: #e6f2ff;
            padding: 15px;
            border-radius: 5px;
        }
        .destination { 
            margin-top: 30px; 
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }
        .footer { 
            position: absolute; 
            bottom: 20px; 
            left: 20px;
            right: 20px;
            text-align: center; 
            font-size: 12px; 
            color: #777;
            padding-top: 10px;
            border-top: 1px solid #e0e0e0;
        }
        .bold { 
            font-weight: bold; 
            color: #333;
        }
        p {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Mrs.Paquetes</div>
            <div class="contact-info">
                <p><span class="bold">Cliente:</span> {{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }}</p>
                <p><span class="bold">Tel:</span> {{ $orden->cliente->telefono }}</p>
            </div>
        </div>
        
        <div class="qr-code">
            <img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="QR Code" style="width: 100%; height: 100%;">
        </div>
        
        <div class="main-info">
            <p><span class="bold">DESTINO:</span> {{ $orden->direccion->municipio->nombre }}</p>
            <p><span class="bold">Nombre:</span> {{ $orden->direccion->nombre_contacto }}</p>
            <p><span class="bold">Contacto:</span> {{ $orden->direccion->telefono }}</p>
            <p><span class="bold">Dirección:</span> {{ $orden->direccion->direccion }}</p>
            <p><span class="bold">Referencia:</span> {{ $orden->direccion->referencia }}</p>
        </div>
        
        <div class="tracking">
            Tracking: {{ $orden->numero_seguimiento }}
        </div>
        
        <div class="destination">
            <p><span class="bold"><span class="bold">Destino:</span> {{ $orden->direccion->municipio->nombre }}</p>
            <p><span class="bold">Estado de pago:</span> {{ $mensajePago }}</p>
            <p><span class="bold">Peso:</span> {{ $orden->detalles->sum('paquete.peso') }} LB</p>
            <p><span class="bold">Instrucciones:</span> {{ $orden->detalles->first()->instrucciones_entrega ?? 'Ninguna' }}</p>
        </div>
        
        <div class="footer">
            <p>Fecha: {{ $orden->created_at->format('d/m/Y') }} | No. Orden: {{ $orden->id }} | Tipo de entrega: {{ $orden->detalles->first()->tipoEntrega->entrega }}</p>
        </div>
    </div>
</body>
</html>
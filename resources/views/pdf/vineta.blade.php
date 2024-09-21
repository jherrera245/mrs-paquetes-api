<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viñeta de Envío</title>
    <style>
        @page {
            width: 625px; 
            height: 625px; 
            page-break-after: always;
            font-family: Arial, sans-serif; 
            font-size: 14px;
        }

        body, html { 
            margin: 0; 
            padding: 0; 
        }

        body {
            border-bottom: 1px dashed #ddd;
        }

        .container { 
            width:625px; 
            height: 625px; 
            position: relative; 
            box-sizing: border-box;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            padding: 10px;
        }

    </style>
</head>

<body>
    @foreach($orden->detalles as $detalle)
    <div class="container">

        <table>
            <tr>
                <td style="text-align:center;">
                    <img src="{{ $logo }}" alt="logo" style="width: 2.5cm; height: 2.5cm;">
                </td>

                <td colspan="2" style="text-align: center">
                    <p>Fecha: {{ $orden->created_at->format('d/m/Y') }} | No. Orden: {{ $orden->id }} | Tipo de entrega: {{ $detalle->tipoEntrega->entrega }}</p>
                </td>
            </tr>
            <tr>
                <td rowspan="6" style="width: 10%;">
                    <img src="data:image/png;base64,{{ $detalle->qr_paquete }}" alt="QR Code" style="width: 5cm; height: 5cm;">
                </td>
                <td style="width: 15%;"><b>Cliente<b></td>
                <td>{{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }}</td>
            </tr>
            <tr>
                <td><b>Tel<b></td>
                <td>{{ $orden->cliente->telefono }}</td>
            </tr>
            <tr>
                <td><b>Origen:<b></td>
                <td> {{ $orden->direccion->municipio->nombre }}</td>
            </tr>
            <tr>
                <td><b>Nombre<b></td>
                <td>{{ $orden->direccion->municipio->nombre }}</td>
            </tr>
            <tr>
                <td><b>Contacto<b></td>
                <td>{{ $orden->direccion->telefono }}</td>
            </tr>
            <tr>
                <td><b>Dirección<b></td>
                <td>{{ $orden->direccion->direccion }}</td>
            </tr>
            <tr>
                <th colspan="3">
                    Tracking: {{ $orden->numero_seguimiento }}
                </th>
            </tr>
            <tr>
                <td><b>Destino:</b></td>
                <td colspan="2">{{ $detalle->direccionEntrega->municipio->nombre }}</td>
            </tr>
            <tr>
                <td><b>Contacto:</b></td>
                <td colspan="2">{{ $detalle->direccionEntrega->nombre_contacto }}</td>
            </tr>
            <tr>
                <td><b>Teléfono:</b></td>
                <td colspan="2">{{ $detalle->direccionEntrega->telefono }}</td>
            </tr>
            <tr>
                <td><b>Estado de pago:</b></td>
                <td colspan="2">{{ $mensajePago }}</td>
            </tr>
            <tr>
                <td><b>Peso:</b></td>
                <td colspan="2">{{ $detalle->paquete->peso }} LB</td>
            </tr>
            <tr>
                <td><b>Referencia:</b></td>
                <td colspan="2">{{ $detalle->direccionEntrega->referencia }}</td>
            </tr>
            <tr>
                <td><b>Instrucciones:</b></td>
                <td colspan="2">{{ $detalle->instrucciones_entrega ?? 'Ninguna' }}</td>
            </tr>
        </table>
    </div>
    @endforeach
</body>
</html>
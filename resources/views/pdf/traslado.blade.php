<!DOCTYPE html>
<html>
<head>
    <title>Traslados PDF</title>
    <style>
    .header {
    position: relative;
    top: 0;
    left: 0;
    right: 0;
    padding: 10px;
    border-bottom: 1px solid #000;
    background: white;
    text-align: right;
    font-size: 12px;
    }
    .header img {
        position: absolute;
        max-width: 60px;
    }
    .header h1 {
        text-align: right;
        margin: 5px 0;
        font-size: 14px;
        color: #333;
    }
    .header .fecha
    {
        font-size: 11px;
        color: #555;
    }
    .header .destino {
        
        font-size: 11px;
        color: #555;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: small;
    }
    table th, table td {
        font-size: 12px;
        border: 1px solid #ddd;
        padding: 6px;
        text-align: left;
    }
    table th {
        background-color: #f4f4f4;
        color: #333;
    }
    table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    table tr:hover {
        background-color: #f1f1f1;
    }
    table td[colspan="5"] {
        text-align: center;
        color: #777;
    }
    body {
    font-family: Arial, sans-serif;
    margin:10px;
    padding: 0;
        }
    </style>
</head>
<body>
    <!-- Solo un traslado -->
    @if($single)
        <div class="header">
            <img src="/xampp/htdocs/mrs-paquetes-api/public/images/logo-claro.png" alt="Logo">
            <h1>Traslado de Paquetes</h1>
            <div class="fecha">Fecha: {{ $fecha }}</div>
            <div class="destino">Destino: {{ $destino }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Bodega</th>
                    <th>Orden</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paquetes as $index => $paquete)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $paquete->uuid }}</td>
                        <td>{{ $paquete->descripcion_contenido }}</td>
                        <td>{{ $bodega }}</td>
                        <td>{{ $numero_seguimiento }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <!-- Múltiples traslados -->
        @foreach($traslados as $traslado)
            <div class="page">
                <div class="header">
                    <img src="/xampp/htdocs/mrs-paquetes-api/public/images/logo-claro.png" alt="Logo">
                    <h1>Traslado de Paquetes</h1>
                    <div class="fecha">Fecha: {{ $fecha }}</div>
                    <div class="destino">Destino: {{ $traslado->bodega_nombre }}</div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Bodega</th>
                            <th>Orden</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($traslado->paquetes as $index => $paquete)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $paquete->uuid }}</td>
                                <td>{{ $paquete->descripcion_contenido }}</td>
                                <td>{{ $traslado->bodega_nombre }}</td>
                                <td>{{ $traslado->numero_seguimiento }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif
</body>
</html>

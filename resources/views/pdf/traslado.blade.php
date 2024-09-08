<!DOCTYPE html>
<html>
<head>
    <title>Traslados PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .header {
            display: flex;
            justify-content: space-between; /* Aligns items to both ends */
            align-items: center; /* Vertically centers items */
            padding: 20px;
            border-bottom: 2px solid #000;
        }
        .header img {
            max-width: 120px;
            height: auto;
        }
        .header .info {
            text-align: right; /* Aligns text to the right */
            margin-left: 20px; /* Space between logo and info */
        }
        .header .info div {
            margin: 5px 0;
        }
        .header .info .fecha {
            font-size: 16px;
            font-weight: bold;
        }
        .header .info .destino {
            font-size: 16px;
            color: #007BFF; /* Blue color for emphasis */
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .table th {
            background-color: #f4f4f4;
            font-weight: bold;
            color: #333;
        }
        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .table tr:hover {
            background-color: #e9e9e9;
        }
        .table td {
            font-size: 14px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="/xampp/htdocs/mrs-paquetes-api/public/images/logo-claro.png" alt="Logo">
        <div class="info">
            <div class="fecha">Fecha: {{ $fecha }}</div>
            @if($single)
                <div class="destino">Destino: {{ $destino }}</div>
            @endif
        </div>
    </div>

    <table class="table">
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
            @if($single)
                @foreach($paquetes as $paquete)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $traslado->id }}</td>
                        <td>{{ $paquete->uuid }}</td>
                        <td>{{ $paquete->descripcion_contenido }}</td>
                        <td>{{ $bodega }}</td>
                        <td>{{ $traslado->id_orden }}</td>
                    </tr>
                @endforeach
            @else
                @forelse($traslados as $traslado)
                    @foreach($traslado->paquetes as $paquete)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $traslado->id }}</td>
                            <td>{{ $paquete->uuid }}</td>
                            <td>{{ $paquete->descripcion_contenido }}</td>
                            <td>{{ $traslado->bodega_nombre }}</td>
                            <td>{{ $traslado->id_orden }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">No hay datos disponibles.</td>
                    </tr>
                @endforelse
            @endif
        </tbody>
    </table>
</body>
</html>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @page {
            size: letter landscape;
            margin: 0cm 0cm;
        }

        body {
            margin-top: 5cm;
            margin-left: 1.5cm;
            margin-right: 1.5cm;
            margin-bottom: 0cm;
            font-family: "Arial", sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
            margin-bottom: 1cm;
        }

        th,
        td {
            border: 1px solid #dddddd;
            padding: 10px;
            text-align: center; /* Centrado del contenido */
        }

        th {
            font-size: 11px;
            background-color: #333333; /* Gris oscuro */
            color: white;
        }

        td {
            font-size: 10pt;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #f1f1f1;
        }

        header {
            position: fixed;
            top: 1.0cm;
            left: 1.5cm;
            right: 1.5cm;
            text-align: center;
            border-bottom: 1px solid #333; /* Línea separadora */
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 1cm;
            text-align: center;
            font-size: 8px;
        }

        h4 {
            margin: 5px 0;
            font-size: 12px; /* Ajuste de tamaño de fuente */
        }

        img.logo {
            width: 100px;
            height: auto;
            vertical-align: middle;
            margin-right: 10px;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .page {
            margin-top: 1cm;
        }
    </style>
</head>

<body>

    <header>
        <div class="header-content">
            <img src="{{ public_path('images/logo-claro.png') }}" class="logo" alt="Logo">
            <h3>Fecha: {{ $fecha }}</h3>
            @if($single)
                <h3>Destino: {{ $bodega_destino }}</h3> 
            @endif
        </div>
    </header>

    <div class="page">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Bodega origen</th>
                    <th>Bodega Destino</th>
                    <th>Orden</th>
                </tr>
            </thead>

            <tbody>
            @php $counter = 1; @endphp
            @if($single)
                @forelse($paquetes as $paquete)
                    <tr>
                        <td>{{ $counter++ }}</td>
                        <td>
                            <img src="data:image/png;base64,{{ $qrCodes[0] }}" alt="QR Code" style="width: 50px; height: 50px;" />
                        </td>
                        <td>{{ $paquete->descripcion_contenido }}</td>
                        <td>{{ $bodega_origen }}</td>
                        <td>{{ $bodega_destino }}</td>
                        <td>{{ $numero_seguimiento }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="no-data">No hay paquetes disponibles.</td>
                    </tr>
                @endforelse
            @else
                @forelse($traslados as $traslado)
                    @forelse($traslado->paquetes as $paquete)
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td>
                                <img src="data:image/png;base64,{{ $paquete->qrCode }}" alt="QR Code" style="width: 50px; height: 50px;" />
                            </td>
                            <td>{{ $paquete->descripcion_contenido }}</td>
                            <td>{{ $traslado->bodega_origen_nombre }}</td>
                            <td>{{ $traslado->bodega_destino_nombre }}</td>
                            <td>{{ $traslado->numero_seguimiento }}</td>
                        </tr>
                    @empty
                    @endforelse
                @empty
                    <tr>
                        <td colspan="5" class="no-data">No hay paquetes disponibles.</td>
                    </tr>
                @endforelse
            @endif
            </tbody>
        </table>
    </div>

    <footer>
        <p>Reporte generado automáticamente por el sistema.</p>
    </footer>

</body>

</html>

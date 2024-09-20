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
            text-align: left;
        }

        th {
            font-size: 11px;
            text-align: center;
            background-color: #333333; /* Gris oscuro */
            color: white;
        }

        td {
            font-size: 10pt;
            text-align: left;
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
            <h3>Reporte de ventas</h3>
            <h4>Periodo comprendido entre {{$fechaReporteInicio}} y {{$fechaReporteFinal}}</h4>
        </div>
    </header>

    <div class="page">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Número de control</th>
                    <th>Tipo documento</th>
                    <th>Cliente</th>
                    <th>Tipo pago</th>
                    <th>Gravado</th>
                    <th>IVA</th>
                    <th>Total operación</th>
                </tr>
            </thead>

            <tbody>
                @if (count($ordenes) > 0)
                    @foreach ($ordenes as $orden)
                    <tr>
                        <td>{{$orden->fecha}}</td>
                        <td>{{$orden->numero_control}}</td>
                        <td>{{$orden->tipo_documento}}</td>
                        <td>{{$orden->cliente}}</td>
                        <td>{{$orden->tipo_pago}}</td>
                        <td>{{ number_format($orden->total_gravado, 2) }}</td>
                        <td>{{ number_format($orden->total_iva, 2) }}</td>
                        <td>{{ number_format($orden->total_operacion, 2) }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" style="text-align:center;">No se encontraron registros que cumplan con el criterio de búsqueda.</td>
                    </tr>    
                @endif
            </tbody>
        </table>
    </div>

    <footer>
        <p>Reporte generado automáticamente por el sistema.</p>
    </footer>

</body>

</html>

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
            <h4>Reporte de Asignación de Rutas por Conductor correspondiente a {{$fechaReporte}}</h4>
            <h4>Responsable del vehículo: {{$conductor->nombres}} {{$conductor->apellidos}} {{ $apoyo ? ' acompañado por '.$apoyo->nombres.' '.$apoyo->apellidos: ''}}</h4>
            <h4>Número de paquetes asignados: {{$totalAsignaciones}}</h4>
        </div>
    </header>

    <div class="page">
        <table>
            <thead>
                <tr>
                    <th>Orden de entrega</th>
                    <th>Código de ruta</th>
                    <th>QR</th>
                    <th>Descripción del paquete</th>
                    <th>Peso (kg)</th>
                    <th>Departamento</th>
                    <th>Municipio</th>
                    <th>Destino</th>
                </tr>
            </thead>

            <tbody>
                @if (count($asignaciones) > 0)
                    @foreach ($asignaciones as $asignacion)
                    <tr>
                        <td>{{$asignacion->prioridad}}</td>
                        <td>{{$asignacion->codigo_unico_asignacion}}</td>
                        <td style="text-align:center">
                            <img src="data:image/png;base64,{{$asignacion->qr_base64}}" alt="QR Code" style="width: 60px; height: 60px;">
                        </td>
                        <td>{{$asignacion->descripcion_contenido}}</td>
                        <td>{{$asignacion->peso}}</td>
                        <td>{{$asignacion->departamento}}</td>
                        <td>{{$asignacion->municipio}}</td>
                        <td>{{$asignacion->destino}}</td>
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

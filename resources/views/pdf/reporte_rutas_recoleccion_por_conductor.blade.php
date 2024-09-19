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
            margin-top: 5.5cm;
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

        thead {
            display: table-header-group; /* Esto repite el encabezado en cada página */
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
            <h4>Reporte de rutas de recolección por Conductor correspondiente a {{$fechaReporte}}</h4>
            <h4>Responsable del vehículo: {{$conductor->nombres}} {{$conductor->apellidos}} {{ $apoyo ? ' acompañado por '.$apoyo->nombres.' '.$apoyo->apellidos: ''}}</h4>
            <h4>Número de paquetes en recolección estimados: {{$totalRecolecciones}}</h4>
        </div>
    </header>

    <div class="page">
        <table>
            <thead>
                <tr>
                    <th>Orden de recolección</th>
                    <th>Código de ruta</th>
                    <th>Departamento</th>
                    <th>Municipio</th>
                    <th>Destino</th>
                </tr>
            </thead>

            <tbody>
                @if (count($recolecciones) > 0)
                    @foreach ($recolecciones as $recoleccion)
                        <tr>
                            <td>{{$recoleccion->prioridad}}</td>
                            <td>{{$recoleccion->codigo_unico_recoleccion}}</td>
                            <td>{{$recoleccion->departamento}}</td>
                            <td>{{$recoleccion->municipio}}</td>
                            <td>{{$recoleccion->destino}}</td>
                        </tr>
                        <tr>
                            <th colspan="5">Detalle de paquetes {{$recoleccion->total_paquetes}} estimados</th>
                        </tr>
                        @foreach($recoleccion->paquetes as $paquete)
                            <tr>
                                <td colspan="2">{{$paquete->descripcion_contenido}}</td>
                                <td>{{$paquete->size}}</td>
                                <td>{{$paquete->peso}}</td>
                                <td>{{$paquete->empaquetado}}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <th colspan="5"></th>
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

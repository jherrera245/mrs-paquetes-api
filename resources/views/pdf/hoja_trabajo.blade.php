<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
    @page {
        size: letter;
        margin: 0cm 0cm;
    }

    body {
        margin-top: 3cm;
        margin-left: 1.5cm;
        margin-right: 1.5cm;
        margin-bottom: 0cm;
    }

    header {
        font-family: "Courier New", courier;
        position: fixed;
        top: 0.9cm;
        left: 0;
        right: 0;
        height: 3cm;
        text-align: center;
        page-break-inside: auto;
    }

    header h1 {
        font-size: 12pt;
    }

    header h2 {
        font-size: 8pt;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        page-break-inside: auto;
        transform: scale(0.97);
        transform-origin: 0 0;
    }

    table, th, td {
        border: 1px solid black;
    }

    th, td {
        font-family: "Courier New", courier;
        padding: 8px;
        height: auto;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    thead {
        display: table-header-group;
    }

    th {
        font-size: 7px;
        text-align: center;
    }

    td {
        font-size: 6px;
        text-align: left;
    }

    tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    footer {
        position: fixed;
        bottom: 0cm;
        left: 0cm;
        right: 0cm;
        height: 1cm;
    }
    </style>
</head>

<body>

    <header>
	<h1>Orden de trabajo</h1>
    </header>

    <div class="page" title="Page">
    <h1>Hoja de Trabajo</h1>
    <table>
        <thead>
            <tr>
                <th>UUID Paquete</th>
                <th>Descripción</th>
                <th>Destinatario</th>
                <th>Lugar de Entrega</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($asignaciones as $asignacion)
                <tr>
                    <td>{{ $asignacion->paquete->uuid }}</td>
                    <td>{{ $asignacion->paquete->descripcion_contenido }}</td>
                    <td>{{ $asignacion->paquete->cliente->nombre }} {{ $asignacion->paquete->cliente->apellido }}</td>
                    <td>{{ $asignacion->paquete->cliente->direccion }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

     </div>

</body>

</html>
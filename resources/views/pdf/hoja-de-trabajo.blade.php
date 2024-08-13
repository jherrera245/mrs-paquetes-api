<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hoja de Trabajo</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f4f4f4; }
        h1 { text-align: center; }
    </style>
</head>
<body>
    <h1>Hoja de Trabajo - Ruta</h1>
    <table>
        <thead>
            <tr>
                <th>Paquete UUID</th>
                <th>Tipo de Paquete</th>
                <th>Empaque</th>
                <th>Estado</th>
                <th>Descripción</th>
                <th>Destinatario</th>
                <th>Dirección de Entrega</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($asignaciones as $asignacion)
                <tr>
                    <td>{{ $asignacion->paquete->uuid }}</td>
                    <td>{{ $asignacion->paquete->tipoPaquete->nombre }}</td>
                    <td>{{ $asignacion->paquete->empaquetado->nombre }}</td>
                    <td>{{ $asignacion->paquete->estado->nombre }}</td>
                    <td>{{ $asignacion->paquete->descripcion_contenido }}</td>
                    <td>{{ $asignacion->paquete->Destinatario }}</td>
                    <td>{{ $asignacion->paquete->Dirección }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

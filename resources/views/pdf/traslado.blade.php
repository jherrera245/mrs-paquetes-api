<!DOCTYPE html>
<html>
<head>
    <title>Traslados PDF</title>
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            margin: 20px;
            padding: 0;
            color: #333;
            background-color: #f9f9f9;
        }
        .header {
            padding: 25px;
            border-bottom: 4px solid #2C3E50;
            background: #fff;
            text-align: center;
            margin-bottom: 40px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        .header img {
            position: absolute;
            top:20px;
            max-width: 100px;
            
        }
        .header h1 {
            margin: 5px 0;
            font-size: 24px;
            color: #2C3E50;
            font-weight: 700;
        }
        .header .fecha, .header .destino {
            font-size: 14px;
            color: #7F8C8D;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        table th, table td {
            border: 1px solid #BDC3C7;
            padding: 15px;
            text-align: left;
        }
        table th {
            background-color: #2980B9;
            color: #fff;
            font-weight: 700;
            text-align: center;
        }
        table tbody tr:nth-child(even) {
            background-color: #ECF0F1;
        }
        table tbody tr:hover {
            background-color: #BDC3C7;
        }
        table td[colspan="5"] {
            text-align: center;
            color: #7F8C8D;
            font-style: italic;
        }
    </style>
</head>
<body>
    
    <div class="header">
        <img src="/xampp/htdocs/mrs-paquetes-api/public/images/logo-claro.png" alt="Logo">
        <h1>Traslado de Paquetes</h1>
        <div class="fecha">Fecha: {{ $fecha }}</div>
        @if($single)
            <div class="destino">Destino: {{ $bodega_destino }}</div> 
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Código</th>
                <th>Descripción</th>
                <th>Bodega Destino</th>
                <th>Orden</th>
            </tr>
        </thead>
        <tbody>
            @php
                $counter = 1; // Inicializa el contador
            @endphp
            @if($single)
                @forelse($paquetes as $paquete)
                    <tr>
                        <td>{{ $counter++ }}</td> <!-- Usa el contador y lo incrementa -->
                        <td>{{ $numero_traslado }}</td>
                        <td>{{ $paquete->descripcion_contenido }}</td>
                        <td>{{ $bodega_destino }}</td>
                        <td>{{ $numero_seguimiento }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">No hay paquetes disponibles.</td>
                    </tr>
                @endforelse
            @else
                @forelse($traslados as $traslado)
                    @forelse($traslado->paquetes as $paquete)
                        <tr>
                            <td>{{ $counter++ }}</td> <!-- Usa el contador y lo incrementa -->
                            <td>{{ $traslado->numero_traslado }}</td>
                            <td>{{ $paquete->descripcion_contenido }}</td>
                            <td>{{ $traslado->bodega_destino_nombre }}</td>
                            <td>{{ $traslado->numero_seguimiento }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center;">No hay paquetes disponibles para este traslado.</td>
                        </tr>
                    @endforelse
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">No hay traslados disponibles.</td>
                    </tr>
                @endforelse
            @endif
        </tbody>
    </table>

  
</body>
</html>

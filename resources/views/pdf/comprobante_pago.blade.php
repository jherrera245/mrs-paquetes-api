<!DOCTYPE html>
<html>
<head>
    <title>Comprobante de pago</title>
</head>
<body>
    <h1>Comprobante de pago</h1>
    <p>Número de Factura: {{ $numeroFactura }}</p>
    <p>Fecha: {{ $fecha }}</p>
    
    <h2>Datos del Cliente</h2>
    <p>Nombre: {{ $cliente['nombre'] }}</p>
    <p>NIT: {{ $cliente['nit'] }}</p>
    <p>Dirección: {{ $cliente['direccion'] }}</p>

    <h2>Detalles de la Orden</h2>
    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Precio</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detalles as $detalle)
            <tr>
                <td>{{ $detalle['descripcion'] }}</td>
                <td>${{ number_format($detalle['precio'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Resumen</h2>
    <p>Subtotal: ${{ number_format($subtotal, 2) }}</p>
    <p>IVA (13%): ${{ number_format($iva, 2) }}</p>
    <p>Total a Pagar: ${{ number_format($total, 2) }}</p>

    <p>Método de Pago: {{ $metodoPago }}</p>
</body>
</html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante Consumidor Final</title>
    <style>
        @page {
            size: letter;
            margin: 1cm;
            font-family: Century;
            font-size: 9pt;
        }

        body {
            border: 3px solid #c0c0c0;
            border-radius: 15px;
            box-sizing: border-box;
        }

        h1{
            text-align: center;
            font-size: 10pt;
        }

        .container{
            margin: auto;
            padding: 10px;
            width: 96%;
        }

        table {
            width: 100%;
        }

        p {
            line-height: 1;
        }

        #header-table {
            margin-bottom: 0.5cm;
        }

        #details-document-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #c0c0c0;
            border-radius: 8px;
            margin-bottom: 1cm;
        }

        #details-document-table thead {
            background-color: #f0f0f0;
        }

        #details-document-table th,
        #details-document-table td {
            padding: 8px;
            border: 1px solid #c0c0c0;
        }

        #details-document-table thead th:first-child {
            border-top-left-radius: 8px;
        }

        #details-document-table thead th:last-child {
            border-top-right-radius: 8px;
        }

        #details-document-table tbody tr:last-child td:first-child {
            border-bottom-left-radius: 8px;
        }

        #details-document-table tbody tr:last-child td:last-child {
            border-bottom-right-radius: 8px;
        }

        #details-sale {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            border: 1px solid #c0c0c0;
        }

        #details-sale thead {
            background-color: #f0f0f0;
        }

        #details-sale th, #details-sale td {
            border: 1px solid #c0c0c0;
            padding: 8px;
        }

        #details-sale tfoot table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }

        #details-sale tfoot table td {
            border: 1px solid #c0c0c0;
            padding: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="data:image/p{{ $logo }}" alt="QR Code" style="width: 100px; height: 100px;">
        <h1>DOCUMENTO TRIBUTARIO ELECTRÓNICO FACTURA CONSUMIDOR FINAL</h1>
        <img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="QR Code" style="width: 100px; height: 100px;">
        <table id="header-table">
            <tr>
                <td style="width: 50%"><b>Código Generación</b>: {{$codigo_generacion}}</td>
                <td style="width: 50%"><b>Modelo de facturación</b>: Transmisión normal</td>
            </tr>
            <tr>
                <td style="width: 50%"><b>Número de Control</b>: {{$numero_control}}</td>
                <td style="width: 50%"><b>Tipo de transamisión</b>: Normal</td>
            </tr>
            <tr>
                <td style="width: 50%"><b>Sello de Recepción</b>: {{$sello_recepcion}}</td>
                <td style="width: 50%"><b>Fecha y Hora de Generación</b>:</td>
            </tr>
        </table>

        <table id="details-document-table">
            <thead>
                <tr>
                    <th style="width: 50%">EMISOR</th>
                    <th style="width: 50%">RECEPTOR</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 50%">
                        <p><b>Nombre</b>: Mrs. Paquetes de S.A de S.V</p>
                        <p><b>Correo electrónico</b>: soporte.ti@ mrspaquetes.com.sv</p>
                        <p><b>Dirección</b>: San Miguel</p>
                        <p><b>Teléfono</b>:25673383 &nbsp;&nbsp;&nbsp;<b>NRC</b>: 1234567</p>
                        <p><b>Actividad econoómica</b>: Servicio de entrega a domicilio de paquetes</p>
                    </td>
                    <td style="width: 50%">
                        <p><b>Nombre</b>: {{$cliente->nombre}} {{$cliente->apellido}}</p>
                        <p><b>Correo electrónico</b>: {{$cliente->email}}</p>
                        <p><b>Dirección</b>: {{$cliente->direccion}}</p>
                        <p><b>Teléfono</b>: {{$cliente->telefono}}</p>
                        <p><b>DUI</b>: {{$cliente->dui}}</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <table id="details-sale">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                    <th style="width: 35%">Descripción</th>
                    <th style="width: 14%">Precio Unitario</th>
                    <th style="width: 9.3%">Venta no sujeta</th>
                    <th style="width: 9.1%">Venta exenta</th>
                    <th style="width: 13.3%">Venta Gravada</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $item = 0;
                    $sumatoria = 0;
                    $iva = 0;
                    $subtotal = 1;
                @endphp
                @foreach ($detalles as $detalle)
                <tr>
                    <td>{{ $item++ }}</td>
                    <td>1</td>
                    <td>Paquete</td>
                    <td>{{ $detalle->descripcion }}</td>
                    <td>${{ number_format($detalle->precio + ($detalle->precio * 0.13), 2) }}</td>
                    <td>$0.00</td>
                    <td>$0.00</td>
                    <td>${{ number_format($detalle->precio + ($detalle->precio * 0.13), 2) }}</td>

                    @php
                        $subtotal += $detalle->precio;
                        $sumatoria += $detalle->precio + ($detalle->precio * 0.13);
                        $iva += ($detalle->precio * 0.13); 
                    @endphp
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4"></td>
                    <td colspan="4" style="padding: 0 !important;">
                        <table>
                            <tr>
                                <td>Suma de Ventas</td>
                                <td></td>
                                <td></td>
                                <td style="width: 25.5%">${{ number_format($sumatoria, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan=3>Sumatoria de ventas</td>
                                <td>${{ number_format($sumatoria, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan=3>Sub-Total</td>
                                <td>${{ number_format($subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan=3>IVA Retenido</td>
                                <td>${{ number_format($iva, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan=3>Monto Total de la Operación</td>
                                <td>${{ number_format($sumatoria, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan=3>Total a Pagar</td>
                                <td>${{ number_format($sumatoria, 2) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>
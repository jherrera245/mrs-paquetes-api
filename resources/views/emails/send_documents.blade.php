<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentos Tributarios Electrónicos</title>
</head>
<body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f7f4f1; color: #333;">
    <div style="width: 100%; max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); overflow: hidden;">
        <div style="background: linear-gradient(to right, #8c5e3c, #d4a373); padding: 10px; text-align: center; color: #ffffff;">
            <!-- Reemplaza la URL con la dirección correcta de tu logo -->
            <img src="https://lh3.googleusercontent.com/pw/AP1GczMxRBgFzxPm7L2411_EpgmXV24fcQ2Ys6iX1FHNmKTBrX_uJauNPH7JBgR76eNHsAXCwpQaxpGDf7cZVyK9cg9IlJgD1iv59255LySHFhYgGKZwI4U-s6t1eK8L79Mrxi_Ka0Y7ksZ3vFcFfDgijDv7=w919-h919-s-no-gm?authuser=0" alt="Logo de Mr. Paquetes" style="width: 150px; max-width: 100%;">
        </div>
        <div style="text-align: center; padding: 30px; color: #5a3f2a;">
            <p><strong>Estimado Cliente:</strong> {{ $cliente }}</p>
            <p>Le saludamos de <strong>Mr. Paquetes</strong> _{{ $cliente }}_, adjunto enviamos Documentos Tributarios Electrónicos.</p>
            <h2 style="color: #5a3f2a; font-size: 22px; border-bottom: 2px solid #5a3f2a; padding-bottom: 10px; margin-top: 20px;">Detalle del documento:</h2>
            <ul style="list-style: none; padding: 0; text-align: left; display: inline-block;">
                <li style="padding: 5px 0;"><strong>Número de Factura Electrónica:</strong> {{ $numero_control }}</li>
                <li style="padding: 5px 0;"><strong>Fecha:</strong> {{ $fecha }}</li>
                <li style="padding: 5px 0;"><strong>Tipo de documento tributario:</strong> {{ $tipo_documento }}</li>
                <li style="padding: 5px 0;"><strong>Monto:</strong> {{ $total_pagar }}</li>
            </ul>
            <p>Gracias por usar nuestros servicios.</p>
        </div>
        <div style="background: #f4e9e2; padding: 20px; text-align: center; color: #8c5e3c; font-size: 14px; border-top: 2px solid #8c5e3c;">
            <p style="margin: 0;">Correo generado automáticamente, por favor no responda a este mensaje.</p>
            <div style="margin-top: 10px;">
                <a href="#" style="color: #d4a373; text-decoration: none;">Política de Privacidad</a> | 
                <a href="#" style="color: #d4a373; text-decoration: none;">Términos y Condiciones</a>
            </div>
    </div>
</body>
</html>

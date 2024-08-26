@component('mail::message')
# Hola querido usuario

**Estimado Cliente:** {{ $cliente }}

Le saludamos de **Mr. Paquetes** _{{ $cliente }}_, adjunto enviamos Documentos Tributarios Electrónicos.

## Detalle del documento:

- **Número de Factura Electrónica:** {{ $numero_control }}
- **Fecha:** {{ $fecha }}
- **Tipo de documento tributario:** {{ $tipo_documento }}
- **Monto:** {{ $total_pagar }}

Gracias por usar nuestros servicios!!!.

_**Este es un correo generado automáticamente, por favor no responda a este mensaje.**_

Saludos,  
Mr. Paquetes
@endcomponent
<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\JsonResponse;

class VinetaController extends Controller
{
    public function generarVineta(Request $request, $id)
    {
        $orden = Orden::with(['cliente', 'direccion', 'detalles.paquete', 'tipoPago'])->findOrFail($id);

        // Generar el código QR
        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($orden->numero_seguimiento)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->size(200)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->build();

        $qrCodeBase64 = base64_encode($result->getString());

        // Determinar el mensaje de pago
        $mensajePago = $orden->estado_pago === 'pagado' 
            ? 'Paquete pagado' 
            : 'Cobrar a destinatario: $' . number_format($orden->total_pagar, 2);

        // Cargar la vista con los datos
        $pdf = PDF::loadView('pdf.vineta', compact('orden', 'qrCodeBase64', 'mensajePago'));

        $pdf->setPaper([0, 0, 475, 475]);

        $filename = 'vineta-' . $orden->numero_seguimiento . '.pdf';

        // Verificar si se solicita JSON o descarga directa
        if ($request->query('format') === 'json') {
            // Devolver JSON con el PDF en base64
            $pdfContent = $pdf->output();
            $pdfBase64 = base64_encode($pdfContent);

            return response()->json([
                'success' => true,
                'message' => 'Viñeta generada exitosamente',
                'data' => [
                    'pdf_base64' => $pdfBase64,
                    'filename' => $filename
                ]
            ]);
        } else {
            // Descargar el PDF directamente
            return $pdf->download($filename);
        }
    }
}
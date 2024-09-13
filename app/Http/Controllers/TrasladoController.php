<?php

namespace App\Http\Controllers;

use App\Models\Bodegas;
use App\Models\Destinos;
use App\Models\Rutas;
use App\Models\DetalleOrden;
use App\Models\Paquete;
use App\Models\Departamento;
use App\Models\Orden;
use App\Models\Kardex;
use App\Models\Traslado;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\KardexService;
use App\Models\DetalleTraslado;

class TrasladoController extends Controller
{
    /**
     * Listar traslados con filtros y paginación.
     */
    public function index(Request $request)
    {
        $traslados = Traslado::with(['bodegaOrigen', 'bodegaDestino', 'user'])
            ->paginate($request->input('per_page', 10));

        $trasladosFormatted = $traslados->getCollection()->map->getFormattedData();
        $traslados->setCollection($trasladosFormatted);

        return response()->json($traslados, 200);
    }


    public function show($id)
    {
        $traslado = Traslado::with(['bodegaOrigen', 'bodegaDestino', 'user'])->find($id);

        if (!$traslado) {
            return response()->json(['message' => 'Traslado no encontrado'], 404);
        }

        return response()->json($traslado->getFormattedData(), 200);
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bodega_origen' => 'required|exists:bodegas,id',
            'bodega_destino' => 'required|exists:bodegas,id',
            'paquetes' => 'required|array|min:1',
            'paquetes.*' => 'exists:paquetes,id',
            'codigos_qr' => 'nullable|array|min:1',
            'codigos_qr.*' => 'string|exists:paquetes,uuid',
            'fecha_traslado' => 'required|date',
        ], [
            'bodega_origen.required' => 'La bodega de origen es obligatoria.',
            'bodega_destino.required' => 'La bodega de destino es obligatoria.',
            'paquetes.required' => 'Debes seleccionar al menos un paquete.',
            'paquetes.*.exists' => 'Uno o más paquetes seleccionados no son válidos.',
            'codigos_qr.*.exists' => 'Uno o más códigos QR no son válidos.',
            'fecha_traslado.required' => 'La fecha de traslado es obligatoria.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Generar el número de traslado único para todos los paquetes
        $numeroTraslado = 'TR-' . str_pad(Traslado::max('id') + 1, 8, '0', STR_PAD_LEFT);

        DB::beginTransaction();

        try {
            // Obtener los datos generales que compartirán todos los paquetes
            $bodegaOrigen = $request->input('bodega_origen');
            $bodegaDestino = $request->input('bodega_destino');
            $fechaTraslado = $request->input('fecha_traslado');
            $userId = $request->input('user_id');

            // Instanciar el servicio KardexService
            $kardexService = new KardexService();

            // recibir los paquetes por id u opcionalmente por códigos QR.
            $paquetesIds = $request->input('paquetes', []);
            $codigosQr = $request->input('codigos_qr', []);

            // Convertir los códigos QR a sus respectivos IDs de paquetes y si existen, añadirlos al array de paquetes.
            if (!empty($codigosQr)) {
                $paquetesPorQr = Paquete::whereIn('uuid', $codigosQr)->pluck('id')->toArray();
                $paquetesIds = array_merge($paquetesIds, $paquetesPorQr);
            }

            // create traslado
            $traslado = Traslado::create([
                'bodega_origen' => $bodegaOrigen,
                'bodega_destino' => $bodegaDestino,
                'numero_traslado' => $numeroTraslado,
                'fecha_traslado' => $fechaTraslado,
                'estado' => 'Pendiente',
                'user_id' => Auth::user()->id
            ]);

            // Recorremos el array de los paquetes seleccionados
            foreach ($paquetesIds as $idPaquete) {
                // Registrar el detalle de traslado para cada paquete
                DetalleTraslado::create([
                    'id_traslado' => $traslado->id,
                    'id_paquete' => $idPaquete,
                    'estado' => 1 // Estado activo
                ]);

                // Obtener id_orden y numero_seguimiento a través de inner join
                $detalleOrdenInfo = $kardexService->getOrdenInfo($idPaquete);

                // Verificar si el detalle de la orden y número de seguimiento existen
                if (!$detalleOrdenInfo) {
                    throw new Exception("No se encontró información de la orden para el paquete ID: {$idPaquete}");
                }

                $idOrden = $detalleOrdenInfo->id_orden;
                $numeroSeguimiento = $detalleOrdenInfo->numero_seguimiento;

                // Registrar en Kardex
                // Salida de almacén
                $kardexService->registrarMovimientoKardex($idPaquete, $idOrden, 'SALIDA', 'AlMACENADO', $numeroSeguimiento);

                // Si la bodega de destino es la bodega principal (bodega_id = 1), registrar como ENTRADA
                if ($bodegaDestino == 1) {
                    $kardexService->registrarMovimientoKardex($idPaquete, $idOrden, 'ENTRADA', 'EN_ESPERA_REUBICACION', $numeroSeguimiento);
                } else {
                    // Entrada a traslado (cuando no es la bodega principal)
                    $kardexService->registrarMovimientoKardex($idPaquete, $idOrden, 'ENTRADA', 'TRASLADO', $numeroSeguimiento);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Traslado finalizado con éxito para los paquetes seleccionados.',
                'numero_traslado' => $numeroTraslado
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al finalizar el traslado: ' . $e->getMessage());
            return response()->json(['error' => 'Error al finalizar el traslado.', 'details' => $e->getMessage()], 500);
        }
    }



    /**
     * Actualizar un traslado existente.
     */
    public function update(Request $request, $id)
    {
        $traslado = Traslado::find($id);

        if (!$traslado) {
            return response()->json(['message' => 'Traslado no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'bodega_origen' => 'sometimes|required|exists:bodegas,id',
            'bodega_destino' => 'sometimes|required|exists:bodegas,id',
            'paquetes' => 'sometimes|array|min:1', // Para añadir más paquetes
            'paquetes.*' => 'exists:paquetes,id',
            'numero_traslado' => 'required|string|max:255|unique:traslados,numero_traslado,'.$id,
            'fecha_traslado' => 'sometimes|required|date',
            'estado' => 'sometimes|required|in:Activo,Inactivo',
            'user_id' => 'sometimes|required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // Actualizar la información existente del traslado
            $traslado->update($request->only([
                'bodega_origen',
                'bodega_destino',
                'fecha_traslado',
                'estado',
                'user_id'
            ]));

            $paquetesIds = $request->input('paquetes', []);

            // Añadir nuevos paquetes al traslado si se proporcionan
            foreach ($paquetesIds as $idPaquete) {
                // Verificar si el paquete ya está asignado a este traslado
                $existingTraslado = Traslado::where('id_paquete', $idPaquete)
                    ->where('numero_traslado', $traslado->numero_traslado)
                    ->first();

                if (!$existingTraslado) {
                    // Crear el traslado para el nuevo paquete
                    $nuevoTraslado = Traslado::create([
                        'bodega_origen' => $traslado->bodega_origen,
                        'bodega_destino' => $traslado->bodega_destino,
                        'id_paquete' => $idPaquete,
                        'numero_traslado' => $traslado->numero_traslado,
                        'fecha_traslado' => $traslado->fecha_traslado,
                        'estado' => $traslado->estado,
                        'user_id' => $traslado->user_id
                    ]);

                    // Actualizar Kardex para el nuevo paquete
                    $detalleOrden = DetalleOrden::where('id_paquete', $idPaquete)->first();
                    $numeroSeguimiento = Orden::where('id', $detalleOrden->id_orden)->value('numero_seguimiento');

                    // Salida de almacén o entrada a la bodega principal
                    $kardexSalida = new Kardex();
                    $kardexSalida->id_paquete = $idPaquete;
                    $kardexSalida->id_orden = $detalleOrden->id_orden;
                    $kardexSalida->cantidad = 1;
                    $kardexSalida->numero_ingreso = $numeroSeguimiento;
                    $kardexSalida->tipo_movimiento = 'SALIDA';
                    $kardexSalida->tipo_transaccion = $traslado->bodega_destino == 1 ? 'DEVOLUCION_RECOLECCION' : 'ASIGNADO_RUTA';
                    $kardexSalida->fecha = now();
                    $kardexSalida->save();

                    // Entrada a traslado
                    $kardexEntrada = new Kardex();
                    $kardexEntrada->id_paquete = $idPaquete;
                    $kardexEntrada->id_orden = $detalleOrden->id_orden;
                    $kardexEntrada->cantidad = 1;
                    $kardexEntrada->numero_ingreso = $numeroSeguimiento;
                    $kardexEntrada->tipo_movimiento = 'ENTRADA';
                    $kardexEntrada->tipo_transaccion = 'TRASLADO';
                    $kardexEntrada->fecha = now();
                    $kardexEntrada->save();
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Traslado actualizado y nuevos paquetes añadidos con éxito.',
                'data' => $traslado->getFormattedData()
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar traslado: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar traslado.'], 500);
        }
    }



    /**
     * Eliminar (marcar como inactivo) un traslado.
     */
    public function destroy($id)
    {
        $traslado = Traslado::find($id);

        if (!$traslado) {
            return response()->json(['message' => 'Traslado no encontrado'], 404);
        }

        try {
            $traslado->estado = 'Cancelado';
            $traslado->save();

            return response()->json(['message' => 'Traslado marcado como inactivo.'], 200);
        } catch (Exception $e) {
            Log::error('Error al marcar traslado como inactivo: ' . $e->getMessage());
            return response()->json(['error' => 'Error al marcar traslado como inactivo.'], 500);
        }
    }

    public function trasladoPdf($id = null){

    if ($id){
        // Generar PDF para un solo traslado
        $traslado = Traslado::findOrFail($id);

        // Obtener la bodega de origen y destino
        $bodegaOrigen = Bodegas::find($traslado->bodega_origen);
        $bodegaDestino = Bodegas::find($traslado->bodega_destino);

        $bodegaOrigenNombre = $bodegaOrigen ? $bodegaOrigen->nombre : 'Bodega de origen no disponible';
        $bodegaDestinoNombre = $bodegaDestino ? $bodegaDestino->nombre : 'Bodega de destino no disponible';

        // Obtener los detalles del traslado
        $detalleOrdenes = DetalleOrden::where('id_paquete', $traslado->id_paquete)->get();
        $paquetes = $detalleOrdenes->map(function($detalle) {
            return Paquete::find($detalle->id_paquete);
        })->filter();

        // Obtener el número de seguimiento de la orden
        $numeroSeguimiento = $detalleOrdenes->isNotEmpty() ? Orden::find($detalleOrdenes->first()->id_orden)->numero_seguimiento : 'Número de seguimiento no disponible';

        // Convertir fechas a objetos Carbon
        $fechaTraslado = $traslado->fecha_traslado ? Carbon::parse($traslado->fecha_traslado)->format('d/m/Y') : 'Fecha no disponible';

        // Preparar los datos para el PDF
        $data = [
                'fecha' => now()->format('d/m/Y'),
                'bodega_origen' => $bodegaOrigenNombre,
                'bodega_destino' => $bodegaDestinoNombre,
                'numero_traslado'=> $traslado->numero_traslado,
                'paquetes' => $paquetes,
                'numero_seguimiento' => $numeroSeguimiento,
                'fecha_traslado' => $fechaTraslado,
                'estado' => $traslado->estado,
                'single' => true
                ];

            // Generar el PDF para un solo traslado
            $pdf = Pdf::loadView('pdf.traslado', $data);
            $pdf->setPaper('A4', 'portrait');
            $pdfContent = $pdf->output();
            $base64Pdf = base64_encode($pdfContent);

            return response()->json([$base64Pdf]);

        }
        else {
            $traslados = Traslado::all();

            // Inicializa la colección de traslados
            $trasladosConDatos = $traslados->map(function($traslado) 
                {
                // Obtener la bodega de destino
                $bodegaDestino = Bodegas::find($traslado->bodega_destino);
                $traslado->bodega_destino_nombre = $bodegaDestino ? $bodegaDestino->nombre : 'Bodega de destino no disponible';
    
                // Obtener los detalles del traslado
                $detalleOrdenes = DetalleOrden::where('id_paquete', $traslado->id_paquete)->get();
                $paquetes = $detalleOrdenes->map(function($detalle) {
                    return Paquete::find($detalle->id_paquete);
                })->filter(); 
    
                // Obtener el número de seguimiento de la orden
                $numeroSeguimiento = $detalleOrdenes->isNotEmpty() ? Orden::find($detalleOrdenes->first()->id_orden)->numero_seguimiento : 'Número de seguimiento no disponible';
    
                // Convertir fechas a objetos Carbon
                $fechaTraslado = $traslado->fecha_traslado ? Carbon::parse($traslado->fecha_traslado)->format('d/m/Y') : 'Fecha no disponible';
                $traslado->fecha_traslado_formatted = $fechaTraslado;
                $traslado->paquetes = $paquetes;
                $traslado->numero_seguimiento = $numeroSeguimiento;
                $traslado->numero_traslados = $traslado;
    
                return $traslado;
            });
    
            $data = [
                'fecha' => now()->format('d/m/Y'),
                'traslados' => $trasladosConDatos,
                'single' => false
            ];
    
            // Generar el PDF para múltiples traslados
            $pdf = Pdf::loadView('pdf.traslado', $data);
            $pdf->setPaper('A4', 'portrait');
            $pdfContent = $pdf->output();
            $base64Pdf = base64_encode($pdfContent);

            return response()->json([$base64Pdf]);
        }
    }
}

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
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\KardexService;
use App\Models\DetalleTraslado;
use App\Models\UbicacionPaquete;
use Endroid\QrCode\QrCode;

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
        // Buscar el traslado con sus detalles
        $traslado = Traslado::with(['detalleTraslado.paquete'])->find($id);

        if (!$traslado) {
            return response()->json(['message' => 'Traslado no encontrado'], 404);
        }

        // Formatear la respuesta para mostrar el UUID de los paquetes asociados
        // mostrar solo paquetes activos, estado = 1.
        $detalleTraslado = $traslado->detalleTraslado->where('estado', 1);
        $detallesPaquetes = $detalleTraslado->map(function ($detalle) {
            return [
                'id' => $detalle->id,
                'id_paquete' => $detalle->id_paquete,
                'uuid' => $detalle->paquete->uuid // Acceder al UUID del paquete
            ];
        });
        

        return response()->json([
            'id_traslado' => $traslado->id,
            'numero_traslado' => $traslado->numero_traslado,
            'bodega_origen' => $traslado->bodega_origen,
            'bodega_destino' => $traslado->bodega_destino,
            'fecha_traslado' => $traslado->fecha_traslado,
            'estado' => $traslado->estado,
            'paquetes' => $detallesPaquetes
        ], 200);
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bodega_origen' => 'required|exists:bodegas,id',
            'bodega_destino' => 'required|exists:bodegas,id',
            'paquetes' => 'nullable|array|min:1',
            'paquetes.*' => 'exists:paquetes,id',
            'codigos_qr' => 'nullable|array|min:1',
            'codigos_qr.*' => 'string|exists:paquetes,uuid',
            'fecha_traslado' => 'required|date',
        ], [
            'bodega_origen.required' => 'La bodega de origen es obligatoria.',
            'bodega_destino.required' => 'La bodega de destino es obligatoria.',
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
            $codigosQr = $request->input('codigos_qr', []);

            // Convertir los códigos QR a sus respectivos IDs de paquetes y si existen, añadirlos al array de paquetes.
            if (!empty($codigosQr)) {
                $paquetesPorQr = Paquete::whereIn('uuid', $codigosQr)->pluck('id')->toArray();
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
            foreach ($paquetesPorQr as $idPaquete) {
                // buscamos si el paquete estaba asignado a una ubicacion.
                $ubicacion = UbicacionPaquete::where('id_paquete', $idPaquete)->first();
                // si el paquete tiene una ubicacion, se borra del registro de ubicacion.
                if ($ubicacion) {
                    $ubicacion->delete();
                }
                
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
                    $kardexService->registrarMovimientoKardex($idPaquete, $idOrden, 'ENTRADA', 'EN_ESPERA_UBICACION', $numeroSeguimiento);

                    // el estado del paquete cambia al 14 => EN ESPERA DE UBICACION.
                    Paquete::where('id', $idPaquete)->update(['id_estado_paquete' => 14]);
                } else {
                    // Entrada a traslado (cuando no es la bodega principal)
                    $kardexService->registrarMovimientoKardex($idPaquete, $idOrden, 'ENTRADA', 'TRASLADO', $numeroSeguimiento);

                    // el estado del paquete cambia al 5 => EN RUTA DE ENTREGA.
                    Paquete::where('id', $idPaquete)->update(['id_estado_paquete' => 5]);
                    // cambia la el id_ubicacion de la tabla paquetes.
                    Paquete::where('id', $idPaquete)->update(['id_ubicacion' => $bodegaDestino]);
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
    // public function update(Request $request, $id)
    // {
    //     $traslado = Traslado::find($id);

    //     if (!$traslado) {
    //         return response()->json(['message' => 'Traslado no encontrado'], 404);
    //     }

    //     $validator = Validator::make($request->all(), [
    //         'bodega_origen' => 'sometimes|required|exists:bodegas,id',
    //         'bodega_destino' => 'sometimes|required|exists:bodegas,id',
    //         'paquetes' => 'sometimes|array|min:1', // Para añadir más paquetes
    //         'paquetes.*' => 'exists:paquetes,id',
    //         'numero_traslado' => 'required|string|max:255|unique:traslados,numero_traslado,'.$id,
    //         'fecha_traslado' => 'sometimes|required|date',
    //         'estado' => 'sometimes|required|in:Activo,Inactivo',
    //         'user_id' => 'sometimes|required|exists:users,id'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     DB::beginTransaction();

    //     try {
    //         // Actualizar la información existente del traslado
    //         $traslado->update($request->only([
    //             'bodega_origen',
    //             'bodega_destino',
    //             'fecha_traslado',
    //             'estado',
    //             'user_id'
    //         ]));

    //         $paquetesIds = $request->input('paquetes', []);

    //         // Añadir nuevos paquetes al traslado si se proporcionan
    //         foreach ($paquetesIds as $idPaquete) {
    //             // Verificar si el paquete ya está asignado a este traslado
    //             $existingTraslado = Traslado::where('id_paquete', $idPaquete)
    //                 ->where('numero_traslado', $traslado->numero_traslado)
    //                 ->first();

    //             if (!$existingTraslado) {
    //                 // Crear el traslado para el nuevo paquete
    //                 $nuevoTraslado = Traslado::create([
    //                     'bodega_origen' => $traslado->bodega_origen,
    //                     'bodega_destino' => $traslado->bodega_destino,
    //                     'id_paquete' => $idPaquete,
    //                     'numero_traslado' => $traslado->numero_traslado,
    //                     'fecha_traslado' => $traslado->fecha_traslado,
    //                     'estado' => $traslado->estado,
    //                     'user_id' => $traslado->user_id
    //                 ]);

    //                 // Actualizar Kardex para el nuevo paquete
    //                 $detalleOrden = DetalleOrden::where('id_paquete', $idPaquete)->first();
    //                 $numeroSeguimiento = Orden::where('id', $detalleOrden->id_orden)->value('numero_seguimiento');

    //                 // Salida de almacén o entrada a la bodega principal
    //                 $kardexSalida = new Kardex();
    //                 $kardexSalida->id_paquete = $idPaquete;
    //                 $kardexSalida->id_orden = $detalleOrden->id_orden;
    //                 $kardexSalida->cantidad = 1;
    //                 $kardexSalida->numero_ingreso = $numeroSeguimiento;
    //                 $kardexSalida->tipo_movimiento = 'SALIDA';
    //                 $kardexSalida->tipo_transaccion = $traslado->bodega_destino == 1 ? 'DEVOLUCION_RECOLECCION' : 'ASIGNADO_RUTA';
    //                 $kardexSalida->fecha = now();
    //                 $kardexSalida->save();

    //                 // Entrada a traslado
    //                 $kardexEntrada = new Kardex();
    //                 $kardexEntrada->id_paquete = $idPaquete;
    //                 $kardexEntrada->id_orden = $detalleOrden->id_orden;
    //                 $kardexEntrada->cantidad = 1;
    //                 $kardexEntrada->numero_ingreso = $numeroSeguimiento;
    //                 $kardexEntrada->tipo_movimiento = 'ENTRADA';
    //                 $kardexEntrada->tipo_transaccion = 'TRASLADO';
    //                 $kardexEntrada->fecha = now();
    //                 $kardexEntrada->save();
    //             }
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'message' => 'Traslado actualizado y nuevos paquetes añadidos con éxito.',
    //             'data' => $traslado->getFormattedData()
    //         ], 200);
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         Log::error('Error al actualizar traslado: ' . $e->getMessage());
    //         return response()->json(['error' => 'Error al actualizar traslado.'], 500);
    //     }
    // }

    public function update(Request $request, $id)
    {
        // Validar los datos de entrada, solo recibimos los códigos QR
        $validator = Validator::make($request->all(), [
            'codigos_qr' => 'required|array|min:1',
            'codigos_qr.*' => 'string|exists:paquetes,uuid',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // Buscar el traslado a actualizar
            $traslado = Traslado::findOrFail($id);

            // Instanciar el servicio KardexService
            $kardexService = new KardexService();

            // Recibir los paquetes por sus códigos QR (UUID)
            $codigosQr = $request->input('codigos_qr', []);

            // Convertir los códigos QR a sus respectivos IDs de paquetes
            $paquetesPorQr = Paquete::whereIn('uuid', $codigosQr)->pluck('id')->toArray();

            // Agregar nuevos paquetes
            foreach ($paquetesPorQr as $idPaquete) {
                // Verificar si el paquete ya está asignado a este traslado.
                $existingTraslado = DetalleTraslado::where('id_paquete', $idPaquete)
                    ->where('id_traslado', $traslado->id)
                    ->where('estado', 1) // Solo paquetes activos
                    ->first();
                
                if ($existingTraslado) {
                    // si el paquete esta asignado, mostrar mensaje de error.
                    throw new Exception("El paquete con ID: {$idPaquete} ya está asignado a este traslado.");
                }

                //verificamos la ubicacion del paquete.
                $ubicacion = UbicacionPaquete::where('id_paquete', $idPaquete)->first();
                // si el paquete tiene una ubicacion, se borra del registro de ubicacion.
                if ($ubicacion) {
                    $ubicacion->delete();
                }
                
                // Registrar el detalle de traslado para cada paquete
                DetalleTraslado::create([
                    'id_traslado' => $traslado->id,
                    'id_paquete' => $idPaquete,
                    'estado' => 1 // Estado activo
                ]);

                // Obtener id_orden y numero_seguimiento a través del servicio KardexService
                $detalleOrdenInfo = $kardexService->getOrdenInfo($idPaquete);

                if (!$detalleOrdenInfo) {
                    throw new Exception("No se encontró información de la orden para el paquete ID: {$idPaquete}");
                }

                $idOrden = $detalleOrdenInfo->id_orden;
                $numeroSeguimiento = $detalleOrdenInfo->numero_seguimiento;

                // Registrar movimientos en el Kardex
                $kardexService->registrarMovimientoKardex($idPaquete, $idOrden, 'SALIDA', 'AlMACENADO', $numeroSeguimiento);

                if ($traslado->bodega_destino == 1) {
                    $kardexService->registrarMovimientoKardex($idPaquete, $idOrden, 'ENTRADA', 'EN_ESPERA_UBICACION', $numeroSeguimiento);
                    Paquete::where('id', $idPaquete)->update(['id_estado_paquete' => 14]);
                } else {
                    $kardexService->registrarMovimientoKardex($idPaquete, $idOrden, 'ENTRADA', 'TRASLADO', $numeroSeguimiento);
                    Paquete::where('id', $idPaquete)->update(['id_estado_paquete' => 5]);
                    Paquete::where('id', $idPaquete)->update(['id_ubicacion' => $traslado->bodega_destino]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Traslado actualizado con éxito. Paquetes añadidos.',
                'numero_traslado' => $traslado->numero_traslado
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar el traslado: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar el traslado.', 'details' => $e->getMessage()], 500);
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

    // destroy para el detalle de un traslado -> pasar el paquete a inactivo.
    public function destroyDetail($id)
    {
        $detalleTraslado = DetalleTraslado::find($id);

        if (!$detalleTraslado) {
            return response()->json(['message' => 'Detalle de traslado no encontrado'], 404);
        }

        // si se elimina un detalle de traslado, se tiene que borrar el registro del kardex con el services.
         // Instanciar el servicio KardexService
        $kardexService = new KardexService();
        // registrar salida de traslado.

        $detalleOrdenInfo = $kardexService->getOrdenInfo($detalleTraslado->id_paquete);

        if (!$detalleOrdenInfo) {
            throw new Exception("No se encontró información de la orden para el paquete ID: {$detalleTraslado->id_paquete}");
        }

        $idOrden = $detalleOrdenInfo->id_orden;
        $numeroSeguimiento = $detalleOrdenInfo->numero_seguimiento;


        $kardexService->registrarMovimientoKardex($detalleTraslado->id_paquete, $idOrden, 'SALIDA', 'TRASLADO', $numeroSeguimiento);
        // cambiar estado de paquete en espera de ubicacion.
        Paquete::where('id', $detalleTraslado->id_paquete)->update(['id_estado_paquete' => 14]);
        // registrar entrada al kardex en espera de ubicacion.
        $kardexService->registrarMovimientoKardex($detalleTraslado->id_paquete, $idOrden, 'ENTRADA', 'EN_ESPERA_UBICACION', $numeroSeguimiento);

        try {
            $detalleTraslado->estado = 0;
            $detalleTraslado->save();

            return response()->json(['message' => 'Detalle de traslado marcado como inactivo.'], 200);
        } catch (Exception $e) {
            Log::error('Error al marcar detalle de traslado como inactivo: ' . $e->getMessage());
            return response()->json(['error' => 'Error al marcar detalle de traslado como inactivo.'], 500);
        }
    }

    public function trasladoPdf($id = null) {
        try {
            if ($id) {
                // Generar PDF para un solo traslado
                $traslado = DetalleTraslado::find($id);

                // Obtener el traslado relacionado para obtener la bodega de destino
                $trasladoInfo = Traslado::find($traslado->id_traslado);
                $bodegaDestinoNombre = $trasladoInfo ? Bodegas::find($trasladoInfo->bodega_destino)->nombre : 'No disponible';
                $bodegaOrigenNombre = $trasladoInfo ? Bodegas::find($trasladoInfo->bodega_origen)->nombre: 'No disponible';
                // Obtener todos los detalles de orden relacionados con este traslado
                $detalleOrdenes = DetalleOrden::whereIn('id_paquete', function($query) use ($traslado) {
                    $query->select('id_paquete')
                        ->from('detalle_traslado')
                        ->where('id_traslado', $traslado->id)
                        ->where('estado', 1); // Solo incluir paquetes con estado 1
                })->get();
    
                $paquetes = $detalleOrdenes->map(function($detalle) {
                    return Paquete::find($detalle->id_paquete);
                })->filter();
    
                // Obtener el número de seguimiento de la orden
                $numeroSeguimiento = $detalleOrdenes->isNotEmpty() ? Orden::find($detalleOrdenes->first()->id_orden)->numero_seguimiento : 'No disponible';
    
                // Formatear la fecha de traslado
                $fechaTraslado = $traslado->created_at ? Carbon::parse($traslado->created_at)->format('d/m/Y') : 'No disponible';
    
                // Generar QR para cada paquete
                $qrCodes = []; 
                foreach ($paquetes as $paquete) {
                    $qrCode = new QrCode($paquete->uuid); 
                    $result = (new PngWriter())->write($qrCode);
                    $qrCodes[] = base64_encode($result->getString());
                }
    
                // Preparar los datos para el PDF
                $data = [
                    'fecha' => now()->format('d/m/Y'),
                    'bodega_destino' => $bodegaDestinoNombre,
                    'bodega_origen' => $bodegaOrigenNombre,
                    'paquetes' => $paquetes,
                    'numero_seguimiento' => $numeroSeguimiento,
                    'fecha_traslado' => $fechaTraslado,
                    'single' => true,
                    'qrCodes' => $qrCodes,
                ];
    
                // Generar el PDF
                $pdf = Pdf::loadView('pdf.traslado', $data);
                $pdfBase64 = base64_encode($pdf->output());
        
                return response()->json([$pdfBase64]);
            } else {

                // Manejo de múltiples traslados
                $traslados = DetalleTraslado::get(); 
                $trasladosConDatos = $traslados->map(function($traslado) {
                    $trasladoInfo = Traslado::find($traslado->id_traslado);
                    $bodegaDestinoNombre = $trasladoInfo ? Bodegas::find($trasladoInfo->bodega_destino)->nombre : 'No disponible';
                    $bodegaOrigenNombre = $trasladoInfo ? Bodegas::find($trasladoInfo->bodega_origen)->nombre : 'No disponible';
                    $traslado->bodega_destino_nombre = $bodegaDestinoNombre;
                    $traslado->bodega_origen_nombre = $bodegaOrigenNombre;

                    // Obtener los detalles de orden relacionados con este traslado
                    $detalleOrdenes = DetalleOrden::whereIn('id_paquete', function($query) use ($traslado) {
                        $query->select('id_paquete')
                            ->from('detalle_traslado')
                            ->where('id_traslado', $traslado->id)
                            ->where('estado', 1); // Solo incluir paquetes con estado 1
                    })->get();
                    
                    $traslado->paquetes = $detalleOrdenes->map(function($detalle) {
                        return Paquete::find($detalle->id_paquete);
                    })->filter();
    

                // Obtener el número de seguimiento de la orden para este traslado
                $traslado->numero_seguimiento = $detalleOrdenes->isNotEmpty() ? Orden::find($detalleOrdenes->first()->id_orden)->numero_seguimiento : 'No disponible';

                return $traslado;
                    return $traslado;
                });
    
                // Generar QR para cada paquete
                foreach ($trasladosConDatos as $traslado) {
                    foreach ($traslado->paquetes as $paquete) {
                        $uuid = $paquete->uuid; // Usar el UUID del paquete
                        $qrCode = new QrCode($uuid);
                        $result = (new PngWriter())->write($qrCode);
                        $paquete->qrCode = base64_encode($result->getString());
                    }
                }
    
                $data = [
                    'fecha' => now()->format('d/m/Y'),
                    'traslados' => $trasladosConDatos,
                    'single' => false
                ];
    
                // Generar el PDF
                $pdf = Pdf::loadView('pdf.traslado', $data);
                $pdfBase64 = base64_encode($pdf->output());
        
                return response()->json([$pdfBase64]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }    
    
    public function trasladoPdfGeneral() {
        try {
            // Obtener todos los traslados únicos
            $traslados = DetalleTraslado::select('id_traslado')->distinct()->get();
    
            // Preparar datos para cada traslado
            $trasladosConDatos = $traslados->map(function($traslado) {
                // Obtener información del traslado
                $trasladoInfo = Traslado::find($traslado->id_traslado);
                $bodegaDestinoNombre = $trasladoInfo ? Bodegas::find($trasladoInfo->bodega_destino)->nombre : 'No disponible';
                $bodegaOrigenNombre = $trasladoInfo ? Bodegas::find($trasladoInfo->bodega_origen)->nombre : 'No disponible';
    
                // Obtener el número de seguimiento de la orden
                $detalleOrden = DetalleOrden::whereIn('id_paquete', function($query) use ($traslado) {
                    $query->select('id_paquete')
                        ->from('detalle_traslado')
                        ->where('id_traslado', $traslado->id_traslado)
                        ->where('estado', 1);
                })->first();
    
                $numeroSeguimiento = $detalleOrden ? Orden::find($detalleOrden->id_orden)->numero_seguimiento : 'No disponible';
    
                // Contar los id_paquete en detalle_traslado relacionados con el traslado y la orden
                $cantidadPaquetes = DetalleTraslado::where('id_traslado', $traslado->id_traslado)
                    ->whereIn('id_paquete', function($query) use ($detalleOrden) {
                        $query->select('id_paquete')
                            ->from('detalle_orden')
                            ->where('id_orden', $detalleOrden ? $detalleOrden->id_orden : null);
                    })
                    ->where('estado', 1) // Solo contar los activos
                    ->count('id_paquete');
    
                return [
                    'bodega_origen' => $bodegaOrigenNombre,
                    'bodega_destino' => $bodegaDestinoNombre,
                    'orden' => $numeroSeguimiento,
                    'cantidad_paquetes' => $cantidadPaquetes,
                ];
            });
    
            // Preparar los datos para el PDF
            $data = [
                'fecha' => now()->format('d/m/Y'),
                'traslados' => $trasladosConDatos,
            ];
    
            // Generar el PDF
            $pdf = Pdf::loadView('pdf.traslado_general', $data);
    
            $pdfBase64 = base64_encode($pdf->output());
        
            return response()->json([$pdfBase64]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    
    
    
}

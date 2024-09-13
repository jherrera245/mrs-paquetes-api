<?php

namespace App\Http\Controllers;

use App\Models\Bodegas;
use App\Models\Destinos;
use App\Models\Rutas;
use App\Models\DetalleOrden;
use App\Models\Paquete;
use App\Models\Departamento;
use App\Models\Orden;
use App\Models\Traslado;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TrasladoController extends Controller
{
    /**
     * Listar traslados con filtros y paginación.
     */
    public function index(Request $request)
    {
        $traslados = Traslado::with(['bodegaOrigen', 'bodegaDestino', 'paquete', 'user'])
            ->paginate($request->input('per_page', 10));

        $trasladosFormatted = $traslados->getCollection()->map->getFormattedData();
        $traslados->setCollection($trasladosFormatted);

        return response()->json($traslados, 200);
    }


    public function show($id)
    {
        $traslado = Traslado::with(['bodegaOrigen', 'bodegaDestino', 'paquete', 'user'])->find($id);

        if (!$traslado) {
            return response()->json(['message' => 'Traslado no encontrado'], 404);
        }

        return response()->json($traslado->getFormattedData(), 200);
    }



    public function store(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'bodega_origen' => 'required|exists:bodegas,id',
            'bodega_destino' => 'required|exists:bodegas,id',
            'paquetes' => 'required|array|min:1',
            'paquetes.*' => 'exists:paquetes,id',
            'fecha_traslado' => 'required|date',
            'user_id' => 'required|exists:users,id'
        ], [
            'bodega_origen.required' => 'La bodega de origen es obligatoria.',
            'bodega_destino.required' => 'La bodega de destino es obligatoria.',
            'paquetes.required' => 'Debes seleccionar al menos un paquete.',
            'paquetes.*.exists' => 'Uno o más paquetes seleccionados no son válidos.',
            'fecha_traslado.required' => 'La fecha de traslado es obligatoria.',
            'user_id.exists' => 'El usuario que realiza el traslado no es válido.'
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

            // Recorrer el array de paquetes para registrar cada traslado
            foreach ($request->input('paquetes') as $idPaquete) {
                // Registrar el traslado de cada paquete
                $traslado = Traslado::create([
                    'bodega_origen' => $bodegaOrigen,
                    'bodega_destino' => $bodegaDestino,
                    'id_paquete' => $idPaquete,
                    'numero_traslado' => $numeroTraslado,
                    'fecha_traslado' => $fechaTraslado,
                    'estado' => 'Pendiente',
                    'user_id' => $userId
                ]);

                // Crear la entrada y salida en Kardex para cada paquete
                $detalleOrden = DetalleOrden::where('id_paquete', $idPaquete)->first();
                $numeroSeguimiento = Orden::where('id', $detalleOrden->id_orden)->value('numero_seguimiento');

                // Salida de almacén
                $kardexSalida = new Kardex();
                $kardexSalida->id_paquete = $idPaquete;
                $kardexSalida->id_orden = $detalleOrden->id_orden;
                $kardexSalida->cantidad = 1;
                $kardexSalida->numero_ingreso = $numeroSeguimiento;
                $kardexSalida->tipo_movimiento = 'SALIDA';
                $kardexSalida->tipo_transaccion = 'ASIGNADO_RUTA';
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
            'id_paquete' => 'sometimes|required|exists:paquetes,id',
            'numero_traslado' => 'sometimes|required|string|max:255|unique:traslados,numero_traslado,'.$id,
            'fecha_traslado' => 'sometimes|required|date',
            'estado' => 'sometimes|required|in:Activo,Inactivo',
            'user_id' => 'sometimes|required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $traslado->update($request->all());

            return response()->json([
                'message' => 'Traslado actualizado con éxito.',
                'data' => $traslado->getFormattedData()
            ], 200);
        } catch (Exception $e) {
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

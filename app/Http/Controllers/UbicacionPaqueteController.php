<?php

namespace App\Http\Controllers;

use App\Models\UbicacionPaquete;
use App\Models\Paquete;
use App\Models\Kardex;
use App\Models\DetalleOrden;
use App\Models\Orden;
use App\Models\Ubicacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UbicacionPaqueteResource;
use Illuminate\Support\Facades\Validator;
use Exception;



class UbicacionPaqueteController extends Controller
{
    /**
     * Listar todas las relaciones de ubicaciones con paquetes con paginación y filtros.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Obtener todos los registros con los campos solicitados
            $resultados = DB::table('detalle_orden')
                ->join('ordenes', 'detalle_orden.id_orden', '=', 'ordenes.id')
                ->join('paquetes', 'detalle_orden.id_paquete', '=', 'paquetes.id')
                ->join('ubicaciones_paquetes', 'detalle_orden.id_paquete', '=', 'ubicaciones_paquetes.id_paquete')
                ->join('ubicaciones', 'ubicaciones_paquetes.id_ubicacion', '=', 'ubicaciones.id')
                ->where('ubicaciones_paquetes.estado', '!=', 0)
                ->select(
                    'ubicaciones_paquetes.id AS id',
                    'ordenes.numero_seguimiento AS numero_orden',
                    'paquetes.uuid AS qr_paquete',
                    'paquetes.descripcion_contenido AS descripcion_paquete',
                    'ubicaciones.nomenclatura AS nomenclatura_ubicacion'
                )
                ->get();

            return response()->json($resultados, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los detalles', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar una relación específica de ubicación con paquete.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function index_filtro(Request $request)
    {
        try {
            // Obtener los filtros desde la solicitud
            $filtros = $request->only(['numero_orden', 'qr_paquete', 'descripcion_paquete', 'nomenclatura_ubicacion']);

            // Iniciar la consulta
            $query = DB::table('detalle_orden')
                ->join('ordenes', 'detalle_orden.id_orden', '=', 'ordenes.id')
                ->join('paquetes', 'detalle_orden.id_paquete', '=', 'paquetes.id')
                ->join('ubicaciones_paquetes', 'detalle_orden.id_paquete', '=', 'ubicaciones_paquetes.id_paquete')
                ->join('ubicaciones', 'ubicaciones_paquetes.id_ubicacion', '=', 'ubicaciones.id')
                ->where('ubicaciones_paquetes.estado', '!=', 0);

            // Aplicar filtros si están presentes
            if (!empty($filtros['numero_orden'])) {
                $query->where('ordenes.numero_seguimiento', 'LIKE', '%' . $filtros['numero_orden'] . '%');
            }

            if (!empty($filtros['qr_paquete'])) {
                $query->where('paquetes.uuid', 'LIKE', '%' . $filtros['qr_paquete'] . '%');
            }

            if (!empty($filtros['descripcion_paquete'])) {
                $query->where('paquetes.descripcion_contenido', 'LIKE', '%' . $filtros['descripcion_paquete'] . '%');
            }

            if (!empty($filtros['nomenclatura_ubicacion'])) {
                $query->where('ubicaciones.nomenclatura', 'LIKE', '%' . $filtros['nomenclatura_ubicacion'] . '%');
            }

            // Obtener los resultados filtrados
            $resultados = $query->select(
                'ubicaciones_paquetes.id AS id',
                'ordenes.numero_seguimiento AS numero_orden',
                'paquetes.uuid AS qr_paquete',
                'paquetes.descripcion_contenido AS descripcion_paquete',
                'ubicaciones.nomenclatura AS nomenclatura_ubicacion'
            )
                ->get();

            return response()->json($resultados, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al filtrar los detalles', 'details' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            // Obtener el registro específico usando el ID de la tabla ubicaciones_paquetes
            $resultado = DB::table('ubicaciones_paquetes')
                ->join('detalle_orden', 'detalle_orden.id_paquete', '=', 'ubicaciones_paquetes.id_paquete')
                ->join('ordenes', 'detalle_orden.id_orden', '=', 'ordenes.id')
                ->join('paquetes', 'detalle_orden.id_paquete', '=', 'paquetes.id')
                ->join('ubicaciones', 'ubicaciones_paquetes.id_ubicacion', '=', 'ubicaciones.id')
                ->where('ubicaciones_paquetes.id', $id) // Usamos el ID de la tabla ubicaciones_paquetes
                ->where('ubicaciones_paquetes.estado', '!=', 0) // Solo ubicaciones activas
                ->select(
                    'ubicaciones_paquetes.id AS id',
                    'ordenes.numero_seguimiento AS numero_orden',
                    'paquetes.uuid AS qr_paquete',
                    'paquetes.descripcion_contenido AS descripcion_paquete',
                    'ubicaciones.nomenclatura AS nomenclatura_ubicacion'
                )
                ->first();

            if (!$resultado) {
                return response()->json(['error' => 'Registro no encontrado'], 404);
            }

            return response()->json($resultado, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener el registro', 'details' => $e->getMessage()], 500);
        }
    }


    /**
     * Crear una nueva relación de ubicación con paquete.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validación de la entrada
        $validator = Validator::make($request->all(), [
            'codigo_qr_paquete' => 'required|string|exists:paquetes,uuid',
            'codigo_nomenclatura_ubicacion' => 'required|string|exists:ubicaciones,nomenclatura',
        ], [
            'codigo_qr_paquete.required' => 'El campo de código del paquete es obligatorio.',
            'codigo_qr_paquete.exists' => 'El paquete con ese código no es válido.',
            'codigo_nomenclatura_ubicacion.required' => 'El campo de ubicación es obligatorio.',
            'codigo_nomenclatura_ubicacion.exists' => 'La ubicación seleccionada no es válida.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        DB::beginTransaction(); // Iniciar transacción

        try {
            // Buscar el paquete por el UUID escaneado (el código QR del paquete)
            $paquete = Paquete::where('uuid', $request->codigo_qr_paquete)->firstOrFail();

            // si el paquete se encuentra en un estado diferente a 14 (En espera de ubicación) no se puede asignar una ubicación.
            if ($paquete->estado->id != 14) {
                return response()->json(['error' => 'El paquete no está en estado "En espera de ubicación".'], 400);
            }

            // Buscar la ubicación por la nomenclatura escaneada (el código de la ubicación)
            $ubicacion = Ubicacion::where('nomenclatura', $request->codigo_nomenclatura_ubicacion)->firstOrFail();
            
            // Verificar si ya existe una relación de ubicación para el paquete con la misma ubicación
            $existingUbicacionPaquete = UbicacionPaquete::where('id_paquete', $paquete->id)
                ->where('id_ubicacion', $ubicacion->id)
                ->first();

            if ($existingUbicacionPaquete) {
                return response()->json(['error' => 'Esta ubicación ya está asignada a este paquete.'], 400);
            }

            // **Agregar los movimientos en el Kardex**
            $detalleOrden = DetalleOrden::where('id_paquete', $paquete->id)->first();
            if (!$detalleOrden) {
                throw new Exception('Detalle de orden no encontrado para el paquete.');
            }

            $numeroSeguimiento = Orden::where('id', $detalleOrden->id_orden)->value('numero_seguimiento');
            if (!$numeroSeguimiento) {
                throw new Exception('Número de seguimiento no encontrado para la orden.');
            }

            // Si el estado del paquete es 1 (Recibido de recepción) se hace una salida de recepción a almacenado.
            if ($paquete->id_estado_paquete == 1) {
                // 1. **SALIDA de RECEPCION**
                $kardexSalida = new Kardex();
                $kardexSalida->id_paquete = $paquete->id;
                $kardexSalida->id_orden = $detalleOrden->id_orden;
                $kardexSalida->cantidad = 1;
                $kardexSalida->numero_ingreso = $numeroSeguimiento;
                $kardexSalida->tipo_movimiento = 'SALIDA';
                $kardexSalida->tipo_transaccion = 'RECEPCION';
                $kardexSalida->fecha = now();
                $kardexSalida->save();
            } else {
                // 1. **SALIDA de RECOLECTADO**
                $kardexSalida = new Kardex();
                $kardexSalida->id_paquete = $paquete->id;
                $kardexSalida->id_orden = $detalleOrden->id_orden;
                $kardexSalida->cantidad = 1;
                $kardexSalida->numero_ingreso = $numeroSeguimiento;
                $kardexSalida->tipo_movimiento = 'SALIDA';
                $kardexSalida->tipo_transaccion = 'EN_ESPERA_UBICACION';
                $kardexSalida->fecha = now();
                $kardexSalida->save();
            }
            // 2. **ENTRADA a ALMACENADO**
            $kardexEntrada = new Kardex();
            $kardexEntrada->id_paquete = $paquete->id;
            $kardexEntrada->id_orden = $detalleOrden->id_orden;
            $kardexEntrada->cantidad = 1;
            $kardexEntrada->numero_ingreso = $numeroSeguimiento;
            $kardexEntrada->tipo_movimiento = 'ENTRADA';
            $kardexEntrada->tipo_transaccion = 'ALMACENADO';
            $kardexEntrada->fecha = now();
            $kardexEntrada->save();
            // Crear la nueva relación de ubicación con paquete
            $ubicacionPaquete = new UbicacionPaquete();
            $ubicacionPaquete->id_paquete = $paquete->id; // Guardar el ID del paquete encontrado por su UUID
            $ubicacionPaquete->id_ubicacion = $ubicacion->id; // Guardar el ID de la ubicación encontrada por su nomenclatura
            $ubicacionPaquete->estado = 1; // Establecer el estado a 1 cuando se asocia
            $ubicacionPaquete->save();

            // Actualizar el campo id_ubicacion en el paquete
            $paquete->id_ubicacion = $ubicacion->id;
            $paquete->id_estado_paquete = 2; // ID 2 para "En Bodega"
            $paquete->save();

            $detalleOrden->id_estado_paquetes = 2; // Asegurarse de que el estado del paquete en detalle_orden también sea "En Bodega"
            $detalleOrden->save();

            DB::commit(); // Confirmar la transacción

            return response()->json(['message' => 'Relación de Ubicación con Paquete creada correctamente y Kardex actualizado.'], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Revertir la transacción si hay algún error
            Log::error('Error al crear la relación: ' . $e->getMessage());
            return response()->json(['error' => 'Error al crear la relación', 'details' => $e->getMessage()], 500);
        }
    }


    public function update(Request $request, $id)
    {
        // Validación de la entrada
        $validator = Validator::make($request->all(), [
            'codigo_nomenclatura_ubicacion' => 'sometimes|string|exists:ubicaciones,nomenclatura',
            'estado' => 'sometimes|integer|in:0,1', // Validar que el estado sea 0 o 1 si está presente
        ], [
            'codigo_nomenclatura_ubicacion.exists' => 'La ubicación seleccionada no es válida.',
            'estado.integer' => 'El estado debe ser un número entero.',
            'estado.in' => 'El estado debe ser 0 o 1.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 'fail'], 400);
        }

        DB::beginTransaction(); // Iniciar transacción

        try {
            // Buscar la relación UbicacionPaquete usando el ID directamente desde la URL
            $ubicacionPaquete = UbicacionPaquete::findOrFail($id);

            // Obtener el paquete asociado
            $paquete = Paquete::findOrFail($ubicacionPaquete->id_paquete);

            // Si se proporciona una nueva ubicación, verificar y actualizar
            if ($request->has('codigo_nomenclatura_ubicacion')) {
                // Buscar la nueva ubicación
                $ubicacion = Ubicacion::where('nomenclatura', $request->codigo_nomenclatura_ubicacion)->firstOrFail();

                // Verificar si es la misma que la actual
                if ($ubicacionPaquete->id_ubicacion == $ubicacion->id) {
                    return response()->json(['error' => 'El paquete ya está en la ubicación especificada.', 'status' => 'fail'], 400);
                }

                // Verificar si la nueva ubicación ya está ocupada
                $paqueteEnUbicacion = UbicacionPaquete::where('id_ubicacion', $ubicacion->id)
                    ->where('estado', 1) // Asumiendo que estado 1 significa activo
                    ->first();

                if ($paqueteEnUbicacion) {
                    return response()->json(['error' => 'Ya existe un paquete en la ubicación especificada.', 'status' => 'fail'], 400);
                }

                // **Actualizar los movimientos en el Kardex**
                // 1. **SALIDA de la ubicación actual**
                $kardexSalida = new Kardex();
                $kardexSalida->id_paquete = $paquete->id;
                $kardexSalida->id_orden = $paquete->detalleOrden->id_orden; // Asumimos que existe una relación con DetalleOrden
                $kardexSalida->cantidad = 1;
                $kardexSalida->numero_ingreso = $paquete->detalleOrden->orden->numero_seguimiento; // Asumimos relación
                $kardexSalida->tipo_movimiento = 'SALIDA';
                $kardexSalida->tipo_transaccion = 'ALMACENADO';
                $kardexSalida->fecha = now();
                $kardexSalida->save();

                // 2. **ENTRADA a la nueva ubicación**
                $kardexEntrada = new Kardex();
                $kardexEntrada->id_paquete = $paquete->id;
                $kardexEntrada->id_orden = $paquete->detalleOrden->id_orden; // Asumimos que existe una relación con DetalleOrden
                $kardexEntrada->cantidad = 1;
                $kardexEntrada->numero_ingreso = $paquete->detalleOrden->orden->numero_seguimiento; // Asumimos relación
                $kardexEntrada->tipo_movimiento = 'ENTRADA';
                $kardexEntrada->tipo_transaccion = 'ALMACENADO';
                $kardexEntrada->fecha = now();
                $kardexEntrada->save();

                // Actualizar la relación de ubicación actual con la nueva ubicación
                $ubicacionPaquete->id_ubicacion = $ubicacion->id;
            }

            // Si se proporciona un estado, actualizar el estado de la relación de ubicación
            if ($request->has('estado')) {
                $ubicacionPaquete->estado = $request->estado;
            }

            // Guardar los cambios en la relación de UbicacionPaquete
            $ubicacionPaquete->save();

            // Actualizar el campo id_ubicacion en el paquete
            $paquete->id_ubicacion = $ubicacionPaquete->id_ubicacion;
            $paquete->save();

            DB::commit(); // Confirmar la transacción

            return response()->json([
                'message' => 'Ubicación del paquete actualizada correctamente.',
                'status' => 'success',
                'data' => [
                    'paquete_id' => $paquete->id,
                    'paquete_uuid' => $paquete->uuid,
                    'ubicacion' => $ubicacion->nomenclatura ?? $paquete->ubicacion->nomenclatura,
                    'estado' => $ubicacionPaquete->estado
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // Revertir la transacción si hay algún error
            Log::error('Error al actualizar la ubicación del paquete: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al actualizar la ubicación del paquete',
                'details' => $e->getMessage(),
                'status' => 'fail'
            ], 500);
        }
    }


    /**
     * Eliminar una relación.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Buscar el UbicacionPaquete por ID
            $ubicacionPaquete = UbicacionPaquete::find($id);

            if (!$ubicacionPaquete) {
                return response()->json(['error' => 'Relación no encontrada'], 404);
            }

            // Buscar el paquete relacionado
            $paquete = $ubicacionPaquete->paquete;

            if ($paquete) {
                // Establecer el campo id_ubicacion a NULL y id_estado_paquete a 14 en la tabla paquete para el paquete relacionado
                $paquete->id_ubicacion = null;
                $paquete->id_estado_paquete = 14;
                $paquete->save();
            }

            // Obtener el detalle de la orden
            $detalleOrden = DetalleOrden::where('id_paquete', $ubicacionPaquete->id_paquete)->first();

            if (!$detalleOrden) {
                // Si no se encuentra el DetalleOrden, continuar sin lanzar error
                $ubicacionPaquete->estado = 0;
                $ubicacionPaquete->save();
                return response()->json(['message' => 'Ubicación desactivada correctamente, pero no se encontró el detalle de la orden asociado.'], 200);
            }

            // Verificar que la orden existe
            $orden = Orden::find($detalleOrden->id_orden);
            if (!$orden) {
                return response()->json(['error' => 'Orden no encontrada.'], 404);
            }

            // Crear la salida en el Kardex con el número de seguimiento (numero_seguimiento) como SALIDA de recolección
            $kardexSalida = new Kardex();
            $kardexSalida->id_paquete = $ubicacionPaquete->id_paquete;
            $kardexSalida->id_orden = $detalleOrden->id_orden;
            $kardexSalida->cantidad = 1;
            $kardexSalida->numero_ingreso = $orden->numero_seguimiento;
            $kardexSalida->tipo_movimiento = 'SALIDA';
            $kardexSalida->tipo_transaccion = 'ALMACENADO';
            $kardexSalida->fecha = now();
            $kardexSalida->save();

            // Crear la entrada en el Kardex con el número de seguimiento como ENTRADA de almacenado
            $kardexEntrada = new Kardex();
            $kardexEntrada->id_paquete = $ubicacionPaquete->id_paquete;
            $kardexEntrada->id_orden = $detalleOrden->id_orden;
            $kardexEntrada->cantidad = 1;
            $kardexEntrada->numero_ingreso = $orden->numero_seguimiento;
            $kardexEntrada->tipo_movimiento = 'ENTRADA';
            $kardexEntrada->tipo_transaccion = 'EN_ESPERA_UBICACION';
            $kardexEntrada->fecha = now();
            $kardexEntrada->save();

            // en lugar de cambiar estado, se va eliminar el regitro.
            $ubicacionPaquete->delete();

            // Retornar un mensaje de éxito
            return response()->json(['message' => 'Ubicación eliminada correctamente.'], 200);
        } catch (Exception $e) {
            Log::error('Error al desactivar la relación: ' . $e->getMessage());
            return response()->json(['error' => 'Error al desactivar la relación'], 500);
        }
    }
}

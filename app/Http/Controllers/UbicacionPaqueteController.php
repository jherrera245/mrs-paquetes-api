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
    public function index(Request $request)
    {
        try {
            // Obtener filtros de la solicitud
            $filters = $request->only(['id_paquete', 'id_ubicacion', 'estado']);

            $query = UbicacionPaquete::with(['ubicacion', 'paquete']);

            // Aplicar filtros si están presentes
            if (!empty($filters['id_paquete'])) {
                $query->where('id_paquete', $filters['id_paquete']);
            }

            if (!empty($filters['id_ubicacion'])) {
                $query->where('id_ubicacion', $filters['id_ubicacion']);
            }

            if (array_key_exists('estado', $filters) && !is_null($filters['estado'])) {
                $query->where('estado', $filters['estado']);
            }

            // Paginación de resultados utilizando el parámetro 'per_page'
            $perPage = $request->input('per_page', 10); // Número de elementos por página por defecto es 10
            $ubicacionPaquetes = $query->paginate($perPage);

            // Formatear los datos manualmente
            $ubicacionPaquetesFormatted = $ubicacionPaquetes->getCollection()->map(function ($ubicacionPaquete) {
                return [
                    'id' => $ubicacionPaquete->id,
                    'paquete' => $ubicacionPaquete->paquete ? $ubicacionPaquete->paquete->descripcion_contenido : 'N/A',
                    'id_paquete' => $ubicacionPaquete->paquete ? $ubicacionPaquete->paquete->id : 'N/A',
                    'ubicacion' => $ubicacionPaquete->ubicacion ? $ubicacionPaquete->ubicacion->nomenclatura : 'N/A',
                    'id_ubicacion' => $ubicacionPaquete->ubicacion ? $ubicacionPaquete->ubicacion->id : 'N/A',
                    'estado' => $ubicacionPaquete->estado,
                ];
            });

            $ubicacionPaquetes->setCollection($ubicacionPaquetesFormatted);

            // Devolver la respuesta paginada
            return response()->json($ubicacionPaquetes, 200);
        } catch (\Exception $e) {
            Log::error('Error al listar ubicaciones de paquetes: ' . $e->getMessage());
            return response()->json(['error' => 'Error al listar ubicaciones de paquetes', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar una relación específica de ubicación con paquete.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function show($id)
    {
        try {
            // Buscar el UbicacionPaquete por ID con sus relaciones
            $ubicacionPaquete = UbicacionPaquete::with(['ubicacion', 'paquete'])->find($id);

            if (!$ubicacionPaquete) {
                return response()->json(['error' => 'Relación no encontrada'], 404);
            }

            // Formatear los datos manualmente
            $formattedData = [
                'id' => $ubicacionPaquete->id,
                'paquete' => $ubicacionPaquete->paquete ? $ubicacionPaquete->paquete->descripcion_contenido : 'N/A',
                'id_paquete' => $ubicacionPaquete->paquete ? $ubicacionPaquete->paquete->id : 'N/A',
                'ubicacion' => $ubicacionPaquete->ubicacion ? $ubicacionPaquete->ubicacion->nomenclatura : 'N/A',
                'id_ubicacion' => $ubicacionPaquete->ubicacion ? $ubicacionPaquete->ubicacion->id : 'N/A',
                'estado' => $ubicacionPaquete->estado,
            ];

            return response()->json($formattedData, 200);
        } catch (Exception $e) {
            Log::error('Error al mostrar la relación: ' . $e->getMessage());
            return response()->json(['error' => 'Error al mostrar la relación', 'details' => $e->getMessage()], 500);
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

            // si el estado del paquete es 1 (Recibido de recepcion) se hace una salida de recepcion a almacenado.
            if ($paquete->id_estado_paquete == 5) {
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
            }else{
                // 1. **SALIDA de RECOLECTADO**
                $kardexSalida = new Kardex();
                $kardexSalida->id_paquete = $paquete->id;
                $kardexSalida->id_orden = $detalleOrden->id_orden;
                $kardexSalida->cantidad = 1;
                $kardexSalida->numero_ingreso = $numeroSeguimiento;
                $kardexSalida->tipo_movimiento = 'SALIDA';
                $kardexSalida->tipo_transaccion = 'RECOLECTADO';
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
            $ubicacionPaquete->estado = 1;
            $ubicacionPaquete->save();

            // Actualizar el campo id_ubicacion en el paquete
            $paquete->id_ubicacion = $ubicacion->id;
            $paquete->id_estado_paquete = 2; // ID 2 para "En Bodega"
            $paquete->save();

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
        DB::beginTransaction(); // Iniciar una transacción

        try {
            // Buscar la relación de ubicación con paquete
            $ubicacionPaquete = UbicacionPaquete::find($id);

            if (!$ubicacionPaquete) {
                return response()->json(['error' => 'Relación no encontrada'], 404);
            }

            // Verificar si la nueva ubicación ya está ocupada por otro paquete
            if (UbicacionPaquete::where('id_ubicacion', $request->id_ubicacion)
                ->where('id', '!=', $id)
                ->where('estado', 0)  // Asegúrate de que 'estado' esté correcto
                ->exists()
            ) {
                return response()->json(['error' => 'Esta ubicación se encuentra ocupada por otro paquete.'], 400);
            }

            // Verificar si el paquete ya tiene una ubicación asignada en ubicaciones_paquetes
            $existingUbicacionPaquete = UbicacionPaquete::where('id_paquete', $request->id_paquete)
                ->where('id', '!=', $id)
                ->first();

            if ($existingUbicacionPaquete) {
                // Actualizar la ubicación del paquete existente
                $existingUbicacionPaquete->id_ubicacion = $request->id_ubicacion;
                $existingUbicacionPaquete->estado = $request->estado;
                $existingUbicacionPaquete->save();
            } else {
                // Si no existe, crear una nueva relación de ubicación con paquete
                $ubicacionPaquete->id_ubicacion = $request->id_ubicacion;
                $ubicacionPaquete->id_paquete = $request->id_paquete;
                $ubicacionPaquete->estado = $request->estado;
                $ubicacionPaquete->save();
            }

            // Actualizar el paquete en la tabla 'paquetes'
            $paquete = Paquete::find($request->id_paquete);
            if ($paquete) {
                $paquete->id_ubicacion = $request->id_ubicacion; // Actualizar la ubicación del paquete
                $paquete->save();
            } else {
                throw new Exception('Paquete no encontrado para actualizar la ubicación.');
            }

            DB::commit(); // Confirmar la transacción

            return response()->json(['message' => 'Ubicación de los paquetes actualizada correctamente.'], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // Revertir la transacción en caso de error
            Log::error('Error al actualizar la ubicación: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar la ubicación', 'details' => $e->getMessage()], 500);
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
            // Comenzar buscando el UbicacionPaquete
            $ubicacionPaquete = UbicacionPaquete::find($id);

            if (!$ubicacionPaquete) {
                return response()->json(['error' => 'Relación no encontrada'], 404);
            }


            // Establecer el campo id_ubicacion como NULL en la tabla paquete para el paquete relacionado
            $paquete = $ubicacionPaquete->paquete;
            if ($paquete) {
                $paquete->id_ubicacion = null; // Establecer a NULL para eliminar la asociación
                $paquete->save();
            }

            // salida de Almacenado en kardex.
            // Obtener el detalle de la orden.
            $detalleOrden = DetalleOrden::where('id_paquete', $ubicacionPaquete->id_paquete)->first();

            if (!$detalleOrden) {
                // Si no se encuentra el DetalleOrden, continuar sin lanzar error
                $ubicacionPaquete->delete(); // Eliminar la relación de todos modos
                return response()->json(['message' => 'Ubicación eliminada correctamente, pero no se encontró el detalle de la orden asociado.'], 200);
            }

            // Verificar que la orden existe
            $orden = Orden::find($detalleOrden->id_orden);
            if (!$orden) {
                return response()->json(['error' => 'Orden no encontrada.'], 404);
            }

            // Crear la entrada en el Kardex con el número de seguimiento (numero_seguimiento) como SALIDA de recolección
            $kardexSalida = new Kardex();
            $kardexSalida->id_paquete = $ubicacionPaquete->id_paquete;
            $kardexSalida->id_orden = $detalleOrden->id_orden;
            $kardexSalida->cantidad = 1;
            $kardexSalida->numero_ingreso = $orden->numero_seguimiento;
            $kardexSalida->tipo_movimiento = 'SALIDA';
            $kardexSalida->tipo_transaccion = 'RETIRO_ALMACEN';
            $kardexSalida->fecha = now();
            $kardexSalida->save();

            // Crear entrada en el kardex con el numero de seguimiento como ENTRADA de almacenado
            $kardexEntrada = new Kardex();
            $kardexEntrada->id_paquete = $ubicacionPaquete->id_paquete;
            $kardexEntrada->id_orden = $detalleOrden->id_orden;
            $kardexEntrada->cantidad = 1;
            $kardexEntrada->numero_ingreso = $orden->numero_seguimiento;
            $kardexEntrada->tipo_movimiento = 'ENTRADA';
            $kardexEntrada->tipo_transaccion = 'DEVOLUCION_RECOLECCION';
            $kardexEntrada->fecha = now();
            $kardexEntrada->save();

            // Solo eliminar después de que todas las operaciones sean exitosas
            $ubicacionPaquete->delete();

            // Retornar un mensaje de éxito
            return response()->json(['message' => 'Ubicación eliminada correctamente.'], 200);
        } catch (Exception $e) {
            Log::error('Error al eliminar la relación: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar la relación'], 500);
        }
    }
}

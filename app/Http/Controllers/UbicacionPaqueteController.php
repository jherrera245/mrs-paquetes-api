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
            // Filtrar los resultados
            $filters = $request->only(['id_paquete', 'id_ubicacion', 'estado']);

            $query = UbicacionPaquete::with(['ubicacion', 'paquete']);

            if (!empty($filters['id_paquete'])) {
                $query->where('id_paquete', $filters['id_paquete']);
            }

            if (!empty($filters['id_ubicacion'])) {
                $query->where('id_ubicacion', $filters['id_ubicacion']);
            }

            if (array_key_exists('estado', $filters) && !is_null($filters['estado'])) {
                $query->where('estado', $filters['estado']);
            }

            // Paginar los resultados
            $perPage = $request->input('per_page', 10); // Tamaño de página por defecto 10
            $ubicacionPaquetes = $query->paginate($perPage);

            if ($ubicacionPaquetes->isEmpty()) {
                return response()->json(['message' => 'No se encontraron ubicaciones de paquetes.'], 404);
            }

            // Formatear los datos manualmente en el controlador
            $formattedData = $ubicacionPaquetes->getCollection()->map(function ($ubicacionPaquete) {
                return [
                    'id' => $ubicacionPaquete->id,
                    'paquete' => $ubicacionPaquete->paquete ? $ubicacionPaquete->paquete->descripcion_contenido : 'N/A',
                    'id_paquete' => $ubicacionPaquete->paquete ? $ubicacionPaquete->paquete->id : 'N/A',
                    'ubicacion' => $ubicacionPaquete->ubicacion ? $ubicacionPaquete->ubicacion->nomenclatura : 'N/A',
                    'id_ubicacion' => $ubicacionPaquete->ubicacion ? $ubicacionPaquete->ubicacion->id : 'N/A',
                    'estado' => $ubicacionPaquete->estado,
                ];
            });

            // Devolver la respuesta paginada
            return response()->json([
                'data' => $formattedData, // Datos formateados
                'pagination' => [
                    'current_page' => $ubicacionPaquetes->currentPage(),
                    'per_page' => $ubicacionPaquetes->perPage(),
                    'total' => $ubicacionPaquetes->total(),
                    'last_page' => $ubicacionPaquetes->lastPage(),
                    'from' => $ubicacionPaquetes->firstItem(),
                    'to' => $ubicacionPaquetes->lastItem(),
                ]
            ], 200);
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
            'id_paquete' => 'required|exists:paquetes,id',
            'id_ubicacion' => 'required|exists:ubicaciones,id',
            'estado' => 'required|boolean',
        ], [
            'id_paquete.required' => 'El campo de paquete es obligatorio.',
            'id_paquete.exists' => 'El paquete seleccionado no es válido.',
            'id_ubicacion.required' => 'El campo de ubicación es obligatorio.',
            'id_ubicacion.exists' => 'La ubicación seleccionada no es válida.',
            'estado.required' => 'El campo de estado es obligatorio.',
            'estado.boolean' => 'El campo de estado debe ser verdadero o falso.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        DB::beginTransaction(); // Iniciar transacción

        try {
            // Verificar si ya existe una relación de ubicación para el paquete con la misma ubicación
            $existingUbicacionPaquete = UbicacionPaquete::where('id_paquete', $request->id_paquete)
                ->where('id_ubicacion', $request->id_ubicacion)
                ->first();

            if ($existingUbicacionPaquete) {
                return response()->json(['error' => 'Esta ubicación ya está asignada a este paquete.'], 400);
            }

            // Crear la nueva relación de ubicación con paquete
            $ubicacionPaquete = new UbicacionPaquete();
            $ubicacionPaquete->id_paquete = $request->id_paquete;
            $ubicacionPaquete->id_ubicacion = $request->id_ubicacion;
            $ubicacionPaquete->estado = $request->estado;
            $ubicacionPaquete->save();

            // Actualizar el campo id_ubicacion en el paquete
            $paquete = Paquete::find($ubicacionPaquete->id_paquete);
            $paquete->id_ubicacion = $ubicacionPaquete->id_ubicacion;
            $paquete->save();

            // **Agregar los movimientos en el Kardex**
            $detalleOrden = DetalleOrden::where('id_paquete', $ubicacionPaquete->id_paquete)->first();
            if (!$detalleOrden) {
                throw new Exception('Detalle de orden no encontrado para el paquete.');
            }

            $numeroSeguimiento = Orden::where('id', $detalleOrden->id_orden)->value('numero_seguimiento');
            if (!$numeroSeguimiento) {
                throw new Exception('Número de seguimiento no encontrado para la orden.');
            }

            // 1. **SALIDA de RECOLECTADO**
            $kardexSalida = new Kardex();
            $kardexSalida->id_paquete = $ubicacionPaquete->id_paquete;
            $kardexSalida->id_orden = $detalleOrden->id_orden;
            $kardexSalida->cantidad = 1;
            $kardexSalida->numero_ingreso = $numeroSeguimiento;
            $kardexSalida->tipo_movimiento = 'SALIDA';
            $kardexSalida->tipo_transaccion = 'RECOLECTADO';
            $kardexSalida->fecha = now();
            $kardexSalida->save();

            // 2. **ENTRADA a ALMACENADO**
            $kardexEntrada = new Kardex();
            $kardexEntrada->id_paquete = $ubicacionPaquete->id_paquete;
            $kardexEntrada->id_orden = $detalleOrden->id_orden;
            $kardexEntrada->cantidad = 1;
            $kardexEntrada->numero_ingreso = $numeroSeguimiento;
            $kardexEntrada->tipo_movimiento = 'ENTRADA';
            $kardexEntrada->tipo_transaccion = 'ALMACENADO';
            $kardexEntrada->fecha = now();
            $kardexEntrada->save();

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

            // Verificar si ya existe una ubicación asignada al paquete nuevo
            if (
                $request->has('id_paquete') &&
                $request->has('id_ubicacion') &&
                UbicacionPaquete::where('id_paquete', $request->id_paquete)
                ->where('id_ubicacion', $request->id_ubicacion)
                ->where('id', '!=', $id)
                ->exists()
            ) {
                return response()->json(['error' => 'Este paquete ya tiene asignada esa ubicación.'], 400);
            }

            // Set the old location to 'Desocupado'
            $oldUbicacion = $ubicacionPaquete->ubicacion;
            if ($oldUbicacion) {
                $oldUbicacion->ocupado = 0; // Set to '0' or any value that indicates unoccupied
                $oldUbicacion->save();
            }

            // Obtener el detalle de la orden del paquete que se está quitando
            $detalleOrdenRemovido = DetalleOrden::where('id_paquete', $ubicacionPaquete->id_paquete)->first();
            $ordenRemovido = Orden::find($detalleOrdenRemovido->id_orden);

            // Registrar la SALIDA del paquete removido (desde ALMACENADO)
            $kardexSalidaRemovido = new Kardex();
            $kardexSalidaRemovido->id_paquete = $ubicacionPaquete->id_paquete;
            $kardexSalidaRemovido->id_orden = $detalleOrdenRemovido->id_orden;
            $kardexSalidaRemovido->cantidad = 1;
            $kardexSalidaRemovido->numero_ingreso = $ordenRemovido->numero_seguimiento;
            $kardexSalidaRemovido->tipo_movimiento = 'SALIDA';
            $kardexSalidaRemovido->tipo_transaccion = 'ALMACENADO';
            $kardexSalidaRemovido->fecha = now();
            $kardexSalidaRemovido->save();

            // Registrar la ENTRADA del paquete removido a su nueva ubicación (DEVOLUCION_RECOLECCION)
            $kardexEntradaRemovido = new Kardex();
            $kardexEntradaRemovido->id_paquete = $ubicacionPaquete->id_paquete;
            $kardexEntradaRemovido->id_orden = $detalleOrdenRemovido->id_orden;
            $kardexEntradaRemovido->cantidad = 1;
            $kardexEntradaRemovido->numero_ingreso = $ordenRemovido->numero_seguimiento;
            $kardexEntradaRemovido->tipo_movimiento = 'ENTRADA';
            $kardexEntradaRemovido->tipo_transaccion = 'DEVOLUCION_RECOLECCION';
            $kardexEntradaRemovido->fecha = now();
            $kardexEntradaRemovido->save();

            // Obtener el detalle de la orden del paquete nuevo que ocupará la ubicación
            $detalleOrdenNuevo = DetalleOrden::where('id_paquete', $request->id_paquete)->first();
            $ordenNuevo = Orden::find($detalleOrdenNuevo->id_orden);

            // Registrar la SALIDA del nuevo paquete desde su ubicación anterior, debería ser RECOLECTADO.
            $kardexSalidaNuevo = new Kardex();
            $kardexSalidaNuevo->id_paquete = $detalleOrdenNuevo->id_paquete;
            $kardexSalidaNuevo->id_orden = $detalleOrdenNuevo->id_orden;
            $kardexSalidaNuevo->cantidad = 1;
            $kardexSalidaNuevo->numero_ingreso = $ordenNuevo->numero_seguimiento;
            $kardexSalidaNuevo->tipo_movimiento = 'SALIDA';
            $kardexSalidaNuevo->tipo_transaccion = 'RECOLECTADO'; // Ajustar según aplique
            $kardexSalidaNuevo->fecha = now();
            $kardexSalidaNuevo->save();

            // Registrar la ENTRADA del nuevo paquete a la ubicación actual (ALMACENADO)
            $kardexEntradaNuevo = new Kardex();
            $kardexEntradaNuevo->id_paquete = $detalleOrdenNuevo->id_paquete;
            $kardexEntradaNuevo->id_orden = $detalleOrdenNuevo->id_orden;
            $kardexEntradaNuevo->cantidad = 1;
            $kardexEntradaNuevo->numero_ingreso = $ordenNuevo->numero_seguimiento;
            $kardexEntradaNuevo->tipo_movimiento = 'ENTRADA';
            $kardexEntradaNuevo->tipo_transaccion = 'ALMACENADO';
            $kardexEntradaNuevo->fecha = now();
            $kardexEntradaNuevo->save();

            // Update the new location to 'Ocupado'
            $newUbicacion = Ubicacion::find($request->id_ubicacion);
            if ($newUbicacion) {
                $newUbicacion->ocupado = 1; // Set to '1' or any value that indicates occupied
                $newUbicacion->save();
            }

            // Actualizar la relación de ubicación
            $ubicacionPaquete->update($request->all());

            DB::commit(); // Confirmar la transacción

            return response()->json(['message' => 'Ubicación de los paquetes actualizada correctamente.'], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // Revertir la transacción en caso de error
            Log::error('Error al actualizar la ubicación: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar la ubicación'], 500);
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

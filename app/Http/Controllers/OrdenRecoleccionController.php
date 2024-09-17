<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdenRecoleccion;
use App\Models\RutaRecoleccion;
use App\Models\Kardex;
use App\Models\Orden;
use App\Models\Direcciones;
use Illuminate\Support\Facades\DB;
// usamos logs
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class OrdenRecoleccionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = OrdenRecoleccion::with(['rutaRecoleccion', 'orden']);

        if ($request->has('id_ruta_recoleccion')) {
            $query->where('id_ruta_recoleccion', $request->input('id_ruta_recoleccion'));
        }

        // Aplicar paginación
        $perPage = $request->input('per_page', 10);
        $ordenesRecolecciones = $query->paginate($perPage);

        return response()->json($ordenesRecolecciones);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha_asignacion' => 'required|date',
            'id_vehiculo' => 'required|integer|exists:vehiculos,id',
            'ordenes' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {

            $ruta = new RutaRecoleccion();
            $ruta->fecha_asignacion = $request->input('fecha_asignacion');
            $ruta->id_vehiculo = $request->input('id_vehiculo');
            $ruta->estado = 1;
            $ruta->save();

            //generar codigo de ruta
            $ruta->nombre  =  'RR' . str_pad($ruta->id, 11, '0', STR_PAD_LEFT);
            $ruta->save();

            $ordenes = $request->input('ordenes');

            $prioridades = array_column($ordenes, 'prioridad');
            $conteo_prioridades = array_count_values($prioridades);

            foreach ($ordenes as $orden) {

                if ( $conteo_prioridades[$paquete['prioridad']] > 1) {
                    DB::rollBack();
                    return response()->json(['message' => 'Debe de establecer una proridad unica para cada orden'], 500);
                }

                //informacion de la orden
                $detalle = DB::table('ordenes')
                ->select(
                    'ordenes.id', 
                    'ordenes.tipo_orden',
                    'ordenes.numero_seguimiento', 
                    'ordenes.id_direccion', 
                    'direcciones.id_departamento', 
                    'direcciones.id_municipio', 
                    'direcciones.direccion'
                )
                ->join('direcciones', 'direcciones.id', '=', 'ordenes.id_direccion')
                ->where('ordenes.id', $orden['id'])
                ->first();

                //orden no existe o es una preorden
                if (!$detalle || $detalle->tipo_orden !== 'preorden') {
                    DB::rollBack();
                    return response()->json(['error' => 'Orden no valida numero de seguimiento '. $$detalle->numero_seguimiento], Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                $recolecciones = new OrdenRecoleccion();
                $recolecciones->codigo_unico_recoleccion = $ruta->nombre;
                $recolecciones->id_ruta_recoleccion = $ruta->id;
                $recolecciones->id_orden = $orden['id'];
                $recolecciones->prioridad = $orden['prioridad'];
                $recolecciones->id_departamento = $detalle->id_departamento;
                $recolecciones->id_municipio = $detalle->id_municipio;
                $recolecciones->id_direccion = $detalle->id_direccion;
                $recolecciones->destino = $detalle->direccion;
                $recolecciones->estado = 1;
                $recolecciones->save();

                //consultar los paquetes
                $paquetes = DB::table('detalle_orden')
                ->select(
                    'detalle_orden.id_paquete', 
                    'detalle_orden.id_orden', 
                    'ordenes.numero_seguimiento', 
                    'detalle_orden.id_direccion_entrega', 
                    'direcciones.id_departamento', 
                    'direcciones.id_municipio', 
                    'direcciones.direccion'
                )
                ->join('ordenes', 'ordenes.id', '=', 'detalle_orden.id_orden')
                ->join('direcciones', 'direcciones.id', '=', 'detalle_orden.id_direccion_entrega')
                ->where('ordenes.id', $orden['id'])->get();


                foreach($paquetes as $paquete)
                {
                    // Crear el objeto Kardex para ENTRADA en ASIGNADO_RUTA
                    $kardexEntrada = new Kardex();
                    $kardexEntrada->id_paquete = $paquete->id_paquete;
                    $kardexEntrada->id_orden = $paquete->id_orden;
                    $kardexEntrada->cantidad = 1;
                    $kardexEntrada->numero_ingreso = $paquete->numero_seguimiento;
                    $kardexEntrada->tipo_movimiento = 'ENTRADA';
                    $kardexEntrada->tipo_transaccion = 'EN_RECOLECCION';
                    $kardexEntrada->fecha = now();
                    $kardexEntrada->save(); // Guardar el registro de ENTRADA en kardex
                }
            }

            DB::commit();
            return response()->json(['message' => 'Ruta de recoleccion creada y orden de recolecion generada correctamente'], Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Error al asignar rutas'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ordenRecoleccion = OrdenRecoleccion::with(['rutaRecoleccion', 'orden'])->findOrFail($id);

        return response()->json($ordenRecoleccion);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'fecha_asignacion' => 'required|date',
            'id_vehiculo' => 'required|integer|exists:vehiculos,id',
            'ordenes' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Buscar la ruta de recolección existente
            $ruta = RutaRecoleccion::find($id);
            $ruta->fecha_asignacion = $request->input('fecha_asignacion');
            $ruta->id_vehiculo = $request->input('id_vehiculo');
            $ruta->estado = 1;
            $ruta->save();

            $ordenes = $request->input('ordenes');

            $prioridades = array_column($ordenes, 'prioridad');
            $conteo_prioridades = array_count_values($prioridades);

            foreach ($ordenes as $orden) {
                // Información de la orden

                if ( $conteo_prioridades[$paquete['prioridad']] > 1) {
                    DB::rollBack();
                    return response()->json(['message' => 'Debe de establecer una proridad unica para cada orden'], 500);
                }

                $detalle = DB::table('ordenes')
                    ->select(
                        'ordenes.id', 
                        'ordenes.tipo_orden',
                        'ordenes.numero_seguimiento', 
                        'ordenes.id_direccion', 
                        'direcciones.id_departamento', 
                        'direcciones.id_municipio', 
                        'direcciones.direccion'
                    )
                    ->join('direcciones', 'direcciones.id', '=', 'ordenes.id_direccion')
                    ->where('ordenes.id', $orden['id'])
                    ->first();

                // Orden no existe o es una preorden
                if (!$detalle || $detalle->tipo_orden !== 'preorden') {
                    DB::rollBack();
                    return response()->json(['error' => 'Orden no válida número de seguimiento ' . $detalle->numero_seguimiento], Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                // Buscar recolección existente para la orden
                $recoleccionExistente = OrdenRecoleccion::where('id_ruta_recoleccion', $ruta->id)
                    ->where('id_orden', $orden['id'])
                    ->first();

                if ($recoleccionExistente) {
                    // Actualizar recolección existente
                    $recoleccionExistente->prioridad = $orden['prioridad'];
                    $recoleccionExistente->save();
                } else {
                    // Crear nueva recolección
                    $recolecciones = new OrdenRecoleccion();
                    $recolecciones->codigo_unico_recoleccion = $ruta->nombre;
                    $recolecciones->id_ruta_recoleccion = $ruta->id;
                    $recolecciones->id_orden = $orden['id'];
                    $recolecciones->prioridad = $orden['prioridad'];
                    $recolecciones->id_departamento = $detalle->id_departamento;
                    $recolecciones->id_municipio = $detalle->id_municipio;
                    $recolecciones->id_direccion = $detalle->id_direccion;
                    $recolecciones->destino = $detalle->direccion;
                    $recolecciones->estado = 1;
                    $recolecciones->save();

                    // Consultar los paquetes
                    $paquetes = DB::table('detalle_orden')
                        ->select(
                            'detalle_orden.id_paquete', 
                            'detalle_orden.id_orden', 
                            'ordenes.numero_seguimiento', 
                            'detalle_orden.id_direccion_entrega', 
                            'direcciones.id_departamento', 
                            'direcciones.id_municipio', 
                            'direcciones.direccion'
                        )
                        ->join('ordenes', 'ordenes.id', '=', 'detalle_orden.id_orden')
                        ->join('direcciones', 'direcciones.id', '=', 'detalle_orden.id_direccion_entrega')
                        ->where('ordenes.id', $orden['id'])->get();

                    foreach ($paquetes as $paquete) {
                        // Crear el objeto Kardex para ENTRADA en ASIGNADO_RUTA
                        $kardexEntrada = new Kardex();
                        $kardexEntrada->id_paquete = $paquete->id_paquete;
                        $kardexEntrada->id_orden = $paquete->id_orden;
                        $kardexEntrada->cantidad = 1;
                        $kardexEntrada->numero_ingreso = $paquete->numero_seguimiento;
                        $kardexEntrada->tipo_movimiento = 'ENTRADA';
                        $kardexEntrada->tipo_transaccion = 'EN_RECOLECCION';
                        $kardexEntrada->fecha = now();
                        $kardexEntrada->save(); // Guardar el registro de ENTRADA en kardex
                    }
                }
            }

            DB::commit();
            return response()->json(['message' => 'Ruta de recolección actualizada y órdenes de recolección gestionadas correctamente'], Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar rutas'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function asignarRecoleccion($id_orden_recoleccion)
    {
        // Iniciar transacción
        DB::beginTransaction();

        try {
            // Obtiene la orden de recolección o lanzar un 404 en caso de un error
            $ordenRecoleccion = OrdenRecoleccion::findOrFail($id_orden_recoleccion);

            // Verificar que la orden tenga detalles antes de continuar
            if (!$ordenRecoleccion->orden || $ordenRecoleccion->orden->detalles->isEmpty()) {
                return response()->json(['error' => 'La orden de recolección no tiene detalles asociados'], 400);
            }

            // Cambiamos el estado de la orden de recoleccion.
            $ordenRecoleccion->recoleccion_iniciada = 1;
            $ordenRecoleccion->save();

            // Recorrer el detalle de la orden de recolección
            foreach ($ordenRecoleccion->orden->detalles as $detalle) {
                // Registro de movimiento SALIDA en el kardex (ORDEN)
                $kardexSalida = new Kardex();
                $kardexSalida->id_paquete = $detalle->id_paquete;
                $kardexSalida->id_orden = $detalle->id_orden;
                $kardexSalida->cantidad = 1;
                $kardexSalida->numero_ingreso = $detalle->orden->numero_seguimiento;
                $kardexSalida->tipo_movimiento = 'SALIDA';
                $kardexSalida->tipo_transaccion = 'ORDEN';
                $kardexSalida->fecha = date('Y-m-d'); 
                $kardexSalida->save();

                // Registro de movimiento ENTRADA en el kardex (ESPERA_RECOLECCION)
                $kardexEntrada = new Kardex();
                $kardexEntrada->id_paquete = $detalle->id_paquete;
                $kardexEntrada->id_orden = $detalle->id_orden;
                $kardexEntrada->cantidad = 1;
                $kardexEntrada->numero_ingreso = $detalle->orden->numero_seguimiento;
                $kardexEntrada->tipo_movimiento = 'ENTRADA';
                $kardexEntrada->tipo_transaccion = 'ESPERA_RECOLECCION';
                $kardexEntrada->fecha = date('Y-m-d');
                $kardexEntrada->save();
            }

            // Confirmar la transacción
            DB::commit();

            return response()->json(['message' => 'Estado de recolecciones actualizado correctamente'], 200);
        } catch (\Exception $e) {
            // Si ocurre algún error, revertir la transacción
            DB::rollback();

            // Loguear el error si es necesario
            Log::error('Error en la asignación de recolecciones: ' . $e->getMessage());

            // Devolver una respuesta de error
            return response()->json(['error' => 'Hubo un problema al actualizar las recolecciones'], 500);
        }
    }

    // funcion para finalizar una orden de recoleccion
    public function finalizarOrdenRecoleccion($id_orden_recoleccion)
    {
        // obtenemos la orden de recoleccion dela bd.
        $ordenRecoleccion = OrdenRecoleccion::findOrFail($id_orden_recoleccion);

        // Usamos rollback para que si hay un error en el proceso se revierta.
        DB::beginTransaction();

        try {
            // Cambiamos el estado de la orden de recoleccion.
            $ordenRecoleccion->recoleccion_finalizada = 1;
            $ordenRecoleccion->estado = 0;
            $ordenRecoleccion->recoleccion_iniciada = 0;
            $ordenRecoleccion->save();

            // recorremos el detalle de esa orden de recoleccion.
            foreach ($ordenRecoleccion->orden->detalles as $detalle) {
                // Registrar un movimiento en el kardex SALIDA en orden y ENTRADA en transacción RECOLECTADO.
                $kardexSalida = new Kardex();
                $kardexSalida->id_paquete = $detalle->id_paquete;
                $kardexSalida->id_orden = $detalle->id_orden; 
                $kardexSalida->cantidad = 1;
                $kardexSalida->numero_ingreso = $detalle->orden->numero_seguimiento;
                $kardexSalida->tipo_movimiento = 'SALIDA';
                $kardexSalida->tipo_transaccion = 'ESPERA_RECOLECCION';
                $kardexSalida->fecha = date('Y-m-d');
                $kardexSalida->save();

                $kardexEntrada = new Kardex();
                $kardexEntrada->id_paquete = $detalle->id_paquete;
                $kardexEntrada->id_orden = $detalle->id_orden; 
                $kardexEntrada->cantidad = 1;
                $kardexEntrada->numero_ingreso = $detalle->orden->numero_seguimiento;
                $kardexEntrada->tipo_movimiento = 'ENTRADA';
                $kardexEntrada->tipo_transaccion = 'RECOLECTADO';
                $kardexEntrada->fecha = date('Y-m-d');
                $kardexEntrada->save();
            }

            // Confirmamos la transacción después de recorrer todo.
            DB::commit();

            return response()->json(['message' => 'Órden de recoleccion finalizada correctamente']);
        } catch (\Throwable $th) {
            // Si hay un error se hace rollback.
            DB::rollback();
            throw $th;
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ordenRecoleccion = OrdenRecoleccion::findOrFail($id);

        $ordenRecoleccion->delete();

        // enviar mensaje personalizado.
        return response()->json(['message' => 'Orden de recolección eliminada correctamente']);
    }
}

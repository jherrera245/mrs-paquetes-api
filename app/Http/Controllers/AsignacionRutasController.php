<?php

namespace App\Http\Controllers;

use App\Models\AsignacionRutas;
use App\Models\DetalleOrden;
use App\Models\Direcciones;
use App\Models\Rutas;
use App\Models\Kardex;
use App\Models\Orden;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Paquete;
use Doctrine\DBAL\Query\QueryException;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AsignacionRutasController extends Controller
{
    public function index(Request $request)
    {
        $filtros = $request->only([
            'codigo_unico_asignacion',
            'id_ruta',
            'id_vehiculo',
            'id_paquete',
            'fecha',
            'id_estado',
            'destino',
        ]);

        $perPage = $request->input('per_page', 10);


        $asignacionrutas = AsignacionRutas::filtrar($filtros)->paginate($perPage);

        $data = [
            'asignacionrutas' => $asignacionrutas,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_bodega' => 'required|exists:bodegas,id',
            'fecha_programada' => 'required|date',
            'id_vehiculo' => 'required|integer|exists:vehiculos,id',
            'paquetes' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {

            $ruta = new Rutas();
            $ruta->id_bodega = $request->input('id_bodega');
            $ruta->fecha_programada = $request->input('fecha_programada');
            $ruta->estado = 1;
            $ruta->save();

            //generar codigo de ruta
            $ruta->nombre  =  'R' . str_pad($ruta->id, 12, '0', STR_PAD_LEFT);
            $ruta->save();

            $paquetes = $request->input('paquetes');
            $fecha = now();

            $prioridades = array_column($paquetes, 'prioridad');
            $conteo_prioridades = array_count_values($prioridades);

            foreach ($paquetes as $paquete) {

                if ($conteo_prioridades[$paquete['prioridad']] > 1) {
                    DB::rollBack();
                    return response()->json(['message' => 'Debe de establecer una proridad unica para cada paquete'], 500);
                }

                $detalle = DB::table('detalle_orden')
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
                    ->where('detalle_orden.id_paquete', $paquete["id"])->first();

                $results[] = $detalle;

                $asignaciones = new AsignacionRutas();
                $asignaciones->codigo_unico_asignacion = $ruta->nombre;
                $asignaciones->fecha = $fecha;
                $asignaciones->id_ruta = $ruta->id;
                $asignaciones->id_vehiculo = $request->input('id_vehiculo');
                $asignaciones->id_paquete = $paquete['id'];
                $asignaciones->prioridad = $paquete['prioridad'];
                $asignaciones->id_departamento = $detalle->id_departamento;
                $asignaciones->id_municipio = $detalle->id_municipio;
                $asignaciones->id_direccion = $detalle->id_direccion_entrega;
                $asignaciones->id_estado = 1;
                $asignaciones->destino = $detalle->direccion;
                $asignaciones->save();

                // Actualizar el estado del paquete a "En Ruta de Entrega"
                DB::table('paquetes')
                    ->where('id', $paquete['id'])
                    ->update(['id_estado_paquete' => 5]);

                $kardexSalida = new Kardex();
                $kardexSalida->id_paquete = $detalle->id_paquete;
                $kardexSalida->id_orden = $detalle->id_orden;
                $kardexSalida->cantidad = 1;
                $kardexSalida->numero_ingreso = $detalle->numero_seguimiento;
                $kardexSalida->tipo_movimiento = 'SALIDA';
                $kardexSalida->tipo_transaccion = 'ALMACENADO';
                $kardexSalida->fecha = now();
                $kardexSalida->save(); // Guardar el registro de SALIDA en kardex

                // Crear el objeto Kardex para ENTRADA en ASIGNADO_RUTA
                $kardexEntrada = new Kardex();
                $kardexEntrada->id_paquete = $detalle->id_paquete;
                $kardexEntrada->id_orden = $detalle->id_orden;
                $kardexEntrada->cantidad = 1;
                $kardexEntrada->numero_ingreso = $detalle->numero_seguimiento;
                $kardexEntrada->tipo_movimiento = 'ENTRADA';
                $kardexEntrada->tipo_transaccion = 'ASIGNADO_RUTA';
                $kardexEntrada->fecha = now();
                $kardexEntrada->save(); // Guardar el registro de ENTRADA en kardex

            }

            DB::commit();
            return response()->json(['message' => 'Ruta creada y asignada correctamente'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Error al asignar rutas'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AsignacionRutas  $asignacionRutas
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $asignacionRuta = AsignacionRutas::find($id);
        // obtiene el tama;o del paquete de la tabla paquetes.
        $paquete = Paquete::find($asignacionRuta->id_paquete);
        // de la relacion de paquetes con tamano paquete obtenemos el nombre del tamano
        $tamano = $paquete->tamanoPaquete->nombre;
        $asignacionRuta->tamano = $tamano;
        // agrega el tamano a la respuesta.
        if (!$asignacionRuta) {
            $data = [
                'message' => 'asignacion de Ruta no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'asignacionRuta' => $asignacionRuta,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AsignacionRutas  $asignacionRutas
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_bodega' => 'required|exists:bodegas,id',
            'fecha_programada' => 'required|date',
            'id_vehiculo' => 'required|integer|exists:vehiculos,id',
            'paquetes' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Buscar la ruta existente
            $ruta = Rutas::find($id);
            $ruta->id_bodega = $request->input('id_bodega');
            $ruta->fecha_programada = $request->input('fecha_programada');
            $ruta->save();

            // Actualizar o crear asignaciones para los paquetes
            $paquetes = $request->input('paquetes');
            $fecha = now();

            $prioridades = array_column($paquetes, 'prioridad');
            $conteo_prioridades = array_count_values($prioridades);

            foreach ($paquetes as $paquete) {

                if ($conteo_prioridades[$paquete['prioridad']] > 1) {
                    DB::rollBack();
                    return response()->json(['message' => 'Debe de establecer una proridad unica para cada paquete'], 500);
                }

                // Obtener el detalle del paquete
                $detalle = DB::table('detalle_orden')
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
                    ->where('detalle_orden.id_paquete', $paquete["id"])
                    ->first();

                // Verificar si ya existe una asignación para el paquete
                $asignaciones = AsignacionRutas::where('id_paquete', $paquete['id'])
                    ->where('id_ruta', $ruta->id)
                    ->first();

                if ($asignaciones) {
                    // Actualizar asignación existente
                    $asignaciones->id_vehiculo = $request->input('id_vehiculo');
                    $asignaciones->prioridad = $paquete['prioridad'];
                    $asignaciones->fecha = $fecha;
                    $asignaciones->id_departamento = $detalle->id_departamento;
                    $asignaciones->id_municipio = $detalle->id_municipio;
                    $asignaciones->id_direccion = $detalle->id_direccion_entrega;
                    $asignaciones->destino = $detalle->direccion;
                    $asignaciones->save();
                } else {
                    // Crear una nueva asignación si no existe
                    $asignaciones = new AsignacionRutas();
                    $asignaciones->codigo_unico_asignacion = $ruta->nombre;
                    $asignaciones->fecha = $fecha;
                    $asignaciones->id_ruta = $ruta->id;
                    $asignaciones->id_vehiculo = $request->input('id_vehiculo');
                    $asignaciones->id_paquete = $paquete['id'];
                    $asignaciones->prioridad = $paquete['prioridad'];
                    $asignaciones->id_departamento = $detalle->id_departamento;
                    $asignaciones->id_municipio = $detalle->id_municipio;
                    $asignaciones->id_direccion = $detalle->id_direccion_entrega;
                    $asignaciones->id_estado = 1;
                    $asignaciones->destino = $detalle->direccion;
                    $asignaciones->save();

                    // Actualizar Kardex de SALIDA
                    $kardexSalida = new Kardex();
                    $kardexSalida->id_paquete = $detalle->id_paquete;
                    $kardexSalida->id_orden = $detalle->id_orden;
                    $kardexSalida->cantidad = 1;
                    $kardexSalida->numero_ingreso = $detalle->numero_seguimiento;
                    $kardexSalida->tipo_movimiento = 'SALIDA';
                    $kardexSalida->tipo_transaccion = 'ALMACENADO';
                    $kardexSalida->fecha = now();
                    $kardexSalida->save();

                    // Actualizar Kardex de ENTRADA
                    $kardexEntrada = new Kardex();
                    $kardexEntrada->id_paquete = $detalle->id_paquete;
                    $kardexEntrada->id_orden = $detalle->id_orden;
                    $kardexEntrada->cantidad = 1;
                    $kardexEntrada->numero_ingreso = $detalle->numero_seguimiento;
                    $kardexEntrada->tipo_movimiento = 'ENTRADA';
                    $kardexEntrada->tipo_transaccion = 'ASIGNADO_RUTA';
                    $kardexEntrada->fecha = now();
                    $kardexEntrada->save();
                }
            }

            DB::commit();
            return response()->json(['message' => 'Ruta actualizada y asignaciones guardadas correctamente'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar la ruta'], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AsignacionRutas  $asignacionRutas
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $asignacionRuta = AsignacionRutas::find($id);

        if (!$asignacionRuta) {
            $data = [
                'message' => 'asignacion Ruta no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        // en lugar de hacer el delete, cambiamos el estado.
        $asignacionRuta->id_estado = 2;
        $asignacionRuta->save();
        $data = [
            'message' => 'Asignacion de Ruta eliminada',
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    // funcion para determinar los estados de los paquetes asignados a una ruta, recibe el id de la ruta como parámetro.
    public function estadoPaquetes($id)
    {
        // Obtener todas las asignaciones de la ruta específica
        $asignacionesRuta = AsignacionRutas::where('id_ruta', $id)->get();

        if ($asignacionesRuta->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron asignaciones para la ruta especificada',
                'status' => 404
            ], 404);
        }

        // Preparar datos de salida
        $paquetes = $asignacionesRuta->map(function ($asignacion) {
            return [
                'codigo_unico_asignacion' => $asignacion->codigo_unico_asignacion,
                'placa_vehiculo' => $asignacion->vehiculo->placa,
                'id_paquete' => $asignacion->paquete->uuid,
                'fecha' => $asignacion->fecha,
                'estado' => $asignacion->estado_ruta->estado,
                'created_at' => $asignacion->created_at,
                'updated_at' => $asignacion->updated_at
            ];
        });

        return response()->json([
            'paquetes' => $paquetes,
            'status' => 200
        ], 200);
    }

    /**
     * Registra una entrada en el kardex.
     */
    private function registrarEntradaKardex($idPaquete, $idOrden, $numeroSeguimiento, $tipoTransaccion)
    {
        Kardex::create([
            'id_paquete' => $idPaquete,
            'id_orden' => $idOrden,
            'cantidad' => 1,
            'numero_ingreso' => $numeroSeguimiento, // Utilizar el numero_seguimiento de la orden
            'tipo_movimiento' => 'ENTRADA',
            'tipo_transaccion' => $tipoTransaccion,
            'fecha' => now(),
        ]);
    }

    /**
     * Registra una salida en el kardex.
     */
    private function registrarSalidaKardex($idPaquete, $idOrden, $numeroSeguimiento, $tipoTransaccion)
    {
        Kardex::create([
            'id_paquete' => $idPaquete,
            'id_orden' => $idOrden,
            'cantidad' => 1,
            'numero_ingreso' => $numeroSeguimiento, // Utilizar el numero_seguimiento de la orden
            'tipo_movimiento' => 'SALIDA',
            'tipo_transaccion' => $tipoTransaccion,
            'fecha' => now(),
        ]);
    }
}

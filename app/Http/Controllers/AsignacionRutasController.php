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
            $ruta->save();

            //generar codigo de ruta
            $ruta->nombre  =  'R' . str_pad($ruta->id, 12, '0', STR_PAD_LEFT);
            $ruta->save();

            $paquetes = $request->input('paquetes');

            foreach ($paquetes as $paquete) {
                $asignaciones = new AsignacionRutas();
                
                $detalle =   $detalle =  DB::table('detalle_orden')
                ->join('ordenes', 'ordenes.id', '=', 'detalle_orden.id_orden')
                ->join('direcciones', 'direcciones.id', '=', 'detalle_orden.id_direccion')
                ->select('detalle_orden.id_paquete', 'detalle_orden.id_orden', 'ordenes.numero_seguimiento', 'detalle_orden.id_direccion', 'direcciones.id_departamento', 'direcciones.id_municipio', 'direcciones.direccion')
                ->where('detalle_orden.id_paquete', $asignacion->id_paquete)->first();

                $asignaciones->codigo_unico_asignacion = $ruta->nombre;
                $asignaciones->id_ruta = $ruta->id;
                $asignaciones->id_vehiculo = $request->input('id_vehiculo');
                $asignaciones->id_paquete = $paquete['id'];
                $asignaciones->prioridad = $paquete['prioridad'];
                $asignaciones->id_departamento = $detalle->id_departamento;
                $asignaciones->id_municipio = $detalle->id_municipio;
                $asignaciones->id_direccion = $detalle->id_direccion;
                $asignaciones->destino = $detalle->direccion;
                $asignaciones->save();

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
            return response()->json(['message' => 'Error al asignar rutas', 'status' => 500],
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
        $asignacionRuta = AsignacionRutas::find($id);

        if (!$asignacionRuta) {
            $data = [
                'message' => 'asignacion de Ruta no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'codigo_unico_asignacion' => 'required|unique:asignacion_rutas,codigo_unico_asignacion,'.$id,
            'id_ruta' => 'required',
            'id_vehiculo' => 'required',
            'id_paquete' => 'required',
            'fecha' => 'required|date',
            'id_estado' => 'required'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $asignacionRuta->codigo_unico_asignacion = $request->codigo_unico_asignacion;
        $asignacionRuta->id_ruta = $request->id_ruta;
        $asignacionRuta->id_vehiculo = $request->id_vehiculo;
        $asignacionRuta->id_paquete = $request->id_paquete;
        $asignacionRuta->fecha = $request->fecha;
        $asignacionRuta->id_estado = $request->id_estado;

        $asignacionRuta->save();

        $data = [
            'message' => 'asignacion de Ruta actualizada',
            'status' => 200
        ];

        return response()->json($data, 200);
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
        $asignacionRuta->estado = 0;
        $asignacionRuta->save();
        $data = [
            'message' => 'asignacion de Ruta eliminada',
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

    public function asignarRutasPaquetes(Request $request)
    {
        // Validar la solicitud
        $validated = $request->validate([
            'id_ruta' => 'required|integer|exists:rutas,id',
            'id_vehiculo' => 'required|integer|exists:vehiculos,id',
            'id_paquete' => 'required|array|min:1',
            'id_paquete.*' => 'integer|exists:paquetes,id'
        ]);

        $idRuta = $validated['id_ruta'];
        $idVehiculo = $validated['id_vehiculo'];
        $paquetesIds = $validated['id_paquete'];
        $fechaActual = now();

        // Inicia una transacción para garantizar la integridad de los datos
        try {
            $asignaciones = DB::transaction(function () use ($idRuta, $idVehiculo, $paquetesIds, $fechaActual) {
                $asignaciones = [];
                $codigoBase = 'AR';

                // Encuentra el último código generado
                $ultimoAsignacion = AsignacionRutas::latest('id')->first();
                $ultimoCodigo = $ultimoAsignacion ? $ultimoAsignacion->codigo_unico_asignacion : $codigoBase . str_pad(0, 12, '0', STR_PAD_LEFT);

                // Extrae el número del último código (si existe) y calcula el siguiente número
                $ultimoNumero = (int) substr($ultimoCodigo, 3);
                $siguienteNumero = $ultimoNumero + 1;
                
                // Calcula el nuevo código único para la asignación
                $formatoNumero = str_pad($siguienteNumero, 12, '0', STR_PAD_LEFT);
                $codigoUnico = $codigoBase . $formatoNumero;

                foreach ($paquetesIds as $idPaquete) {
                    // Verificar si el paquete existe
                    $detalleOrden = DetalleOrden::where('id_paquete', $idPaquete)->firstOrFail();

                    // Generar el código único
                    $codigoUnico = $codigoBase . $formatoNumero;

                    // Crear la asignación de ruta
                    $asignacion = AsignacionRutas::create([
                        'codigo_unico_asignacion' => $codigoUnico,
                        'id_ruta' => $idRuta,
                        'id_vehiculo' => $idVehiculo,
                        'id_paquete' => $idPaquete,
                        'fecha' => $fechaActual,
                        'id_estado' => 1, 
                        'created_at' => $fechaActual,
                        'updated_at' => $fechaActual
                    ]);

                    // Agregar la asignación a la lista
                    $asignaciones[] = $asignacion;

                    // Obtener el número de seguimiento de la orden
                    $numeroSeguimiento = Orden::where('id', $detalleOrden->id_orden)->value('numero_seguimiento');

                    // Validar si el número de seguimiento está presente
                    if (!$numeroSeguimiento) {
                        throw new \Exception('Número de seguimiento no encontrado para la orden.');
                    }

                    // Crear el objeto Kardex para SALIDA de ALMACENADO
                    $kardexSalida = new Kardex();
                    $kardexSalida->id_paquete = $idPaquete;
                    $kardexSalida->id_orden = $detalleOrden->id_orden;
                    $kardexSalida->cantidad = 1;
                    $kardexSalida->numero_ingreso = $numeroSeguimiento;
                    $kardexSalida->tipo_movimiento = 'SALIDA';
                    $kardexSalida->tipo_transaccion = 'ALMACENADO';
                    $kardexSalida->fecha = now();
                    $kardexSalida->save(); // Guardar el registro de SALIDA en kardex

                    // Crear el objeto Kardex para ENTRADA en ASIGNADO_RUTA
                    $kardexEntrada = new Kardex();
                    $kardexEntrada->id_paquete = $idPaquete;
                    $kardexEntrada->id_orden = $detalleOrden->id_orden;
                    $kardexEntrada->cantidad = 1;
                    $kardexEntrada->numero_ingreso = $numeroSeguimiento;
                    $kardexEntrada->tipo_movimiento = 'ENTRADA';
                    $kardexEntrada->tipo_transaccion = 'ASIGNADO_RUTA';
                    $kardexEntrada->fecha = now();
                    $kardexEntrada->save(); // Guardar el registro de ENTRADA en kardex

                }

                return $asignaciones;
            });

            return response()->json([
                'message' => 'Paquetes asignados exitosamente y Kardex actualizado',
                'data' => $asignaciones
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error en la asignación de rutas y actualización de Kardex: ' . $e->getMessage());
            return response()->json([
                'message' => 'Ocurrió un error al asignar los paquetes a la ruta.',
                'error' => $e->getMessage()
            ], 500);
        }
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

<?php

namespace App\Http\Controllers;

use App\Models\AsignacionRutas;
use Illuminate\Http\Request;
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

        $asignacionrutas = AsignacionRutas::filtrar($filtros);

        $data = [
            'asignacionrutas' => $asignacionrutas,
            'status' => 200
        ];

        return response()->json($data, 200);
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo_unico_asignacion' => 'required|max:255|unique:asignacion_rutas',
            'id_ruta' => 'required',
            'id_vehiculo' => 'required',
            'id_paquete' => 'required',
            'fecha' => 'required|date',
            'id_estado' => 'required',
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $asignacionruta = AsignacionRutas::create([
            'codigo_unico_asignacion' => $request->codigo_unico_asignacion,
            'id_ruta' => $request->id_ruta,
            'id_vehiculo' => $request->id_vehiculo,
            'id_paquete' => $request->id_paquete,
            'fecha' => $request->fecha,
            'id_estado' => $request->id_estado,
        ]);

        if (!$asignacionruta) {
            $data = [
                'message' => 'Error al crear la asignacion de ruta',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        $data = [
            'asignacionruta' => $asignacionruta,
            'status' => 201
        ];

        return response()->json($data, 201);

        $validator = AsignacionRutas::validate($request->all());

        if ($validator->fails()) {
            $errors = implode('<br>', $validator->errors()->all());
            return response()->json(['error' => $errors], 400);
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
    public function destroy( $id)
    {
        $asignacionRuta = AsignacionRutas::find($id);

    if (!$asignacionRuta) {
        $data = [
            'message' => 'asignaciond Ruta no encontrada',
            'status' => 404
        ];
        return response()->json($data, 404);
    }

    $asignacionRuta->delete();

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
            'id_ruta' => 'required|integer',
            'id_vehiculo' => 'required|integer',
            'id_paquete' => 'required|array|min:1',
            'id_paquete.*' => 'integer'
        ]);

        $idRuta = $validated['id_ruta'];
        $idVehiculo = $validated['id_vehiculo'];
        $paquetesIds = $validated['id_paquete'];
        $fechaActual = now();

        // Inicia una transacción para garantizar la integridad de los datos
        $asignaciones = DB::transaction(function () use ($idRuta, $idVehiculo, $paquetesIds, $fechaActual) {
        $asignaciones = [];
        $codigoBase = 'AR-';

        // Encuentra el último código generado
        $ultimoCodigo = AsignacionRutas::latest('id')
            ->first()
            ->codigo_unico_asignacion;

        // Extrae el número del último código (si existe) y calcula el siguiente número
        $ultimoNumero = $ultimoCodigo ? (int) substr($ultimoCodigo, 3) : 0;
        $siguienteNumero = $ultimoNumero + 1;
        $formatoNumero = str_pad($siguienteNumero, 12, '0', STR_PAD_LEFT);

        foreach ($paquetesIds as $idPaquete) {
            $codigoUnico = $codigoBase . $formatoNumero;

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

            // Incrementa el número para el próximo código
            $siguienteNumero++;
            $formatoNumero = str_pad($siguienteNumero, 12, '0', STR_PAD_LEFT);
        }

            return $asignaciones;
        });

        return response()->json([
            'message' => 'Paquetes asignados exitosamente',
            'data' => $asignaciones
        ], 200);
    }

}

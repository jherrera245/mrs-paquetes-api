<?php

namespace App\Http\Controllers;

use App\Models\AsignacionRutas;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AsignacionRutasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $asignacionrutas = AsignacionRutas::all();

        $data = [
            'asignacionrutas' => $asignacionrutas,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
        'empleado' => $empleado,
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
}

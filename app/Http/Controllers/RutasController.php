<?php

namespace App\Http\Controllers;

use App\Models\Rutas;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RutasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rutas = Rutas::all();

        $data = [
            'rutas' => $rutas,
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
            'id_destino' => 'required',
            'nombre' => 'required|max:255',
            'id_bodega' => 'required',
            'id_estado' => 'required',
            'distancia_km' => 'required|numeric',
            'duracion_aproximada' => 'required|numeric',
            'fecha_programada' => 'required|date'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $ruta = Rutas::create([
            'id_destino' => $request->id_destino,
            'nombre'=> $request->nombre,
            'id_bodega' => $request->id_bodega,
            'id_estado' => $request->id_estado,
            'distancia_km' => $request->distancia_km,
            'duracion_aproximada' => $request->duracion_aproximada,
            'fecha_programada' => $request->fecha_programada
        ]);

        if (!$ruta) {
            $data = [
                'message' => 'Error al crear la ruta',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        $data = [
            'ruta' => $ruta,
            'status' => 201
        ];

        return response()->json($data, 201);

        $validator = Rutas::validate($request->all());

        if ($validator->fails()) {
            $errors = implode('<br>', $validator->errors()->all());
            return response()->json(['error' => $errors], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Rutas  $rutas
     * @return \Illuminate\Http\Response
     */
    public function show($ruta)
    {
        $ruta = Rutas::find($ruta);

        if (!$ruta) {
            $data = [
                'message' => 'ruta no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'ruta' => $ruta,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Rutas  $rutas
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $ruta = Rutas::find($id);

        if (!$ruta) {
            $data = [
                'message' => 'ruta no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'id_destino' => 'required',
            'nombre' => 'required|max:255',
            'id_bodega' => 'required',
            'id_estado' => 'required',
            'distancia_km' => 'required|numeric',
            'duracion_aproximada' => 'required|numeric',
            'fecha_programada' => 'required|date'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $ruta->id_destino = $request->id_destino;
        $ruta->nombre = $request->nombre;
        $ruta->id_bodega = $request->id_bodega;
        $ruta->id_estado = $request->id_estado;
        $ruta->distancia_km = $request->distancia_km;
        $ruta->duracion_aproximada = $request->duracion_aproximada;
        $ruta->fecha_programada = $request->fecha_programada;
        $ruta->save();

        $data = [
            'message' => 'ruta actualizada',
            'ruta' => $ruta,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rutas  $rutas
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ruta = Rutas::find($id);

        if (!$ruta) {
            $data = [
                'message' => 'ruta no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $ruta->delete();

        $data = [
            'message' => 'ruta eliminada',
            'status' => 200
        ];

        return response()->json($data, 200);
    }
}

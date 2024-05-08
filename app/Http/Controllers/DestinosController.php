<?php

namespace App\Http\Controllers;

use App\Models\Destinos;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DestinosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $destinos = Destinos::all();

        $data = [
            'destinos' => $destinos,
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
            'nombre' => 'required|max:255',
            'descripcion' => 'required|max:255',
            'id_departamento' => 'required',
            'id_municipio' => 'required',
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

        $destino = Destinos::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'id_departamento' => $request->id_departamento,
            'id_municipio' => $request->id_municipio,
            'id_estado' => $request->id_estado
        ]);

        if (!$destino) {
            $data = [
                'message' => 'Error al crear el destino',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        $data = [
            'destino' => $destino,
            'status' => 201
        ];

        return response()->json($data, 201);

        $validator = Destinos::validate($request->all());

        if ($validator->fails()) {
            $errors = implode('<br>', $validator->errors()->all());
            return response()->json(['error' => $errors], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Destinos  $destinos
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $destino = Destinos::find($id);

        if (!$destino) {
            $data = [
                'message' => 'destino no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'destino' => $destino,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Destinos  $destinos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $destino = Destinos::find($id);

    if (!$destino) {
        $data = [
            'message' => 'destino no encontrado',
            'status' => 404
        ];
        return response()->json($data, 404);
    }

    $validator = Validator::make($request->all(), [
        'nombre' => 'required|max:255',
        'descripcion' => 'required|max:255',
        'id_departamento' => 'required',
        'id_municipio' => 'required',
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

    $destino->nombre = $request->nombre;
    $destino->descripcion = $request->descripcion;
    $destino->id_departamento = $request->id_departamento;
    $destino->id_municipio = $request->id_municipio;
    $destino->id_estado = $request->id_estado;

    $destino->save();

    $data = [
        'message' => 'destino actualizado',
        'destino' => $destino,
        'status' => 200
    ];

    return response()->json($data, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Destinos  $destinos
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $destino = Destinos::find($id);

    if (!$destino) {
        $data = [
            'message' => 'destino no encontrado',
            'status' => 404
        ];
        return response()->json($data, 404);
    }

    $destino->delete();

    $data = [
        'message' => 'destino eliminado',
        'status' => 200
    ];

    return response()->json($data, 200);
    }
}

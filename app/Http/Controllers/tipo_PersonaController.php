<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\tipo_persona;
use Illuminate\Support\Facades\Validator;

class tipo_personaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tipo_persona = tipo_persona::all();
        return response()->json($tipo_persona);
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
            'nombre' => 'required|string|unique:tipo_persona,nombre',
            'descripcion'=>'required'
            // Agrega aquí más reglas de validación según tus necesidades
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        $tipo_persona = tipo_persona::create($request->all());

        return response()->json($tipo_persona, 201);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, tipo_persona $tipo_persona)
    {
        $data = $request ->only('nombre','descripcion');

        $validator = Validator::make($data, [
            'nombre' => 'required|unique:tipo_persona,nombre,' . $tipo_persona->id,
            'descripcion' => 'required' ,
            // Agrega aquí más reglas de validación según tus necesidades
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        if ($tipo_persona->update($request->all())) {
            return response()->json($tipo_persona, 200);
        }

        return response()->json(["error" => "Tipo de persona no actualizado"], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(tipo_persona $tipo_persona)
    {
        if ($tipo_persona->delete()) {
            return response()->json(["success" => "Tipo de persona eliminado correctamente"], 200);
        }
        return response()->json(["error" => "No se pudo eliminar el tipo de persona"], 400);
    }
}


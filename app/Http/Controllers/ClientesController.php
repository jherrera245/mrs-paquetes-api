<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clientes;
use Illuminate\Support\Facades\Validator;

class ClientesController extends Controller
{
    public function index()
    {
        $clientes = Clientes::all();
        return response()->json($clientes);
    }

    public function store(Request $request)
    {
        $data = $request->only(
            'nombre',
		'apellido',
        'nombre_comercial',
        'email',
        'telefono',
        'id_tipo_persona',
        'es_contribuyente',
        'id_genero',
        'dui',
       'fecha_registro',
        'id_estado',
        'id_departamento',
        'id_municipio'
        );
        $validator = Validator::make($data, [
            'nombre' => 'required',
            'apellido' => 'required',
            'nombre_comercial' => 'nullable',
            'email' => 'required|unique:clientes,email',
            'dui' => 'nullable|unique:clientes,dui',
            'telefono' => 'nullable',
            'id_tipo_persona' => 'required|exists:tipo_persona,id',
            'es_contribuyente' => 'required',
            'id_genero' => 'required|exists:genero,id',
           'fecha_registro' => 'required',
            'id_estado' => 'required|exists:estado_clientes,id',
            'id_departamento' => 'required|exists:departamento,id',
            'id_municipio' => 'required|exists:municipios,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        $clientes = Clientes::create($request->all());

        return response()->json($clientes, 201);
    }

    public function update(Request $request, Clientes $clientes)
    {
        $data = $request->only(
            'nombre',
		'apellido',
        'nombre_comercial',
        'email',
        'telefono',
        'id_tipo_persona',
        'es_contribuyente',
        'id_genero',
        'dui',
       'fecha_registro',
        'id_estado',
        'id_departamento',
        'id_municipio'
        );
        $validator = Validator::make($data, [
            'nombre' => 'required',
            'apellido' => 'required',
            'nombre_comercial' => 'nullable',
            'email' => 'required|unique:clientes,email,'.$clientes->id,
            'dui' => 'nullable|unique:clientes,dui,'.$clientes->id,
            'telefono' => 'nullable',
            'id_tipo_persona' => 'required|exists:tipo_persona,id',
            'es_contribuyente' => 'required',
            'id_genero' => 'required|exists:genero,id',
           'fecha_registro' => 'required',
            'id_estado' => 'required|exists:estado_clientes,id',
            'id_departamento' => 'required|exists:departamento,id',
            'id_municipio' => 'required|exists:municipios,id',
        ]);


        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        if ($clientes->update($request->all())) {
            return response()->json($clientes, 200);
        }

        return response()->json(["error" => "Cliente no actualizado"], 400);
    }

    public function destroy(Clientes $clientes)
    {
        if ($clientes->delete()) {
            return response()->json(["success" => "Cliente eliminado correctamente"], 200);
        }
        return response()->json(["error" => "No se pudo eliminar el cliente"], 400);
    }
}
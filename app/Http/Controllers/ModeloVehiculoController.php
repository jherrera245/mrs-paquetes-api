<?php

namespace App\Http\Controllers;

use App\Models\ModeloVehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ModeloVehiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Obtiene todos los modelos de vehículos y los devuelve como JSON
        $modelos = ModeloVehiculo::all();
        return response()->json($modelos);
    }

    public function show(ModeloVehiculo $modeloVehiculo)
    {
        return response()->json($modeloVehiculo);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Valida los datos del formulario para crear un nuevo modelo de vehículo
        $data = $request->only('nombre', 'descripcion');

        $validator = Validator::make($data, [
            'nombre' => 'required|unique:modelos,nombre',
            'descripcion' => 'required',
        ]);

        // Si la validación falla, devuelve un error de validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        // Crea un nuevo modelo de vehículo y lo devuelve como JSON
        $modelo = ModeloVehiculo::create($request->all());
        return response()->json($modelo, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ModeloVehiculo $modeloVehiculo)
    {
        // Valida los datos del formulario para actualizar un modelo de vehículo existente
        $data = $request->only('nombre', 'descripcion');

        $validator = Validator::make($data, [
            'nombre' => 'required|unique:modelos,nombre,' . $modeloVehiculo->id,
            'descripcion' => 'required',
        ]);

        // Si la validación falla, devuelve un error de validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }


        // Actualiza el modelo de vehículo y lo devuelve como JSON
        if ($modeloVehiculo->update($request->all())) {
            return response()->json($modeloVehiculo, 200);
        }

        // Si no se puede actualizar, devuelve un mensaje de error
        return response()->json(["error" => "Modelo de vehículo no actualizado"], 200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ModeloVehiculo  $modeloVehiculo
     * @return \Illuminate\Http\Response
     */
    public function destroy(ModeloVehiculo $modeloVehiculo)
    {
        // Elimina un modelo de vehículo existente y devuelve un mensaje de éxito o error
        if ($ModeloVehiculo->delete()) {
            return response()->json(["success" => "Modelo de vehículo eliminado correctamente"], 200);
        }
        return response()->json(["error" => "Error al eliminar el modelo de vehículo"], 400);
    }
}

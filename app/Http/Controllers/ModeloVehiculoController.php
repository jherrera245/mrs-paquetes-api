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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'nombre'
        ]);

        $perPage = $request->input('per_page', 10);

        $modelosQuery = ModeloVehiculo::search($filters);

        $modelos = $modelosQuery->paginate($perPage);

        return response()->json($modelos);
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
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|unique:modelos,nombre',
            'descripcion' => 'required',
        ]);

        // Si la validación falla, devuelve un error de validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        // Crea un nuevo modelo de vehículo y lo devuelve como JSON
        $modelo = ModeloVehiculo::create($request->all());
        return response()->json($modelo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ModeloVehiculo  $modeloVehiculo
     * @return \Illuminate\Http\Response
     */
    public function show(ModeloVehiculo $modeloVehiculo)
    {
        return response()->json($modeloVehiculo, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ModeloVehiculo  $modeloVehiculo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ModeloVehiculo $modeloVehiculo)
    {
        // Valida los datos del formulario para actualizar un modelo de vehículo existente
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|unique:modelos,nombre,' . $modeloVehiculo->id,
            'descripcion' => 'required',
        ]);

        // Si la validación falla, devuelve un error de validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        // Actualiza el modelo de vehículo y lo devuelve como JSON
        $modeloVehiculo->update($request->all());
        return response()->json($modeloVehiculo, 200);
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
        $modeloVehiculo->delete();
        return response()->json(["success" => "Modelo de vehículo eliminado correctamente"], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\MarcaVehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MarcaVehiculoController extends Controller
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

        $marcasQuery = MarcaVehiculo::search($filters);

        $marcas = $marcasQuery->paginate($perPage);

        return response()->json($marcas);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Valida los datos del formulario para crear una nueva marca de vehículo
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|unique:marcas,nombre',
            'descripcion' => 'required',
        ]);

        // Si la validación falla, devuelve un error de validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        // Crea una nueva marca de vehículo y la devuelve como JSON
        $marca = MarcaVehiculo::create($request->all());
        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MarcaVehiculo  $marcaVehiculo
     * @return \Illuminate\Http\Response
     */
    public function show(MarcaVehiculo $marcaVehiculo)
    {
        return response()->json($marcaVehiculo, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MarcaVehiculo  $marcaVehiculo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MarcaVehiculo $marcaVehiculo)
    {
        // Valida los datos del formulario para actualizar una marca de vehículo existente
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|unique:marcas,nombre,' . $marcaVehiculo->id,
            'descripcion' => 'required',
        ]);

        // Si la validación falla, devuelve un error de validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        // Actualiza la marca de vehículo y la devuelve como JSON
        $marcaVehiculo->update($request->all());
        return response()->json($marcaVehiculo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MarcaVehiculo  $marcaVehiculo
     * @return \Illuminate\Http\Response
     */
    public function destroy(MarcaVehiculo $marcaVehiculo)
    {
        // Elimina una marca de vehículo existente y devuelve un mensaje de éxito o error
        $marcaVehiculo->delete();
        return response()->json(["success" => "Marca de vehículo eliminada correctamente"], 200);
    }
}

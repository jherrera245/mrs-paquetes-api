<?php

namespace App\Http\Controllers;

use App\Models\marcaVehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MarcaVehiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Obtiene todas las marcas de vehículos y las devuelve como JSON
        $marcas = marcaVehiculo::all();
        return response()->json($marcas);
    }

    public function show(marcaVehiculo $marcaVehiculo)
    {
        return response()->json($marcaVehiculo, 200);
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
        $data = $request->only('nombre', 'descripcion');

        $validator = Validator::make($data, [
            'nombre' => 'required|unique:marcas,nombre',
            'descripcion' => 'required',
        ]);

        // Si la validación falla, devuelve un error de validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        // Crea una nueva marca de vehículo y la devuelve como JSON
        $marca = marcaVehiculo::create($request->all());
        return response()->json($marca, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\marcaVehiculo  $marcaVehiculo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, marcaVehiculo $marcaVehiculo)
    {
        // Valida los datos del formulario para actualizar una marca de vehículo existente
        $data = $request->only('nombre', 'descripcion');

        $validator = Validator::make($data, [
            'nombre' => 'required|unique:marcas,nombre,' . $marcaVehiculo->id,
            'descripcion' => 'required',
        ]);

        // Si la validación falla, devuelve un error de validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        // Actualiza la marca de vehículo y la devuelve como JSON
        if ($marcaVehiculo->update($request->all())) {
            return response()->json($marcaVehiculo, 200);
        }

        // Si no se puede actualizar, devuelve un mensaje de error
        return response()->json(["error" => "Marca de vehículo no actualizada"], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\marcaVehiculo  $marcaVehiculo
     * @return \Illuminate\Http\Response
     */
    public function destroy(marcaVehiculo $marcaVehiculo)
    {
        // Elimina una marca de vehículo existente y devuelve un mensaje de éxito o error
        if ($marcaVehiculo->delete()) {
            return response()->json(["success" => "Marca de vehículo eliminada correctamente"], 200);
        }
        return response()->json(["error" => "Error al eliminar la marca de vehículo"], 400);
    }
}

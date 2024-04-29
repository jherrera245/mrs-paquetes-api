<?php

namespace App\Http\Controllers;

use App\Models\vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Obtiene todos los vehículos y los devuelve como JSON
        $vehiculos = Vehiculo::all();
        return response()->json($vehiculos);
    }

    public function show(vehiculo $vehiculo)
    {
        return response()->json($vehiculo, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Valida los datos del formulario para crear un nuevo vehículo
        $data = $request->only(
            'id_empleado_conductor',
            'id_empleado_apoyo',
            'placa',
            'capacidad_carga',
            'id_estado',
            'id_marca',
            'id_modelo',
            'year_fabricacion'
        );

        $validator = Validator::make($data, [
            'id_empleado_conductor' => 'required|exists:empleados,id',
            'id_empleado_apoyo' => 'nullable|exists:empleados,id',
            'placa' => 'required|unique:vehiculos',
            'capacidad_carga' => 'required|numeric',
            'id_estado' => 'required|exists:estados,id',
            'id_marca' => 'required|exists:marcas,id',
            'id_modelo' => 'required|exists:modelos,id',
            'year_fabricacion' => 'required|integer',
        ]);

        // Si la validación falla, devuelve un error de validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        // Crea un nuevo vehículo y lo devuelve como JSON
        $vehiculo = Vehiculo::create($request->all());
        return response()->json($vehiculo, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\vehiculo  $vehiculo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, vehiculo $vehiculo)
    {
        // Valida los datos del formulario para actualizar un vehículo existente
        $data = $request->only(
            'id_empleado_conductor',
            'id_empleado_apoyo',
            'placa',
            'capacidad_carga',
            'id_estado',
            'id_marca',
            'id_modelo',
            'year_fabricacion'
        );

        $validator = Validator::make($data, [
            'id_empleado_conductor' => 'required|exists:empleados,id',
            'id_empleado_apoyo' => 'nullable|exists:empleados,id',
            'placa' => 'required|unique:vehiculos,placa,' . $vehiculo->id,
            'capacidad_carga' => 'required',
            'id_estado' => 'required|exists:estados,id',
            'id_marca' => 'required|exists:marcas,id',
            'id_modelo' => 'required|exists:modelos,id',
            'year_fabricacion' => 'required',
        ]);

        // Si la validación falla, devuelve un error de validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        // Actualiza el vehículo y lo devuelve como JSON
        if ($vehiculo->update($request->all())) {
            return response()->json($vehiculo, 200);
        }

        // Si no se puede actualizar, devuelve un mensaje de error
        return response()->json(['error' => "Vehiculo no actualizado"], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\vehiculo  $vehiculo
     * @return \Illuminate\Http\Response
     */
    public function destroy(vehiculo $vehiculo)
    {
        // Elimina un vehículo existente y devuelve un mensaje de éxito o error
        if ($vehiculo->delete()) {
            return response()->json(["success" => "Vehículo eliminado correctamente"], 200);
        }
        return response()->json(["error" => "Error al eliminar el vehículo"], 400);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Definir el número de elementos por página con un valor predeterminado de 10
        $perPage = $request->input('per_page', 10);

        // Obtener los vehículos paginados
        $vehiculos = Vehiculo::paginate($perPage);

        return response()->json($vehiculos);
    }


    public function show(Vehiculo $vehiculo)
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
            'id_empleado_conductor' => 'required|exists:empleados,id|integer|min:1',
            'id_empleado_apoyo' => 'nullable|exists:empleados,id|integer|min:1',
            'placa' => 'required|unique:vehiculos|regex:/^(?=.*[0-9])[A-Z0-9]{1,7}$/',
            'capacidad_carga' => 'required|numeric|min:0',
            'id_estado' => 'required|exists:estado_vehiculos,id|integer|min:1',
            'id_marca' => 'required|exists:marcas,id|integer|min:1',
            'id_modelo' => 'required|exists:modelos,id|integer|min:1',
            'year_fabricacion' => 'required|integer|between:1900,' . date('Y'),
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
    public function update(Request $request, Vehiculo $vehiculo)
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
            'id_empleado_conductor' => 'required|exists:empleados,id|integer|min:1',
            'id_empleado_apoyo' => 'nullable|exists:empleados,id|integer|min:1',
            'placa' => 'required|unique:vehiculos,placa,' . $vehiculo->id . '|regex:/^(?=.*[0-9])[A-Z0-9]{1,7}$/',
            'capacidad_carga' => 'required|numeric|min:0',
            'id_estado' => 'required|exists:estado_vehiculos,id|integer|min:1',
            'id_marca' => 'required|exists:marcas,id|integer|min:1',
            'id_modelo' => 'required|exists:modelos,id|integer|min:1',
            'year_fabricacion' => 'required|integer|between:1900,' . date('Y'),
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
        return response()->json(['error' => "Vehiculo no actualizado"], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\vehiculo  $vehiculo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vehiculo $vehiculo)
    {
        // Elimina un vehículo existente y devuelve un mensaje de éxito o error
        if ($vehiculo->delete()) {
            return response()->json(["success" => "Vehículo eliminado correctamente"], 200);
        }
        return response()->json(["error" => "Error al eliminar el vehículo"], 400);
    }
}

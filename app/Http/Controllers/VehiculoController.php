<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\ModeloVehiculo;
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
        $filters = $request->only([
            'conductor',
            'apoyo',
            'placa',
            'capacidad_carga',
            'estado',
            'marca',
            'modelo',
            'year_fabricacion',
            'palabra_clave'
        ]);

        $per_page = $request->input('per_page', 10);

        $vehiculosQuery = Vehiculo::search($filters);

        // Aplicar paginación después de la búsqueda
        $vehiculos = $vehiculosQuery->paginate($per_page);

        // Verificar si hay resultados
        if ($vehiculos->isEmpty()) {
            return response()->json(['message' => 'No se encontraron coincidencias.'], 404);
        }

        // Transformar los resultados según sea necesario
        $vehiculos->getCollection()->transform(function ($vehiculo) {
            return $this->transformVehiculo($vehiculo);
        });

        return response()->json($vehiculos, 200);
    }

    public function show($id)
    {
        $vehiculo = Vehiculo::with(['conductor', 'apoyo', 'estado', 'marca', 'modelo'])->find($id);

        if (!$vehiculo) {
            return response()->json(['error' => 'Vehículo no encontrado'], 404);
        }

        return response()->json($this->transformVehiculo($vehiculo), 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_empleado_conductor' => 'required|exists:empleados,id|integer|min:1',
            'id_empleado_apoyo' => 'nullable|exists:empleados,id|integer|min:1',
            'placa' => 'required|unique:vehiculos|regex:/^(?=.*[0-9])[A-Z0-9]{1,7}$/',
            'capacidad_carga' => 'required|numeric|min:0',
            'id_estado' => 'required|exists:estado_vehiculos,id|integer|min:1',
            'id_marca' => 'required|exists:marcas,id|integer|min:1',
            'id_modelo' => 'required|exists:modelos,id|integer|min:1',
            'year_fabricacion' => 'required|integer|between:1900,' . date('Y'),
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        // Verificar que el modelo pertenece a la marca seleccionada
        $modelo = ModeloVehiculo::find($request->id_modelo);
        if ($modelo->id_marca != $request->id_marca) {
            return response()->json(['error' => 'El modelo seleccionado no pertenece a la marca elegida'], 400);
        }

        $vehiculo = Vehiculo::create($request->all());
        $vehiculo->load(['conductor', 'apoyo', 'estado', 'marca', 'modelo']);

        return response()->json($this->transformVehiculo($vehiculo), 201);
    }

    public function update(Request $request, $id)
    {
        $vehiculo = Vehiculo::find($id);

        if (!$vehiculo) {
            return response()->json(['error' => 'Vehículo no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_empleado_conductor' => 'required|exists:empleados,id|integer|min:1',
            'id_empleado_apoyo' => 'nullable|exists:empleados,id|integer|min:1',
            'placa' => 'required|unique:vehiculos,placa,' . $vehiculo->id . '|regex:/^(?=.*[0-9])[A-Z0-9]{1,7}$/',
            'capacidad_carga' => 'required|numeric|min:0',
            'id_estado' => 'required|exists:estado_vehiculos,id|integer|min:1',
            'id_marca' => 'required|exists:marcas,id|integer|min:1',
            'id_modelo' => 'required|exists:modelos,id|integer|min:1',
            'year_fabricacion' => 'required|integer|between:1900,' . date('Y'),
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        $vehiculo->update($request->all());
        $vehiculo->load(['conductor', 'apoyo', 'estado', 'marca', 'modelo']);

        return response()->json($this->transformVehiculo($vehiculo), 200);
    }

    public function destroy($id)
    {
        $vehiculo = Vehiculo::find($id);

        if (!$vehiculo) {
            return response()->json(['error' => 'Vehículo no encontrado'], 404);
        }

        $vehiculo->delete();

        return response()->json(["success" => "Vehículo eliminado correctamente"], 200);
    }

    private function transformVehiculo(Vehiculo $vehiculo)
    {
        return [
            'id' => $vehiculo->id,
            'conductor' => $vehiculo->conductor ? $vehiculo->conductor->nombres . ' ' . $vehiculo->conductor->apellidos : null,
            'apoyo' => $vehiculo->apoyo ? $vehiculo->apoyo->nombres . ' ' . $vehiculo->apoyo->apellidos : null,
            'placa' => $vehiculo->placa,
            'capacidad_carga' => $vehiculo->capacidad_carga . ' T',
            'estado' => $vehiculo->estado ? $vehiculo->estado->estado : null,
            'marca' => $vehiculo->marca ? $vehiculo->marca->nombre : null,
            'modelo' => $vehiculo->modelo ? $vehiculo->modelo->nombre : null,
            'year_fabricacion' => $vehiculo->year_fabricacion,
            'created_at' => $vehiculo->created_at,
            'updated_at' => $vehiculo->updated_at,
        ];
    }

    public function getModelosByMarca($marcaId)
    {
        $modelos = ModeloVehiculo::where('id_marca', $marcaId)->get();
        return response()->json($modelos);
    }

}

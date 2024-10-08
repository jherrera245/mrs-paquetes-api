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
            'id_bodega',
            'estado',
            'marca',
            'modelo',
            'year_fabricacion',
            'palabra_clave',
            'tipo',  // Agregar filtro por tipo
        ]);

        $perPage = $request->query('per_page', 10); // Paginación configurable

        try {
            $vehiculosQuery = Vehiculo::search($filters);

            // Aplicar paginación después de la búsqueda
            $vehiculos = $vehiculosQuery->paginate($perPage);

            // Verificar si hay resultados
            if ($vehiculos->isEmpty()) {
                return response()->json(['mensaje' => 'No se encontraron coincidencias.'], 404);
            }

            // Transformar los resultados según sea necesario
            $vehiculos->getCollection()->transform(function ($vehiculo) {
                return $this->transformVehiculo($vehiculo);
            });

            return response()->json($vehiculos, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener la lista de vehículos.'], 500);
        }
    }


    public function show($id)
    {
        try {
            $vehiculo = Vehiculo::find($id);

            if (!$vehiculo) {
                return response()->json(['error' => 'Camión o moto no encontrado.'], 404);
            }

            return response()->json($this->transformVehiculo($vehiculo), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener el vehículo.'], 500);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipo' => 'required|in:camion,moto', // Validación para el tipo
            'id_empleado_conductor' => 'nullable|exists:empleados,id|integer|min:1',
            'id_empleado_apoyo' => 'nullable|exists:empleados,id|integer|min:1',
            'placa' => [
                'required',
                'unique:vehiculos',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->tipo === 'camion' && !preg_match('/^[CP] [A-Z0-9]{4,6}$/i', $value)) {
                        $fail('El formato de la placa para un camión debe ser como "C 7238", "P 823725", "C 5N345F". Asegúrate de incluir un espacio después de la letra y solo caracteres alfanuméricos de 4 a 6 dígitos.');
                    }
                    if ($request->tipo === 'moto' && !preg_match('/^M \d{6}$/', $value)) {
                        $fail('El formato de la placa para una moto debe ser "M 820926".');
                    }
                },
            ],
            'capacidad_carga' => 'nullable|numeric|min:0|required_if:tipo,camion', // Requerido solo si es camion
            'id_bodega' => 'nullable|exists:bodegas,id|integer|min:1|required_if:tipo,camion', // Requerido solo si es camion
            'id_estado' => 'required|exists:estado_vehiculos,id|integer|min:1',
            'id_marca' => 'required|exists:marcas,id|integer|min:1',
            'id_modelo' => 'required|exists:modelos,id|integer|min:1',
            'year_fabricacion' => 'required|integer|between:1900,' . date('Y'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errores' => $validator->errors()->all()], 400);
        }

        try {
            // IDs de los estados que permiten reasignación (mantenimiento o fuera de servicio)
            $estadosPermitidos = [1, 3]; // Suponiendo que 1 = En Mantenimiento, 3 = Fuera de Servicio

            // Verificar si el empleado conductor ya está asignado a otro vehículo activo
            $vehiculoConductorActivo = Vehiculo::where('id_empleado_conductor', $request->id_empleado_conductor)
                ->whereNotIn('id_estado', $estadosPermitidos) // Excluir vehículos en mantenimiento o fuera de servicio
                ->first();

            if ($vehiculoConductorActivo) {
                return response()->json(['error' => 'Este conductor ya está asignado a un vehículo activo.'], 400);
            }

            // Verificar si el empleado de apoyo ya está asignado a otro vehículo activo (si aplica)
            if ($request->id_empleado_apoyo) {
                $vehiculoApoyoActivo = Vehiculo::where('id_empleado_apoyo', $request->id_empleado_apoyo)
                    ->whereNotIn('id_estado', $estadosPermitidos)
                    ->first();

                if ($vehiculoApoyoActivo) {
                    return response()->json(['error' => 'Este empleado de apoyo ya está asignado a un vehículo activo.'], 400);
                }
            }
            // Verificar que el modelo pertenece a la marca seleccionada
            $modelo = ModeloVehiculo::find($request->id_modelo);
            if ($modelo->id_marca != $request->id_marca) {
                return response()->json(['error' => 'El modelo seleccionado no pertenece a la marca elegida.'], 400);
            }

            // Si es 'moto', ajustar los valores específicos
            if ($request->tipo === 'moto') {
                \Log::info('Tipo es moto, ajustando valores...');
                $request->merge([
                    'capacidad_carga' => 0.2,
                    'id_empleado_apoyo' => null,
                    'id_bodega' => null

                ]);
            }

            // Verificar datos antes de la creación
            \Log::info('Datos de vehículo antes de crear:', $request->all());

            // Crear el vehículo usando solo los campos válidos
            $vehiculo = Vehiculo::create($request->only([
                'id_empleado_conductor',
                'id_empleado_apoyo',
                'placa',
                'capacidad_carga',
                'id_bodega',
                'id_estado',
                'id_marca',
                'id_modelo',
                'year_fabricacion',
                'tipo'
            ]));

            // Cargar relaciones
            $vehiculo->load(['conductor', 'apoyo', 'estado', 'marca', 'modelo']);

            return response()->json($this->transformVehiculo($vehiculo), 201);
        } catch (\Exception $e) {
            \Log::error('Error al crear el vehículo: ' . $e->getMessage());
            return response()->json(['error' => 'Error al crear el vehículo: ' . $e->getMessage()], 500);
        }
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $vehiculo = Vehiculo::find($id);

            if (!$vehiculo) {
                return response()->json(['error' => 'Camión o moto no encontrado.'], 404);
            }

            // Validación de los datos
            $validator = Validator::make($request->all(), [
                'id_empleado_conductor' => 'sometimes|exists:empleados,id|integer|min:1', // 'sometimes' en lugar de 'required'
                'id_empleado_apoyo' => 'sometimes|exists:empleados,id|integer|min:1|required_if:tipo,camion', // Requerido solo si es camion
                'placa' => [
                    'sometimes',
                    'unique:vehiculos,placa,' . $vehiculo->id,
                    function ($attribute, $value, $fail) use ($vehiculo) {
                        if ($vehiculo->tipo === 'camion' && !preg_match('/^[CP] [A-Z0-9]{4,6}$/i', $value)) {
                            $fail('El formato de la placa para un camión debe ser como "C 7238", "P 823725", "C 5N345F". Asegúrate de incluir un espacio después de la letra y solo caracteres alfanuméricos de 4 a 6 dígitos.');
                        }
                        if ($vehiculo->tipo === 'moto' && !preg_match('/^M \d{6}$/', $value)) {
                            $fail('El formato de la placa para una moto debe ser "M 120926".');
                        }
                    },
                ],
                'capacidad_carga' => 'sometimes|numeric|min:0|required_if:tipo,camion', // Requerido solo si es camion
                'id_bodega' => 'sometimes|exists:bodegas,id|integer|min:1|required_if:tipo,camion', // Requerido solo si es camion
                'id_estado' => 'sometimes|exists:estado_vehiculos,id|integer|min:1',
                'id_marca' => 'sometimes|exists:marcas,id|integer|min:1',
                'id_modelo' => 'sometimes|exists:modelos,id|integer|min:1',
                'year_fabricacion' => 'sometimes|integer|between:1900,' . date('Y'),
            ]);

            if ($validator->fails()) {
                return response()->json(['errores' => $validator->errors()->all()], 400);
            }

            // IDs de los estados que permiten reasignación (mantenimiento o fuera de servicio)
            $estadosPermitidos = [1, 3]; // Suponiendo que 1 = En Mantenimiento, 3 = Fuera de Servicio

            // Verificar si el conductor está asignado a otro vehículo activo
            if ($request->has('id_empleado_conductor')) {
                $vehiculoConductorActivo = Vehiculo::where('id_empleado_conductor', $request->id_empleado_conductor)
                    ->whereNotIn('id_estado', $estadosPermitidos)
                    ->where('id', '!=', $vehiculo->id) // Excluir el vehículo actual
                    ->first();

                if ($vehiculoConductorActivo) {
                    return response()->json(['error' => 'Este conductor ya está asignado a otro vehículo activo.'], 400);
                }
            }

            // Verificar si el empleado de apoyo ya está asignado a otro vehículo activo (si aplica)
            if ($request->has('id_empleado_apoyo')) {
                $vehiculoApoyoActivo = Vehiculo::where('id_empleado_apoyo', $request->id_empleado_apoyo)
                    ->whereNotIn('id_estado', $estadosPermitidos)
                    ->where('id', '!=', $vehiculo->id) // Excluir el vehículo actual
                    ->first();

                if ($vehiculoApoyoActivo) {
                    return response()->json(['error' => 'Este empleado de apoyo ya está asignado a otro vehículo activo.'], 400);
                }
            }

            // Combinar los datos existentes del vehículo con los datos nuevos del request
            $data = array_merge($vehiculo->toArray(), $request->all());

            // Si es 'moto', ajustar los valores específicos
            if ($vehiculo->tipo === 'moto') {
                $data['capacidad_carga'] = 0.2;
                $data['id_empleado_apoyo'] = null;
                $data['id_bodega'] = null;
            }

            // Actualizar el vehículo con los datos combinados
            $vehiculo->update($data);
            $vehiculo->load(['conductor', 'apoyo', 'estado', 'marca', 'modelo']);

            return response()->json([
                'success' => ucfirst($vehiculo->tipo) . ' actualizado correctamente.',
                'vehiculo' => $this->transformVehiculo($vehiculo)
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar el vehículo.'], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $vehiculo = Vehiculo::find($id);

            if (!$vehiculo) {
                return response()->json(['error' => 'Camión o moto no encontrado.'], 404);
            }

            $tipo = $vehiculo->tipo;  // Obtener el tipo de vehículo antes de eliminar
            $vehiculo->delete();

            return response()->json(["success" => ucfirst($tipo) . " eliminado correctamente."], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el vehículo.'], 500);
        }
    }
    /**
     * Transformar el objeto Vehiculo para la respuesta JSON.
     *
     * @param  \App\Models\Vehiculo  $vehiculo
     * @return array
     */
    private function transformVehiculo(Vehiculo $vehiculo)
    {
        return [
            'id' => $vehiculo->id,
            'conductor' => $vehiculo->conductor ? $vehiculo->conductor->nombres . ' ' . $vehiculo->conductor->apellidos : null,
            'apoyo' => $vehiculo->apoyo ? $vehiculo->apoyo->nombres . ' ' . $vehiculo->apoyo->apellidos : null,
            'placa' => $vehiculo->placa,
            'capacidad_carga' => $vehiculo->capacidad_carga,
            'bodega' => $vehiculo->bodega ? $vehiculo->bodega->nombre : null,
            'estado' => $vehiculo->estado ? $vehiculo->estado->estado : null,
            'marca' => $vehiculo->marca ? $vehiculo->marca->nombre : null,
            'modelo' => $vehiculo->modelo ? $vehiculo->modelo->nombre : null,
            'year_fabricacion' => $vehiculo->year_fabricacion,
            'tipo' => $vehiculo->tipo, // Mostrar el tipo en la respuesta
            'created_at' => $vehiculo->created_at,
            'updated_at' => $vehiculo->updated_at,
        ];
    }

    /**
     * Obtener modelos de vehículos por marca.
     *
     * @param  int  $marcaId
     * @return \Illuminate\Http\Response
     */
    public function getModelosByMarca($marcaId)
    {
        try {
            $modelos = ModeloVehiculo::where('id_marca', $marcaId)->get();
            return response()->json($modelos);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los modelos de vehículos.'], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class UbicacionController extends Controller
{
    // Listar todas las ubicaciones con filtros de búsqueda y paginación
    public function index(Request $request)
    {
        try {
            $query = Ubicacion::with(['bodega:id,nombre', 'pasillo:id,nombre']);

            // Aplicar filtros basados en los parámetros de la solicitud
            if ($request->has('nomenclatura')) {
                $query->where('nomenclatura', 'like', '%' . $request->input('nomenclatura') . '%');
            }

            if ($request->has('id_bodega')) {
                $query->where('id_bodega', $request->input('id_bodega'));
            }

            if ($request->has('id_pasillo')) {
                $query->where('id_pasillo', $request->input('id_pasillo'));
            }

            // Paginación de resultados
            $ubicaciones = $query->paginate(10); // Cambia el número de elementos por página según sea necesario

            // Formatear resultados usando API Resource o un método de formateo
            $formattedData = $ubicaciones->map(function ($ubicacion) {
                return $ubicacion->getFormattedData(); // Usar el método del modelo para formatear los datos
            });

            return response()->json($formattedData, 200);
        } catch (Exception $e) {
            Log::error('Error en la lista de ubicaciones: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener las ubicaciones'], 500);
        }
    }

    // Mostrar una ubicación específica con manejo de errores
    public function show($id)
    {
        try {
            $ubicacion = Ubicacion::with(['bodega:id,nombre', 'pasillo:id,nombre'])->find($id);

            if (!$ubicacion) {
                return response()->json(['error' => 'Ubicación no encontrada'], 404);
            }

            return response()->json($ubicacion->getFormattedData(), 200);
        } catch (Exception $e) {
            Log::error('Error al mostrar la ubicación: ' . $e->getMessage());
            return response()->json(['error' => 'Error al mostrar la ubicación'], 500);
        }
    }

    // Crear una nueva ubicación con validación mejorada y manejo de errores
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomenclatura' => 'required|string|max:255|unique:ubicaciones,nomenclatura', // Agregar regla de unicidad
            'id_bodega' => 'required|exists:bodegas,id',
            'id_pasillo' => 'required|exists:pasillos,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            $ubicacion = Ubicacion::create($request->all());
            return response()->json(['message' => 'Ubicación creada correctamente', 'ubicacion' => $ubicacion->getFormattedData()], 201);
        } catch (Exception $e) {
            Log::error('Error al crear la ubicación: ' . $e->getMessage());
            return response()->json(['error' => 'Error al crear la ubicación'], 500);
        }
    }

    // Actualizar una ubicación existente con validación y manejo de errores
    public function update(Request $request, $id)
    {
        $ubicacion = Ubicacion::find($id);

        if (!$ubicacion) {
            return response()->json(['error' => 'Ubicación no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nomenclatura' => 'sometimes|required|string|max:255|unique:ubicaciones,nomenclatura,' . $id, // Unicidad excepto el mismo registro
            'id_bodega' => 'sometimes|required|exists:bodegas,id',
            'id_pasillo' => 'sometimes|required|exists:pasillos,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            $ubicacion->update($request->all());
            return response()->json(['message' => 'Ubicación actualizada correctamente', 'ubicacion' => $ubicacion->getFormattedData()], 200);
        } catch (Exception $e) {
            Log::error('Error al actualizar la ubicación: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar la ubicación'], 500);
        }
    }

    // Eliminar una ubicación con manejo de errores
    public function destroy($id)
    {
        $ubicacion = Ubicacion::find($id);

        if (!$ubicacion) {
            return response()->json(['error' => 'Ubicación no encontrada'], 404);
        }

        try {
            $ubicacion->delete();
            return response()->json(['message' => 'Ubicación eliminada correctamente'], 200);
        } catch (Exception $e) {
            Log::error('Error al eliminar la ubicación: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar la ubicación'], 500);
        }
    }
    public function ubicaciones()
    {
        try {
            // Seleccionar solo los campos necesarios para un dropdown
            $ubicaciones = Ubicacion::select('id', 'nomenclatura')->get();

            if ($ubicaciones->isEmpty()) {
                return response()->json(['message' => 'No se encontraron ubicaciones disponibles.'], Response::HTTP_NOT_FOUND);
            }

            return response()->json($ubicaciones, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las ubicaciones.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

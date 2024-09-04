<?php

namespace App\Http\Controllers;

use App\Models\UbicacionPaquete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UbicacionPaqueteResource;
use Illuminate\Support\Facades\Validator;
use Exception;

class UbicacionPaqueteController extends Controller
{
    /**
     * Listar todas las relaciones de ubicaciones con paquetes con paginación y filtros.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Filtrar los resultados
            $filters = $request->only(['id_paquete', 'id_ubicacion', 'estado']);

            $query = UbicacionPaquete::with(['ubicacion', 'paquete']);

            if (!empty($filters['id_paquete'])) {
                $query->where('id_paquete', $filters['id_paquete']);
            }

            if (!empty($filters['id_ubicacion'])) {
                $query->where('id_ubicacion', $filters['id_ubicacion']);
            }

            if (array_key_exists('estado', $filters) && !is_null($filters['estado'])) {
                $query->where('estado', $filters['estado']);
            }

            $ubicacionPaquetes = $query->paginate($request->input('per_page', 10));

            if ($ubicacionPaquetes->isEmpty()) {
                return response()->json(['message' => 'No se encontraron ubicaciones de paquetes.'], 404);
            }

            // Formatear los datos manualmente en el controlador
            $formattedData = $ubicacionPaquetes->map(function ($ubicacionPaquete) {
                return [
                    'id' => $ubicacionPaquete->id,
                    'paquete' => $ubicacionPaquete->paquete ? $ubicacionPaquete->paquete->descripcion_contenido : 'N/A',
                    'id_paquete' => $ubicacionPaquete->paquete ? $ubicacionPaquete->paquete->id : 'N/A',
                    'ubicacion' => $ubicacionPaquete->ubicacion ? $ubicacionPaquete->ubicacion->nomenclatura : 'N/A',
                    'id_ubicacion' => $ubicacionPaquete->ubicacion ? $ubicacionPaquete->ubicacion->id : 'N/A',
                    'estado' => $ubicacionPaquete->estado,
                ];
            });

            return response()->json($formattedData, 200);
        } catch (Exception $e) {
            Log::error('Error al listar ubicaciones de paquetes: ' . $e->getMessage());
            return response()->json(['error' => 'Error al listar ubicaciones de paquetes', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar una relación específica de ubicación con paquete.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function show($id)
    {
        try {
            // Buscar el UbicacionPaquete por ID con sus relaciones
            $ubicacionPaquete = UbicacionPaquete::with(['ubicacion', 'paquete'])->find($id);

            if (!$ubicacionPaquete) {
                return response()->json(['error' => 'Relación no encontrada'], 404);
            }

            // Formatear los datos manualmente
            $formattedData = [
                'id' => $ubicacionPaquete->id,
                'paquete' => $ubicacionPaquete->paquete ? $ubicacionPaquete->paquete->descripcion_contenido : 'N/A',
                'id_paquete' => $ubicacionPaquete->paquete ? $ubicacionPaquete->paquete->id : 'N/A',
                'ubicacion' => $ubicacionPaquete->ubicacion ? $ubicacionPaquete->ubicacion->nomenclatura : 'N/A',
                'id_ubicacion' => $ubicacionPaquete->ubicacion ? $ubicacionPaquete->ubicacion->id : 'N/A',
                'estado' => $ubicacionPaquete->estado,
            ];

            return response()->json($formattedData, 200);
        } catch (Exception $e) {
            Log::error('Error al mostrar la relación: ' . $e->getMessage());
            return response()->json(['error' => 'Error al mostrar la relación', 'details' => $e->getMessage()], 500);
        }
    }


    /**
     * Crear una nueva relación de ubicación con paquete.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_paquete' => 'required|exists:paquetes,id',
            'id_ubicacion' => 'required|exists:ubicaciones,id',
            'estado' => 'required|boolean',
        ], [
            'id_paquete.required' => 'El campo de paquete es obligatorio.',
            'id_paquete.exists' => 'El paquete seleccionado no es válido.',
            'id_ubicacion.required' => 'El campo de ubicación es obligatorio.',
            'id_ubicacion.exists' => 'La ubicación seleccionada no es válida.',
            'estado.required' => 'El campo de estado es obligatorio.',
            'estado.boolean' => 'El campo de estado debe ser verdadero o falso.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            // Verificar si ya existe una ubicación para el paquete
            $existingUbicacionPaquete = UbicacionPaquete::where('id_paquete', $request->id_paquete)->first();

            if ($existingUbicacionPaquete) {
                return response()->json(['error' => 'Este paquete ya tiene una ubicación asignada.'], 400);
            }

            // Crear la nueva relación de ubicación con paquete
            $ubicacionPaquete = UbicacionPaquete::create($request->all());

            return response()->json(['message' => 'Relación de Ubicación con Paquete creada correctamente.'], 201);
        } catch (Exception $e) {
            Log::error('Error al crear la relación: ' . $e->getMessage());
            return response()->json(['error' => 'Error al crear la relación'], 500);
        }
    }

    /**
     * Actualizar una relación existente.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $ubicacionPaquete = UbicacionPaquete::find($id);

            if (!$ubicacionPaquete) {
                return response()->json(['error' => 'Relación no encontrada'], 404);
            }

            $validator = Validator::make($request->all(), [
                'id_paquete' => 'sometimes|required|exists:paquetes,id',
                'id_ubicacion' => 'sometimes|required|exists:ubicaciones,id',
                'estado' => 'sometimes|required|boolean',
            ], [
                'id_paquete.exists' => 'El paquete seleccionado no es válido.',
                'id_ubicacion.exists' => 'La ubicación seleccionada no es válida.',
                'estado.boolean' => 'El campo de estado debe ser verdadero o falso.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            // Validar si el paquete ya tiene asignada la misma ubicación
            if (
                $request->has('id_paquete') &&
                $request->has('id_ubicacion') &&
                UbicacionPaquete::where('id_paquete', $request->id_paquete)
                ->where('id_ubicacion', $request->id_ubicacion)
                ->where('id', '!=', $id)
                ->exists()
            ) {
                return response()->json(['error' => 'Este paquete ya tiene asignada esa ubicación.'], 400);
            }

            $ubicacionPaquete->update($request->all());

            return response()->json(['message' => 'Relación de Ubicación con Paquete actualizada correctamente.'], 200);
        } catch (Exception $e) {
            Log::error('Error al actualizar la relación: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar la relación'], 500);
        }
    }

    /**
     * Eliminar una relación.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $ubicacionPaquete = UbicacionPaquete::find($id);

            if (!$ubicacionPaquete) {
                return response()->json(['error' => 'Relación no encontrada'], 404);
            }

            $ubicacionPaquete->delete();

            return response()->json(['message' => 'Relación eliminada correctamente.'], 200);
        } catch (Exception $e) {
            Log::error('Error al eliminar la relación: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar la relación'], 500);
        }
    }
}

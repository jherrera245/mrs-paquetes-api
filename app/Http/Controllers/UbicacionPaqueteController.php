<?php

namespace App\Http\Controllers;

use App\Models\UbicacionPaquete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UbicacionPaqueteController extends Controller
{
    // Listar todas las relaciones de ubicaciones con paquetes
    public function index()
    {
        $ubicacionPaquetes = UbicacionPaquete::with(['ubicacion:id,nomenclatura', 'paquete:id,descripcion_contenido'])
            ->get()
            ->map(function ($ubicacionPaquete) {
                return [
                    'id' => $ubicacionPaquete->id,
                    'paquete' => $ubicacionPaquete->paquete ? $ubicacionPaquete->paquete->descripcion_contenido : null,
                    'ubicacion' => $ubicacionPaquete->ubicacion ? $ubicacionPaquete->ubicacion->nomenclatura : null,
                    'estado' => $ubicacionPaquete->estado,
                ];
            });

        return response()->json($ubicacionPaquetes, 200);
    }

    // Mostrar una relación específica de ubicación con paquete
    public function show($id)
    {
        $ubicacionPaquete = UbicacionPaquete::with(['ubicacion:id,nomenclatura', 'paquete:id,descripcion_contenido'])->find($id);

        if (!$ubicacionPaquete) {
            return response()->json(['error' => 'Relación no encontrada'], 404);
        }

        $ubicacionPaqueteData = [
            'id' => $ubicacionPaquete->id,
            'paquete' => $ubicacionPaquete->paquete ? $ubicacionPaquete->paquete->descripcion_contenido : null,
            'ubicacion' => $ubicacionPaquete->ubicacion ? $ubicacionPaquete->ubicacion->nomenclatura : null,
            'estado' => $ubicacionPaquete->estado,
        ];

        return response()->json($ubicacionPaqueteData, 200);
    }

    // Crear una nueva relación de ubicación con paquete
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

        // Verificar si ya existe una ubicación para el paquete
        $existingUbicacionPaquete = UbicacionPaquete::where('id_paquete', $request->id_paquete)->first();

        if ($existingUbicacionPaquete) {
            return response()->json(['error' => 'Este paquete ya tiene una ubicación asignada.'], 400);
        }

        // Crear la nueva relación de ubicación con paquete
        UbicacionPaquete::create($request->all());

        return response()->json(['message' => 'Relación de Ubicación con Paquete creada correctamente.'], 201);
    }

    // Actualizar una relación existente
    public function update(Request $request, $id)
    {
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
    }

    // Eliminar una relación
    public function destroy($id)
    {
        $ubicacionPaquete = UbicacionPaquete::find($id);

        if (!$ubicacionPaquete) {
            return response()->json(['error' => 'Relación no encontrada'], 404);
        }

        $ubicacionPaquete->delete();

        return response()->json(['message' => 'Relación eliminada correctamente.'], 200);
    }
}

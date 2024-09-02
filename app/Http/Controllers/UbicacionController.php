<?php

namespace App\Http\Controllers;

use App\Models\Bodegas; 
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UbicacionController extends Controller
{
    // Listar todas las ubicaciones
    public function index()
    {
        $ubicaciones = Ubicacion::with('bodega:id,nombre')->get()->map(function ($ubicacion) {
            return [
                'id' => $ubicacion->id,
                'nomenclatura' => $ubicacion->nomenclatura,
                'bodega' => $ubicacion->bodega ? $ubicacion->bodega->nombre : null,
            ];
        });
        return response()->json($ubicaciones, 200);
    }

    // Mostrar una ubicación específica
    public function show($id)
    {
        $ubicacion = Ubicacion::with('bodega:id,nombre')->find($id);

        if (!$ubicacion) {
            return response()->json(['error' => 'Ubicación no encontrada'], 404);
        }

        $ubicacionData = [
            'id' => $ubicacion->id,
            'nomenclatura' => $ubicacion->nomenclatura,
            'bodega' => $ubicacion->bodega ? $ubicacion->bodega->nombre : null,
        ];

        return response()->json($ubicacionData, 200);
    }

    // Crear una nueva ubicación
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomenclatura' => 'required|string|max:255',
            'id_bodega' => 'required|exists:bodegas,id'  // Asegúrate de que el nombre de la tabla sea correcto
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $ubicacion = Ubicacion::create($request->all());
        return response()->json(['message' => 'Ubicación creada correctamente', 'ubicacion' => $ubicacion], 201);
    }

    // Actualizar una ubicación existente
    public function update(Request $request, $id)
    {
        $ubicacion = Ubicacion::find($id);

        if (!$ubicacion) {
            return response()->json(['error' => 'Ubicación no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nomenclatura' => 'sometimes|required|string|max:255',
            'id_bodega' => 'sometimes|required|exists:bodegas,id'  // Verifica que la tabla coincida
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $ubicacion->update($request->all());
        return response()->json(['message' => 'Ubicación actualizada correctamente', 'ubicacion' => $ubicacion], 200);
    }

    // Eliminar una ubicación
    public function destroy($id)
    {
        $ubicacion = Ubicacion::find($id);

        if (!$ubicacion) {
            return response()->json(['error' => 'Ubicación no encontrada'], 404);
        }

        $ubicacion->delete();
        return response()->json(['message' => 'Ubicación eliminada correctamente'], 200);
    }
}

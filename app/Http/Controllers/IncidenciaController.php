<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class IncidenciaController extends Controller
{
    public function index(Request $request)
    {
        // Definir el número de elementos por página con un valor predeterminado de 10
        $perPage = $request->input('per_page', 10);

        // Obtener las incidencias paginadas
        $incidencias = Incidencia::with('tipoIncidencia', 'paquete')->paginate($perPage);

        return response()->json($incidencias);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_paquete' => 'required|exists:paquetes,id',
            'fecha_hora' => 'required|date',
            'id_tipo_incidencia' => 'required|exists:tipo_incidencia,id',
            'descripcion' => 'required|string|max:1000',
            'estado' => 'required|exists:estado_incidencias,id',
            'id_usuario_reporta' => 'required|exists:users,id',
            'id_usuario_asignado' => 'nullable|exists:users,id',
            'solucion' => 'nullable|string|max:1000',
            'fecha_resolucion' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        try {
            $incidencia = Incidencia::create($request->all());
            return response()->json(['incidencia' => $incidencia], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear la incidencia', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $incidencia = Incidencia::with('tipoIncidencia', 'paquete', 'usuarioReporta', 'usuarioAsignado')->findOrFail($id);
            return response()->json($incidencia);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Incidencia no encontrada', 'message' => $e->getMessage()], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_paquete' => 'sometimes|required|exists:paquetes,id',
            'fecha_hora' => 'sometimes|required|date',
            'id_tipo_incidencia' => 'sometimes|required|exists:tipo_incidencia,id',
            'descripcion' => 'sometimes|required|string|max:1000',
            'estado' => 'sometimes|required|exists:estado_incidencias,id',
            'id_usuario_reporta' => 'sometimes|required|exists:users,id',
            'id_usuario_asignado' => 'nullable|exists:users,id',
            'solucion' => 'nullable|string|max:1000',
            'fecha_resolucion' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        try {
            $incidencia = Incidencia::findOrFail($id);
            $incidencia->update($request->all());

            return response()->json(['message' => 'Incidencia actualizada', 'incidencia' => $incidencia]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar la incidencia', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $incidencia = Incidencia::findOrFail($id);
            $incidencia->delete();

            return response()->json(['message' => 'Incidencia eliminada correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar la incidencia', 'message' => $e->getMessage()], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PaqueteReporte;
use Illuminate\Http\Request;

class PaqueteReporteController extends Controller
{
    // Mostrar una lista de todos los reportes
    public function index()
    {
        $reportes = PaqueteReporte::all();
        return response()->json($reportes);
    }

    // Mostrar un solo reporte
    public function show($id)
    {
        $reporte = PaqueteReporte::find($id);

        if (!$reporte) {
            return response()->json(['message' => 'Reporte no encontrado'], 404);
        }

        return response()->json($reporte);
    }

    // Crear un nuevo reporte
    public function store(Request $request)
    {
        $request->validate([
            'id_paquete' => 'required|exists:paquetes,id',
            'id_orden' => 'required|exists:ordenes,id',
            'id_cliente' => 'required|exists:clientes,id',
            'id_empleado_reporta' => 'required|exists:empleados,id',
            'descripcion_dano' => 'nullable|string',
            'estado' => 'required|in:reparado,no reparado,en reparacion,devuelto',
        ]);

        $reporte = PaqueteReporte::create($request->all());

        return response()->json($reporte, 201);
    }

    // Actualizar un reporte existente
    public function update(Request $request, $id)
    {
        $reporte = PaqueteReporte::find($id);

        if (!$reporte) {
            return response()->json(['message' => 'Reporte no encontrado'], 404);
        }

        $request->validate([
            'id_paquete' => 'sometimes|exists:paquetes,id',
            'id_orden' => 'sometimes|exists:ordenes,id',
            'id_cliente' => 'sometimes|exists:clientes,id',
            'id_empleado_reporta' => 'sometimes|exists:empleados,id',
            'descripcion_dano' => 'nullable|string',
            'estado' => 'sometimes|in:reparado,no reparado,en reparacion,devuelto',
        ]);

        $reporte->update($request->all());

        return response()->json($reporte);
    }

    // Eliminar un reporte
    public function destroy($id)
    {
        $reporte = PaqueteReporte::find($id);

        if (!$reporte) {
            return response()->json(['message' => 'Reporte no encontrado'], 404);
        }

        $reporte->delete();

        return response()->json(['message' => 'Reporte eliminado']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\HistorialPaquete;
use Illuminate\Http\Request;

class HistorialPaqueteController extends Controller
{
    public function index(Request $request)
    {
        // Definir el número de elementos por página con un valor predeterminado de 10
        $perPage = $request->input('per_page', 10);

        // Obtener el historial de paquetes paginado y ordenado por fecha_hora en orden descendente
        $historial = HistorialPaquete::orderBy('fecha_hora', 'desc')->paginate($perPage);

        return response()->json($historial);
    }

    public function show($paqueteId)
    {
        // Obtener el historial de un paquete específico
        $historial = HistorialPaquete::where('id_paquete', $paqueteId)
            ->orderBy('fecha_hora', 'desc')
            ->get();

        return response()->json($historial);
    }
}

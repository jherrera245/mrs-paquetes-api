<?php

namespace App\Http\Controllers;

use App\Models\HistorialOrdenTracking;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HistorialOrdenTrackingController extends Controller
{
    public function index()
    {
        try {
            $historial = HistorialOrdenTracking::with(['orden', 'estadoPaquete'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json($historial, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener el historial', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function buscarHistorial($identificador)
    {
        try {
            $historial = HistorialOrdenTracking::where('id_orden', $identificador)
                ->orWhere('numero_seguimiento', $identificador)
                ->with(['orden', 'estadoPaquete'])
                ->orderBy('created_at', 'desc')
                ->get();

            if ($historial->isEmpty()) {
                return response()->json(['message' => 'No se encontró historial para el identificador proporcionado'], Response::HTTP_NOT_FOUND);
            }

            return response()->json($historial, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al buscar el historial', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
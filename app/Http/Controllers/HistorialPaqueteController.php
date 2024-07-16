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
        $historial = HistorialPaquete::with(['paquete', 'usuario'])
            ->orderBy('fecha_hora', 'desc')
            ->paginate($perPage);

        // Transformar los datos para incluir uuid y nombre del usuario
        $historial->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'uuid' => $item->paquete ? $item->paquete->uuid : null,
                'nombre_usuario' => $item->usuario ? $item->usuario->name : null,
                'fecha_hora' => $item->fecha_hora,
                'accion' => $item->accion,
            ];
        });

        return response()->json($historial);
    }

    public function show($paqueteIdOrUuid)
    {
        // Obtener el historial de un paquete específico por su ID o UUID
        $historial = HistorialPaquete::with(['paquete', 'usuario'])
            ->whereHas('paquete', function ($query) use ($paqueteIdOrUuid) {
                $query->where('id', $paqueteIdOrUuid)
                    ->orWhere('uuid', $paqueteIdOrUuid);
            })
            ->orderBy('fecha_hora', 'desc')
            ->get();

        // Transformar los datos para incluir uuid y nombre del usuario
        $historial->transform(function ($item) {
            return [
                'id' => $item->id,
                'uuid' => $item->paquete ? $item->paquete->uuid : null,
                'nombre_usuario' => $item->usuario ? $item->usuario->name : null,
                'fecha_hora' => $item->fecha_hora,
                'accion' => $item->accion,
            ];
        });

        return response()->json($historial);
    }
}

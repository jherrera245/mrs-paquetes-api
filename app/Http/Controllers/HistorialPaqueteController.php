<?php

namespace App\Http\Controllers;

use App\Models\HistorialPaquete;
use Illuminate\Http\Request;

class HistorialPaqueteController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $historial = HistorialPaquete::with(['paquete', 'usuario'])
            ->orderBy('fecha_hora', 'desc')
            ->paginate($perPage);

        $historial->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'uuid' => $item->paquete ? $item->paquete->uuid : null,
                'nombre_usuario' => $item->usuario ? $item->usuario->name : null,
                'fecha_hora' => $item->fecha_hora,
                'accion' => $item->accion,
                'estado' => $item->estado,  // Nuevo campo añadido
            ];
        });

        return response()->json($historial);
    }

    public function show($paqueteIdOrUuid)
    {
        $historial = HistorialPaquete::with(['paquete', 'usuario'])
            ->whereHas('paquete', function ($query) use ($paqueteIdOrUuid) {
                $query->where('id', $paqueteIdOrUuid)
                    ->orWhere('uuid', $paqueteIdOrUuid);
            })
            ->orderBy('fecha_hora', 'desc')
            ->get();

        $historial->transform(function ($item) {
            return [
                'id' => $item->id,
                'uuid' => $item->paquete ? $item->paquete->uuid : null,
                'nombre_usuario' => $item->usuario ? $item->usuario->name : null,
                'fecha_hora' => $item->fecha_hora,
                'accion' => $item->accion,
                'estado' => $item->estado,  // Nuevo campo añadido
            ];
        });

        return response()->json($historial);
    }
}
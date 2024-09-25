<?php

namespace App\Http\Controllers;

use App\Models\Kardex;
use Illuminate\Http\Request;

class KardexController extends Controller
{
    public function index(Request $request)
    {
        // Recogemos los filtros opcionales del request
        $filters = $request->only([
            'tipo_movimiento',
            'fecha',
            'uuid', 
            'id_orden',
            'numero_ingreso'
        ]);

        $query = Kardex::with('paquete:id,uuid'); 

        if (isset($filters['tipo_movimiento'])) {
            $query->byTipoMovimiento($filters['tipo_movimiento']);
        }

        if (isset($filters['fecha'])) {
            $query->byFecha($filters['fecha']);
        }

        if (isset($filters['uuid'])) {
            $query->whereHas('paquete', function ($q) use ($filters) {
                $q->where('uuid', $filters['uuid']);
            });
        }

        if (isset($filters['id_orden'])) {
            $query->byOrden($filters['id_orden']);
        }

        if (isset($filters['numero_ingreso'])) {
            $query->byNumeroIngreso($filters['numero_ingreso']);
        }

        $kardex = $query->paginate($request->input('per_page', 10));

        $formattedKardex = $kardex->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'id_orden' => $item->id_orden,
                'cantidad' => $item->cantidad,
                'numero_ingreso' => $item->numero_ingreso,
                'tipo_movimiento' => $item->tipo_movimiento,
                'tipo_transaccion' => $item->tipo_transaccion,
                'fecha' => $item->fecha,
                'uuid' => $item->paquete ? $item->paquete->uuid : null,
            ];
        });

        return response()->json([
            'data' => $formattedKardex,
            'pagination' => [
                'total' => $kardex->total(),
                'per_page' => $kardex->perPage(),
                'current_page' => $kardex->currentPage(),
                'last_page' => $kardex->lastPage(),
                'from' => $kardex->firstItem(),
                'to' => $kardex->lastItem(),
            ]
        ]);
    }


}

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
            'id_paquete',
            'id_orden',
            'numero_ingreso'
        ]);

        // Creamos una consulta básica que luego iremos filtrando
        $query = Kardex::with('paquete:id,uuid'); // Cargamos solo los campos 'id' y 'uuid' de la relación paquete

        // Aplicamos los filtros si están presentes
        if (isset($filters['tipo_movimiento'])) {
            $query->byTipoMovimiento($filters['tipo_movimiento']);
        }

        if (isset($filters['fecha'])) {
            $query->byFecha($filters['fecha']);
        }

        if (isset($filters['id_paquete'])) {
            $query->byPaquete($filters['id_paquete']);
        }

        if (isset($filters['id_orden'])) {
            $query->byOrden($filters['id_orden']);
        }

        if (isset($filters['numero_ingreso'])) {
            $query->byNumeroIngreso($filters['numero_ingreso']);
        }

        // Ejecutamos la consulta con paginación
        $kardex = $query->paginate($request->input('per_page', 10));

        // Formateamos la salida para reemplazar el id_paquete por el uuid del paquete
        $formattedKardex = $kardex->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'id_orden' => $item->id_orden,
                'cantidad' => $item->cantidad,
                'numero_ingreso' => $item->numero_ingreso,
                'tipo_movimiento' => $item->tipo_movimiento,
                'tipo_transaccion' => $item->tipo_transaccion,
                'fecha' => $item->fecha,
                'paquete' => $item->paquete ? $item->paquete->uuid : null, // Mostramos el uuid del paquete en lugar del id_paquete
            ];
        });

        // Retornamos la respuesta con los resultados formateados y paginados
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

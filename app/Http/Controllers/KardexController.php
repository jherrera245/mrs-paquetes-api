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
        $query = Kardex::query();

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

        // Retornamos la respuesta con los resultados paginados
        return response()->json($kardex);
    }
}

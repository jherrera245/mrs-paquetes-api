<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        // Recogemos los filtros opcionales del request
        $filters = $request->only([
            'id_paquete',
            'numero_ingreso',
            'fecha_entrada',
            'fecha_salida',
            'estado',
            'cantidad'
        ]);

        // Creamos una consulta básica que luego iremos filtrando
        $query = Inventario::query();

        // Aplicamos los filtros si están presentes
        if (isset($filters['id_paquete'])) {
            $query->byPaquete($filters['id_paquete']);
        }

        if (isset($filters['numero_ingreso'])) {
            $query->byNumeroIngreso($filters['numero_ingreso']);
        }

        if (isset($filters['fecha_entrada'])) {
            $query->byFechaEntrada($filters['fecha_entrada']);
        }

        if (isset($filters['fecha_salida'])) {
            $query->byFechaSalida($filters['fecha_salida']);
        }

        if (isset($filters['estado'])) {
            $query->byEstado($filters['estado']);
        }

        if (isset($filters['cantidad'])) {
            $query->byCantidad($filters['cantidad']);
        }

        // Ejecutamos la consulta con paginación
        $inventario = $query->paginate($request->input('per_page', 10));

        // Retornamos la respuesta con los resultados paginados
        return response()->json($inventario);
    }
}

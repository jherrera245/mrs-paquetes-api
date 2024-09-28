<?php

namespace App\Http\Controllers;

use App\Models\Kardex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KardexController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only([
            'tipo_movimiento',
            'fecha_inicio',
            'fecha_fin',
            'uuid', 
            'id_orden',
            'numero_ingreso'
        ]);

        $query = DB::table('kardex as k')
            ->join('paquetes as p', 'p.id', '=', 'k.id_paquete')
            ->select(
                'k.id',
                DB::raw("IF(k.tipo_movimiento = 'ENTRADA', k.numero_ingreso, '') AS numero_entrada"),
                DB::raw("IF(k.tipo_movimiento = 'ENTRADA', k.fecha, '') AS fecha_entrada"),
                DB::raw("IF(k.tipo_movimiento = 'ENTRADA', p.uuid, '') AS paquete_entrada"),
                DB::raw("IF(k.tipo_movimiento = 'ENTRADA', k.tipo_transaccion, '') AS tipo_transaccion_entrada"),
                DB::raw("IF(k.tipo_movimiento = 'ENTRADA', k.cantidad, '') AS cantidad_entrada"),
                DB::raw("IF(k.tipo_movimiento = 'SALIDA', k.numero_ingreso, '') AS numero_salida"),
                DB::raw("IF(k.tipo_movimiento = 'SALIDA', k.fecha, '') AS fecha_salida"),
                DB::raw("IF(k.tipo_movimiento = 'SALIDA', p.uuid, '') AS paquete_salida"),
                DB::raw("IF(k.tipo_movimiento = 'SALIDA', k.tipo_transaccion, '') AS tipo_transaccion_salida"),
                DB::raw("IF(k.tipo_movimiento = 'SALIDA', k.cantidad, '') AS cantidad_salida"),
            );

        if (isset($filters['tipo_movimiento'])) {
            $query->where('k.tipo_movimiento', $filters['tipo_movimiento']);
        }

        if (isset($filters['fecha_inicio']) && isset($filters['fecha_fin'])) {
            $start = Carbon::parse($filters['fecha_inicio'])->format('Y-m-d');
            $end = Carbon::parse($filters['fecha_fin'])->format('Y-m-d');
            $query->whereBetween(DB::raw('DATE(k.fecha)'), [$start, $end]);
        }

        if (isset($filters['uuid'])) {
            $query->where('p.uuid', $filters['uuid']);
        }

        if (isset($filters['id_orden'])) {
            $query->where('k.id_orden', $filters['id_orden']);
        }

        if (isset($filters['numero_ingreso'])) {
            $query->where('k.numero_ingreso', $filters['numero_ingreso']);
        }

        $perPage = $request->input('per_page', 10);
        $kardex = $query->paginate($perPage);

        $formattedKardex = $kardex->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'numero_entrada' => $item->numero_entrada,
                'fecha_entrada' => $item->fecha_entrada,
                'paquete_entrada' => $item->paquete_entrada,
                'tipo_transaccion_entrada' => $item->tipo_transaccion_entrada,
                'cantidad_entrada' => $item->cantidad_entrada,
                'numero_salida' => $item->numero_salida,
                'fecha_salida' => $item->fecha_salida,
                'paquete_salida' => $item->paquete_salida,
                'tipo_transaccion_salida' => $item->tipo_transaccion_salida,
                'cantidad_salida' => $item->cantidad_salida,
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
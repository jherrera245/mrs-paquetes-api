<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function cardSummary()
    {
        $empleados = DB::table('empleados')->count();
        $clientes = DB::table('clientes')->count();
        $bodegas = DB::table('bodegas')->count();
        $users = DB::table('users')->count();

        return response()->json(
            [
                'totales' => [
                    'empleados' => $empleados ?? 0,
                    'clientes'  => $clientes ?? 0,
                    'bodegas'   => $bodegas ?? 0,
                    'usuarios'  => $users ?? 0
                ]
            ],  
            Response::HTTP_OK
        );
    }

    public function ordersByDay()
    {
        $hoy = Carbon::today(); //facha actual
        $inicio = $hoy->copy()->subDays(29); // 29 dias anteriores

        $start = Carbon::parse($inicio)->format('Y-m-d');
        $end = Carbon::parse($hoy)->format('Y-m-d');

        $result =  DB::select( DB::raw("
            WITH RECURSIVE fechas AS (
                SELECT '{$start}' AS fecha
                UNION ALL
                SELECT DATE_ADD(fecha, INTERVAL 1 DAY)
                FROM fechas
                WHERE fecha < '{$end}'
            )
            SELECT 
                fechas.fecha,
                COUNT(DISTINCT ordenes.id) AS ordenes,
                COUNT(detalle_orden.id) AS paquetes
            FROM 
                fechas
            LEFT JOIN 
                ordenes ON DATE(ordenes.created_at) = fechas.fecha
            LEFT JOIN 
                detalle_orden ON ordenes.id = detalle_orden.id_orden
            GROUP BY 
                fechas.fecha
            ORDER BY 
                fechas.fecha ASC;
        "));

        $result = collect($result);

        return response()->json(
            [
                'orders' => $result
            ],
            Response::HTTP_OK
        );
    }

    public function deliveredByDepartment()
    {
        $hoy = Carbon::today(); //facha actual
        $inicio = $hoy->copy()->subDays(29); // 29 dias anteriores

        $start = Carbon::parse($inicio)->format('Y-m-d');
        $end = Carbon::parse($hoy)->format('Y-m-d');

        $result = DB::table('departamento')
        ->leftJoin('direcciones', 'direcciones.id_departamento', '=', 'departamento.id')
        ->leftJoin('detalle_orden', function ($join) use ($start, $end) {
            $join->on('detalle_orden.id_direccion_entrega', '=', 'direcciones.id')
                 ->where('detalle_orden.validacion_entrega', '<>', '0')
                 ->whereBetween(DB::raw('DATE(detalle_orden.updated_at)'), [$start, $end]);
        })
        ->select(
            'departamento.nombre AS departamento',
            DB::raw('COUNT(detalle_orden.id) AS paquetes')
        )
        ->whereIn('departamento.id', [11, 12, 13, 14])
        ->groupBy('departamento.nombre')
        ->orderBy('departamento.nombre')
        ->get();

        return response()->json(
            [
                'departamentos' => $result
            ],
            Response::HTTP_OK
        );
    }

    public function packagesByStatus()
    {
        $hoy = Carbon::today(); //facha actual
        $inicio = $hoy->copy()->subDays(29); // 29 dias anteriores

        $start = Carbon::parse($inicio)->format('Y-m-d');
        $end = Carbon::parse($hoy)->format('Y-m-d');

        $result = DB::table('estado_paquetes')
        ->leftJoin('detalle_orden', function ($join) use ($start, $end) {
            $join->on('detalle_orden.id_estado_paquetes', '=', 'estado_paquetes.id')
                ->whereBetween('detalle_orden.updated_at', [$start, $end]);
        })
        ->select(
            'estado_paquetes.nombre',
            DB::raw('COUNT(detalle_orden.id) AS paquetes')
        )
        ->groupBy('estado_paquetes.nombre')
        ->orderBy('estado_paquetes.nombre')
        ->get();

        return response()->json(
            [
                'estados' => $result
            ],
            Response::HTTP_OK
        );
    }
}

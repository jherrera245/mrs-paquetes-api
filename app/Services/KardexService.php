<?php

namespace App\Services;

use App\Models\Kardex;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KardexService
{
    /**
     * Registrar un movimiento en el Kardex
     *
     * @param int $idPaquete
     * @param int $idOrden
     * @param string $tipoMovimiento ('ENTRADA', 'SALIDA')
     * @param string $tipoTransaccion ('ALMACENADO', 'ASIGNADO_RUTA', 'TRASLADO', etc.)
     * @param int $cantidad (Opcional: cantidad por defecto 1)
     * @param string $numeroIngreso (Opcional: número de seguimiento o ingreso)
     * @param Carbon $fecha (Opcional: si no se especifica se toma la fecha actual)
     * @return Kardex
     */


    public function getOrdenInfo($idPaquete)
    {
        // Consulta para obtener id_orden y numero_seguimiento a través del id_paquete
        return DB::table('detalle_orden as d')
            ->join('ordenes as o', 'd.id_orden', '=', 'o.id')
            ->where('d.id_paquete', $idPaquete)
            ->select('d.id_orden', 'o.numero_seguimiento')
            ->first();
    }

    public static function registrarMovimientoKardex($idPaquete, $idOrden, $tipoMovimiento, $tipoTransaccion, $numeroIngreso)
    {
        try {
            // Validar si los datos de entrada son correctos
            if (!in_array($tipoMovimiento, ['ENTRADA', 'SALIDA'])) {
                throw new \Exception("El tipo de movimiento debe ser 'ENTRADA' o 'SALIDA'.");
            }

            // Crear el registro en Kardex
            $kardex = new Kardex();
            $kardex->id_paquete = $idPaquete;
            $kardex->id_orden = $idOrden;
            $kardex->cantidad = 1;
            $kardex->numero_ingreso = $numeroIngreso;
            $kardex->tipo_movimiento = $tipoMovimiento;
            $kardex->tipo_transaccion = $tipoTransaccion;
            $kardex->fecha = Carbon::now();

            // Guardar el registro
            $kardex->save();

            return $kardex;

        } catch (\Exception $e) {
            \Log::error('Error al registrar en Kardex: ' . $e->getMessage());
            throw new \Exception('Error al registrar el movimiento en el Kardex.');
        }
    }
}

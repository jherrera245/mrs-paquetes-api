<?php

namespace App\Http\Controllers;

use App\Models\Transaccion;
use Illuminate\Http\Request;

class TransaccionController extends Controller
{
    // Index.
    public function index(Request $request)
    {
        $tipoMovimiento = $request->query('tipoMovimiento');
        $id_paquete = $request->query('id_paquete');
        $id_bodega = $request->query('id_bodega');
        $estado = $request->query('estado');
        $id_anaquel = $request->query('id_anaquel');
        $id_pasillo = $request->query('id_pasillo');

        $query = Transaccion::scopeSearch($tipoMovimiento, $id_paquete, $id_bodega, $estado, $id_anaquel, $id_pasillo);

        $bodegas = $query->get();

        // paginamos los resultados
        $transacciones = $query->paginate(10);

        $data = [
            'transacciones' => $transacciones,
            'status' => 200
        ];

        return response()->json($data, 200);
    }
}

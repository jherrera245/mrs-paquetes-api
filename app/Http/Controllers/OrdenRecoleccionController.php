<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdenRecoleccion;
use App\Models\Kardex;
use Illuminate\Support\Facades\DB;

class OrdenRecoleccionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = OrdenRecoleccion::with(['rutaRecoleccion', 'orden']);

        if ($request->has('id_ruta_recoleccion')) {
            $query->where('id_ruta_recoleccion', $request->input('id_ruta_recoleccion'));
        }

        // Aplicar paginación
        $perPage = $request->input('per_page', 10);
        $ordenesRecolecciones = $query->paginate($perPage);

        return response()->json($ordenesRecolecciones);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validaciones
        $validatedData = $request->validate([
            'id_ruta_recoleccion' => 'required|exists:rutas_recolecciones,id',
            'id_orden' => 'required|exists:ordenes,id',
        ]);
        // verificar si la orden ya existe 
        $ordenRecoleccionExistente = OrdenRecoleccion::where('id_ruta_recoleccion', $validatedData['id_ruta_recoleccion'])
            ->where('id_orden', $validatedData['id_orden'])
            ->first();

        if ($ordenRecoleccionExistente) {
            return response()->json(['message' => 'La orden de recolección ya existe'], 400);
        }
        // create orden recoleccion
        $ordenRecoleccion = OrdenRecoleccion::create($validatedData);

        return response()->json($ordenRecoleccion, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ordenRecoleccion = OrdenRecoleccion::with(['rutaRecoleccion', 'orden'])->findOrFail($id);

        return response()->json($ordenRecoleccion);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'id_ruta_recoleccion' => 'required|exists:rutas_recolecciones,id',
            'id_orden' => 'required|exists:ordenes,id',
            'estado' => 'required|integer',
        ]);

        $ordenRecoleccion = OrdenRecoleccion::findOrFail($id);

        $ordenRecoleccion->update($validatedData);

        return response()->json($ordenRecoleccion);
    }

    // Endpoint para cambiar el estado de las recolecciones donde el estado sea 0.
    public function updateEstadoRecolecciones()
    {
        // filtrar por donde el campo estado sea 0.
        $ordenesRecolecciones = OrdenRecoleccion::where('estado', 1)->get();

        //recorrer las recolecciones y cambiar el estado a 1.
        foreach ($ordenesRecolecciones as $ordenRecoleccion) {
            $ordenRecoleccion->recoleccion_iniciada = 1;
            $ordenRecoleccion->save();
        }

        return response()->json(['message' => 'Estado de recolecciones actualizado correctamente']);
    }

    // funcion para finalizar una orden de recoleccion
    public function finalizarOrdenRecoleccion($id_orden_recoleccion)
    {
        // obtenemos la orden de recoleccion dela bd.
        $ordenRecoleccion = OrdenRecoleccion::findOrFail($id_orden_recoleccion);

        // Usamos rollback para que si hay un error en el proceso se revierta.
        DB::beginTransaction();

        try {
            // Cambiamos el estado de la orden de recoleccion.
            $ordenRecoleccion->recoleccion_finalizada = 1;
            $ordenRecoleccion->estado = 0;
            $ordenRecoleccion->recoleccion_iniciada = 0;
            $ordenRecoleccion->save();

            // recorremos el detalle de esa orden de recoleccion.
            foreach ($ordenRecoleccion->orden->detalles as $detalle) {
                // Registrar un movimiento en el kardex SALIDA en orden y ENTRADA en transacción RECOLECTADO.
                $kardexSalida = new Kardex();
                $kardexSalida->id_paquete = $detalle->id_paquete;
                $kardexSalida->id_orden = $detalle->id_orden; 
                $kardexSalida->cantidad = 1;
                $kardexSalida->numero_ingreso = $detalle->orden->numero_seguimiento;
                $kardexSalida->tipo_movimiento = 'SALIDA';
                $kardexSalida->tipo_transaccion = 'ORDEN';
                $kardexSalida->fecha = date('Y-m-d');
                $kardexSalida->save();

                $kardexEntrada = new Kardex();
                $kardexEntrada->id_paquete = $detalle->id_paquete;
                $kardexEntrada->id_orden = $detalle->id_orden; 
                $kardexEntrada->cantidad = 1;
                $kardexEntrada->numero_ingreso = $detalle->orden->numero_seguimiento;
                $kardexEntrada->tipo_movimiento = 'ENTRADA';
                $kardexEntrada->tipo_transaccion = 'RECOLECTADO';
                $kardexEntrada->fecha = date('Y-m-d');
                $kardexEntrada->save();
            }

            // Confirmamos la transacción después de recorrer todo.
            DB::commit();

            return response()->json(['message' => 'Órden de recoleccion finalizada correctamente']);
        } catch (\Throwable $th) {
            // Si hay un error se hace rollback.
            DB::rollback();
            throw $th;
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ordenRecoleccion = OrdenRecoleccion::findOrFail($id);

        $ordenRecoleccion->delete();

        // enviar mensaje personalizado.
        return response()->json(['message' => 'Orden de recolección eliminada correctamente']);
    }
}

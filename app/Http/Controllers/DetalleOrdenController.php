<?php

namespace App\Http\Controllers;
use App\Models\DetalleOrden;

use Illuminate\Http\Request;

class DetalleOrdenController extends Controller
{
   
    public function filter(Request $request)
    {
        $idOrden = $request->input('id_orden');
        $idPaquete = $request->input('id_paquete');

        $detalleOrden = DetalleOrden::filterByOrderAndPackage($idOrden, $idPaquete)->get();

        return response()->json($detalleOrden);
    }

    public function store(Request $request)
    {
        // Validar y crear un nuevo detalle de orden
        $validatedData = $request->validate([
            'id_orden' => 'required|integer|exists:ordenes,id', // Asegurarse de que exista una orden agregada
            'id_paquete' => 'required|integer|exists:paquetes,id', // Asegurarse que el id del paquete exista
            'descripcion' => 'string',
            'total_pago' => 'required|numeric',
        ]);

        $detalleOrden = DetalleOrden::create($validatedData);
        return response()->json($detalleOrden, 201);
    }

    //Se encarga de mostrar un detalle de orden por medio del ID
    public function show($id)
    {
        // Obtener un detalle de orden específico
        $detalleOrden = DetalleOrden::findOrFail($id);
        return response()->json($detalleOrden);
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
        // Validar y actualizar un detalle de orden específico
        $detalleOrden = DetalleOrden::findOrFail($id);

        $validatedData = $request->validate([
            'id_orden' => 'sometimes|required|integer|exists:ordenes,id',
            'id_paquete' => 'sometimes|required|integer|exists:paquetes,id',
            'descripcion' => 'sometimes|string',
            'total_pago' => 'sometimes|required|numeric',
        ]);

        $detalleOrden->update($validatedData);
        return response()->json($detalleOrden);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Eliminar un detalle de orden específico
        $detalleOrden = DetalleOrden::findOrFail($id);
        $detalleOrden->delete();

        return response()->json(null, 204);
    }
}

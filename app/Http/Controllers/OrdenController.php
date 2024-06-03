<?php

namespace App\Http\Controllers;
use App\Models\Orden;

use Illuminate\Http\Request;

class OrdenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Obtener todas las órdenes
        return Orden::all();
    }

    public function store(Request $request)
    {
        // Validar y crear una nueva orden
        $validatedData = $request->validate([
            'id_cliente_entrega' => 'required|integer|exists:clientes,id',
            'telefono_entrega' => 'required|string|max:15',
            'id_cliente_recible' => 'required|integer|exists:clientes,id',
            'id_direccion' => 'required|integer|exists:direcciones,id',
            'id_tipo_entrega' => 'required|integer|exists:tipo_entregas,id',
            'id_estado_paquetes' => 'required|integer|exists:estado_paquetes,id',
            'id_paquete' => 'required|integer|exists:paquetes,id',
            'precio' => 'required|numeric',
            'id_tipo_pago' => 'required|integer|exists:tipo_pagos,id',
            'validacion_entrega' => 'required|string',
            'costo_adicional' => 'nullable|numeric',
            'instrucciones_entrega' => 'nullable|string',
            'fecha_ingreso' => 'required|date',
            'fecha_entrega' => 'required|date',
        ]);

        $orden = Orden::create($validatedData);
        return response()->json($orden, 201);
    }

    public function show($id)
    {
        // Obtener una orden específica
        $orden = Orden::findOrFail($id);
        return response()->json($orden);
    }

    public function update(Request $request, $id)
    {
        // Validar y actualizar una orden específica
        $orden = Orden::findOrFail($id);

        $validatedData = $request->validate([
            'id_cliente_entrega' => 'sometimes|required|integer|exists:clientes,id',
            'telefono_entrega' => 'sometimes|required|string|max:15',
            'id_cliente_recible' => 'sometimes|required|integer|exists:clientes,id',
            'id_direccion' => 'sometimes|required|integer|exists:direcciones,id',
            'id_tipo_entrega' => 'sometimes|required|integer|exists:tipo_entregas,id',
            'id_estado_paquetes' => 'sometimes|required|integer|exists:estado_paquetes,id',
            'id_paquete' => 'sometimes|required|integer|exists:paquetes,id',
            'precio' => 'sometimes|required|numeric',
            'id_tipo_pago' => 'sometimes|required|integer|exists:tipo_pagos,id',
            'validacion_entrega' => 'sometimes|required|string',
            'costo_adicional' => 'nullable|numeric',
            'instrucciones_entrega' => 'nullable|string',
            'fecha_ingreso' => 'sometimes|required|date',
            'fecha_entrega' => 'sometimes|required|date',
        ]);

        $orden->update($validatedData);
        return response()->json($orden);
    }

    public function destroy($id)
    {
        // Eliminar una orden específica
        $orden = Orden::findOrFail($id);
        $orden->delete();

        return response()->json(null, 204);
    }
}

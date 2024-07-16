<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrdenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'id_cliente_entrega',
            'telefono_entrega',
            'id_cliente_recible',
            'id_direccion',
            'id_tipo_entrega',
            'id_estado_paquetes',
            'id_paquete',
            'precio',
            'id_tipo_pago',
            'validacion_entrega',
            'costo_adicional',
            'instrucciones_entrega',
            'fecha_ingreso',
            'fecha_entrega',
        ]);

        $per_page = $request->input('per_page', 10);

        $ordenesQuery = Orden::search($filters);

        // Aplicar paginación después de la búsqueda
        $ordenes = $ordenesQuery->paginate($per_page);

        // Transformar los resultados antes de devolver la respuesta
        $transformedOrdenes = $ordenes->map(function ($orden) {
            return $this->transform($orden);
        });

        // Devolver una respuesta HTTP explícita
        return response()->json($transformedOrdenes, 200);
    }

    public function show($id)
    {
        $orden = Orden::findOrFail($id);

        // Transformar la orden antes de devolverla
        $transformedOrden = $this->transform($orden);

        return response()->json($transformedOrden, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        $orden = Orden::create($request->all());

        // Cargar relaciones antes de transformar y devolver la respuesta
        $orden->load(['clienteEntrega', 'clienteRecible', 'direccion', 'tipoEntrega', 'estadoPaquetes', 'paquete', 'tipoPago']);

        // Transformar la orden antes de devolverla
        $transformedOrden = $this->transform($orden);

        return response()->json($transformedOrden, 201);
    }

    public function update(Request $request, $id)
    {
        $orden = Orden::findOrFail($id);

        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        $orden->fill($request->all())->save();

        // Cargar relaciones antes de transformar y devolver la respuesta
        $orden->load(['clienteEntrega', 'clienteRecible', 'direccion', 'tipoEntrega', 'estadoPaquetes', 'paquete', 'tipoPago']);

        // Transformar la orden antes de devolverla
        $transformedOrden = $this->transform($orden);

        return response()->json($transformedOrden, 200);
    }

    public function destroy($id)
    {
        $orden = Orden::findOrFail($id);
        $orden->delete();

        return response()->json(["success" => "Orden eliminada correctamente"], 200);
    }

    private function transform(Orden $orden)
    {
        return [
            'id' => $orden->id,
            'cliente_entrega' => $orden->clienteEntrega ? $orden->clienteEntrega->nombre : null,
            'telefono_entrega' => $orden->telefono_entrega,
            'cliente_recible' => $orden->clienteRecible ? $orden->clienteRecible->nombre : null,
            'direccion' => $orden->direccion ? $orden->direccion->nombre : null,
            'tipo_entrega' => $orden->tipoEntrega ? $orden->tipoEntrega->nombre : null,
            'estado_paquetes' => $orden->estadoPaquetes ? $orden->estadoPaquetes->estado : null,
            'paquete' => $orden->paquete ? $orden->paquete->nombre : null,
            'precio' => $orden->precio,
            'tipo_pago' => $orden->tipoPago ? $orden->tipoPago->nombre : null,
            'validacion_entrega' => $orden->validacion_entrega,
            'costo_adicional' => $orden->costo_adicional,
            'instrucciones_entrega' => $orden->instrucciones_entrega,
            'fecha_ingreso' => $orden->fecha_ingreso,
            'fecha_entrega' => $orden->fecha_entrega,
            'created_at' => $orden->created_at,
            'updated_at' => $orden->updated_at,
        ];
    }
}

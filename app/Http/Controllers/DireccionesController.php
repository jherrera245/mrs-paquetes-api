<?php

namespace App\Http\Controllers;

use App\Models\Direcciones;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DireccionesController extends Controller
{
    public function index(Request $request)
    {
        // Obtener los filtros que se aplicarán a la consulta
        $filters = $request->only([
            'id_cliente', 
            'nombre_contacto', 
            'telefono', 
            'id_departamento', 
            'id_municipio', 
            'referencia'
        ]);
    
        // Aplicar los filtros usando un método de modelo que los maneje
        $query = Direcciones::filtrarDirecciones($filters);
    
        // Agregar paginación (ej. 10 registros por página)
        $direcciones = $query->paginate(10); // Cambia 10 por el número de registros que prefieras
    
        // Respuesta con los datos paginados y los detalles de la paginación
        $data = [
            'direcciones' => $direcciones->items(), // Datos de la página actual
            'status' => 200,
            'paginacion' => [
                'total' => $direcciones->total(),
                'pagina_actual' => $direcciones->currentPage(),
                'ultima_pagina' => $direcciones->lastPage(),
                'por_pagina' => $direcciones->perPage(),
            ]
        ];
    
        return response()->json($data, 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_cliente' => 'required',
            'nombre_contacto' => 'required|max:255',
            'telefono' => 'required',
            'id_departamento' => 'required',
            'id_municipio' => 'required',
            'direccion' => 'required|max:255',
            'referencia' => 'required|max:255'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $direccion = Direcciones::create([
            'id_cliente' => $request->id_cliente,
            'nombre_contacto' => $request->nombre_contacto,
            'telefono' => $request->telefono,
            'id_departamento' => $request->id_departamento,
            'id_municipio' => $request->id_municipio,
            'direccion' => $request->direccion,
            'referencia' => $request->referencia
        ]);

        if (!$direccion) {
            $data = [
                'message' => 'Error al crear bodega',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        $data = [
            'direccion' => $direccion,
            'status' => 201
        ];

        return response()->json($data, 201);

        $validator = Direcciones::validate($request->all());

        if ($validator->fails()) {
            $errors = implode('<br>', $validator->errors()->all());
            return response()->json(['error' => $errors], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Direcciones  $direcciones
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $direccion = Direcciones::find($id);

        if (!$direccion) {
            $data = [
                'message' => 'direccion no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'direccion' => $direccion,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Direcciones  $direcciones
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $direccion = Direcciones::find($id);

        if (!$direccion) {
            $data = [
                'message' => 'direccion no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'id_cliente' => 'required',
            'nombre_contacto' => 'required|max:255',
            'telefono' => 'required',
            'id_departamento' => 'required',
            'id_municipio' => 'required',
            'direccion' => 'required|max:255',
            'referencia' => 'required|max:255'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $direccion->id_cliente = $request->id_cliente;
        $direccion->nombre_contacto = $request->nombre_contacto;
        $direccion->telefono = $request->telefono;
        $direccion->id_departamento = $request->id_departamento;
        $direccion->id_municipio = $request->id_municipio;
        $direccion->direccion = $request->direccion;
        $direccion->referencia = $request->referencia;

        $direccion->save();

        $data = [
            'message' => 'direccion actualizada',
            'direccion' => $direccion,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Direcciones  $direcciones
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $direccion = Direcciones::find($id);

        if (!$direccion) {
            $data = [
                'message' => 'direccion no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $direccion->delete();

        $data = [
            'message' => 'direccion eliminada',
            'status' => 200
        ];

        return response()->json($data, 200);
    }
}

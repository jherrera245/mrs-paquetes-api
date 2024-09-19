<?php

namespace App\Http\Controllers;

use App\Models\Bodegas;
use App\Models\Transaccion;
use App\Models\Paquete;
use App\Models\Pasillo;
use App\Models\Anaquel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BodegasController extends Controller
{
    
    public function index(Request $request)
    {
        $nombre = $request->query('nombre');
        $tipo_bodega = $request->query('tipo_bodega');
        $id_departamento = $request->query('id_departamento');
        $id_municipio = $request->query('id_municipio');
        $perPage = $request->query('per_page', 10); // Número de resultados por página, por defecto 10
    
        // Consulta con filtros personalizados
        $query = Bodegas::buscarConFiltros($nombre, $id_departamento, $id_municipio);
    
        // Aplicar paginación a la consulta
        $bodegas = $query->paginate($perPage);
    
        $data = [
            'bodegas' => $bodegas->items(),  // Obtener los elementos actuales de la página
            'current_page' => $bodegas->currentPage(),
            'last_page' => $bodegas->lastPage(),
            'total' => $bodegas->total(),
            'per_page' => $bodegas->perPage(),
            'status' => 200
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
            'nombre' => 'required|max:255|unique:bodegas,nombre',
            'tipo_bodega' => 'required',
            'id_departamento' => 'required',
            'id_municipio' => 'required',
            'direccion' => 'required|max:255',
            ], 
            [
            'nombre.unique' => 'El nombre de la bodega ya ha sido creado.',
            ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $bodegas = Bodegas::create([
            'nombre' => $request->nombre,
            'tipo_bodega' => $request->tipo_bodega,
            'id_departamento' => $request->id_departamento,
            'id_municipio' => $request->id_municipio,
            'direccion' => $request->direccion
        ]);

        if (!$bodegas) {
            $data = [
                'message' => 'Error al crear bodega',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        $data = [
            'bodegas' => $bodegas,
            'status' => 201
        ];

        return response()->json($data, 201);

        $validator = Bodegas::validate($request->all());

        if ($validator->fails()) {
            $errors = implode('<br>', $validator->errors()->all());
            return response()->json(['error' => $errors], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bodegas  $bodegas
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bodega = Bodegas::find($id);

        if (!$bodega) {
            $data = [
                'message' => 'bodega no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'bodegas' => $bodega,
            'status' => 200
        ];

        return response()->json($data, 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bodegas  $bodegas
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $bodega = Bodegas::find($id);

    if (!$bodega) {
        $data = [
            'message' => 'bodega no encontrada',
            'status' => 404
        ];
        return response()->json($data, 404);
    }

    $validator = Validator::make($request->all(), [
        'nombre' => 'required|max:255',
        'tipo_bodega' => 'required',
        'id_departamento' => 'required',
        'id_municipio' => 'required',
        'direccion' => 'required|max:255'
    ]);

    if ($validator->fails()) {
        $data = [
            'message' => 'Error en la validación de los datos',
            'errors' => $validator->errors(),
            'status' => 400
        ];
        return response()->json($data, 400);
    }

    $bodega->nombre = $request->nombre;
    $bodega->tipo_bodega = $request->tipo_bodega;
    $bodega->id_departamento = $request->id_departamento;
    $bodega->id_municipio = $request->id_municipio;
    $bodega->direccion = $request->direccion;

    $bodega->save();

    $data = [
        'message' => 'bodega actualizado',
        'bodega' => $bodega,
        'status' => 200
    ];

    return response()->json($data, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bodegas  $bodegas
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $bodega = Bodegas::find($id);

        if (!$bodega) {
            $data = [
                'message' => 'bodega no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $bodega->delete();

        $data = [
            'message' => 'bodega eliminada',
            'status' => 200
        ];

        return response()->json($data, 200);
    }
}

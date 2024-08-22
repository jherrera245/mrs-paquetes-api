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
        $id_departamento = $request->query('id_departamento');
        $id_municipio = $request->query('id_municipio');

        
        $query = Bodegas::buscarConFiltros($nombre, $id_departamento, $id_municipio);

        
        $bodegas = $query->get();

        $data = [
            'bodegas' => $bodegas,
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
            'nombre' => 'required|max:255',
            'id_departamento' => 'required',
            'id_municipio' => 'required',
            'direccion' => 'required|max:255',
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

    // Función para agregar paquetes a bodega.
    public function agregarPaquete(Request $request)
    {
        try {
            // Validar que los campos no estén vacíos
            $validator = Validator::make($request->all(), [
                'uuid' => 'required|string',
                'id_bodega' => 'required',
                'id_pasillo' => 'required',
                'id_anaquel' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error en la validación de los datos',
                    'errors' => $validator->errors(),
                    'status' => 400
                ], 400);
            }

            // Buscar el paquete por UUID
            $paquete = Paquete::where('uuid', $request->uuid)->firstOrFail();

            // Buscar la bodega, pasillo, y anaquel (con manejo de excepciones)
            $bodega = Bodegas::findOrFail($request->id_bodega);
            $pasillo = Pasillo::findOrFail($request->id_pasillo);
            $anaquel = Anaquel::findOrFail($request->id_anaquel);

            

            // Verificar la capacidad del anaquel
            if ($anaquel->capacidad > $anaquel->paquetes_actuales) {
                $anaquel->paquetes_actuales++;
                $anaquel->save();

                // Crear la transacción
                Transaccion::create([
                    'id_paquete' => $paquete->id,
                    'id_bodega' => $bodega->id,
                    'id_pasillo' => $pasillo->id,
                    'id_anaquel' => $anaquel->id,
                    'tipoMovimiento' => 'ENTRADA',
                    'fecha' => now(),
                ]);

                return response()->json(['message' => 'Paquete agregado a la bodega exitosamente'], 201);
            } else {
                return response()->json(['error' => 'No hay espacio en el anaquel'], 400);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Manejo de excepción si no se encuentra un modelo (paquete, bodega, pasillo, anaquel)
            return response()->json(['error' => 'Elemento no encontrado: ' . $e->getMessage()], 404);
        } catch (\Exception $e) {
            // Manejo de cualquier otra excepción
            return response()->json(['error' => 'Ocurrió un error inesperado: ' . $e->getMessage()], 500);
        }
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

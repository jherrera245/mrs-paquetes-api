<?php

namespace App\Http\Controllers;

use App\Models\Pasillo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;

class PasilloController extends Controller
{
    /**
     * Listar todos los pasillos con información de la bodega relacionada.
     */
    public function index()
    {
        try {
            $pasillos = Pasillo::with('bodega')->get()->map(function ($pasillo) {
                return [
                    'id' => $pasillo->id,
                    'nombre' => $pasillo->nombre,
                    'capacidad' => $pasillo->capacidad,
                    'estado' => $pasillo->estado,
                    'bodega' => [
                        'id' => $pasillo->bodega->id,
                        'nombre' => $pasillo->bodega->nombre,
                    ],
                ];
            });

            return response()->json($pasillos, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los pasillos.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Mostrar un pasillo específico con información de la bodega relacionada.
     */
    public function show($id)
    {
        try {
            $pasillo = Pasillo::with('bodega')->find($id);

            if (!$pasillo) {
                return response()->json(['error' => 'Pasillo no encontrado'], Response::HTTP_NOT_FOUND);
            }

            $pasilloData = [
                'id' => $pasillo->id,
                'nombre' => $pasillo->nombre,
                'capacidad' => $pasillo->capacidad,
                'estado' => $pasillo->estado,
                'bodega' => [
                    'id' => $pasillo->bodega->id,
                    'nombre' => $pasillo->bodega->nombre,
                ],
            ];

            return response()->json($pasilloData, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener el pasillo.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Crear un nuevo pasillo.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_bodega' => 'required|exists:bodegas,id',
            'nombre' => 'required|string|max:255',
            'capacidad' => 'required|integer|min:1',
            'estado' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();
        try {
            $pasillo = Pasillo::create($request->all());
            DB::commit();
            return response()->json(['message' => 'Pasillo creado exitosamente.', 'pasillo' => $pasillo], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear el pasillo.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Actualizar un pasillo existente.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_bodega' => 'sometimes|required|exists:bodegas,id',
            'nombre' => 'sometimes|required|string|max:255',
            'capacidad' => 'sometimes|required|integer|min:1',
            'estado' => 'sometimes|required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();
        try {
            $pasillo = Pasillo::findOrFail($id);
            $pasillo->update($request->all());
            DB::commit();
            return response()->json(['message' => 'Pasillo actualizado exitosamente.', 'pasillo' => $pasillo], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Pasillo no encontrado.'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar el pasillo.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Eliminar un pasillo.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $pasillo = Pasillo::findOrFail($id);
            $pasillo->delete();
            DB::commit();
            return response()->json(['message' => 'Pasillo eliminado exitosamente.'], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Pasillo no encontrado.'], Response::HTTP_NOT_FOUND);
        } catch (QueryException $e) {
            DB::rollBack();
            // Verificar si es una violación de restricción de clave foránea
            if ($e->getCode() == '23000') {
                return response()->json([
                    'message' => 'No se puede eliminar el pasillo porque está asociado a otras entidades. Elimine o actualice las asociaciones antes de intentar eliminarlo.',
                ], Response::HTTP_CONFLICT);
            }

            // Para otros errores de consulta
            return response()->json([
                'message' => 'Error al eliminar el pasillo.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al eliminar el pasillo.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

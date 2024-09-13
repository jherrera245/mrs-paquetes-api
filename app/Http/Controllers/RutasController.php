<?php

namespace App\Http\Controllers;

use App\Models\Rutas;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Kardex;
use Illuminate\Support\Facades\DB;

class RutasController extends Controller
{
    /**
     * Muestra una lista de todas las rutas con filtros y paginación.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Obtener los filtros del request
        $filters = $request->only([
            'nombre',
            'tipo',
            'id_bodega',
            'estado',
            'fecha_programada',
        ]);

        // Establecer el número de resultados por página
        $per_page = $request->input('per_page', 10);

        // Aplicar el método de búsqueda del modelo
        $query = Rutas::search($filters);

        // Obtener los resultados paginados y transformarlos
        $rutas = $query->paginate($per_page)->through(function ($ruta) {
            return $this->transformRuta($ruta);
        });

        // Devolver una respuesta JSON
        return response()->json($rutas, 200);
    }

    /**
     * Guarda una nueva ruta en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:255',
            'id_bodega' => 'required|exists:bodegas,id',
            'fecha_programada' => 'required|date'
        ]);

        if ($validator->fails()) {
            Log::error('Error en la validación de la ruta:', $validator->errors()->toArray());
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        try {
            $ruta = Rutas::create([
                'nombre' => $request->input('nombre'),
                'id_bodega' => $request->input('id_bodega'),
                'estado' => 1,
                'fecha_programada' => $request->input('fecha_programada'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error al crear la ruta:', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error al crear la ruta', 'status' => 500], 500);
        }

        return response()->json(['ruta' => $this->transformRuta($ruta), 'status' => 201], 201);
    }

    /**
     * Muestra una ruta específica por su ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ruta = Rutas::find($id);

        if (!$ruta) {
            return response()->json(['message' => 'Ruta no encontrada', 'status' => 404], 404);
        }

        return response()->json(['ruta' => $this->transformRuta($ruta), 'status' => 200], 200);
    }

    /**
     * Actualiza una ruta existente en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $ruta = Rutas::find($id);

        if (!$ruta) {
            return response()->json(['message' => 'Ruta no encontrada', 'status' => 404], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:255',
            'id_bodega' => 'required|exists:bodegas,id',
            'fecha_programada' => 'required|date'
        ]);

        if ($validator->fails()) {
            Log::error('Error en la validación de la ruta:', $validator->errors()->toArray());
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        try {
            $ruta->update([
                'nombre' => $request->input('nombre'),
                'id_bodega' => $request->input('id_bodega'),
                'fecha_programada' => $request->input('fecha_programada'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar la ruta:', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error al actualizar la ruta', 'status' => 500], 500);
        }

        return response()->json(['message' => 'Ruta actualizada', 'ruta' => $this->transformRuta($ruta), 'status' => 200], 200);
    }

    /**
     * Elimina una ruta específica de la base de datos.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ruta = Rutas::find($id);

        if (!$ruta) {
            return response()->json(['message' => 'Ruta no encontrada', 'status' => 404], 404);
        }

        DB::beginTransaction();
        try {
            $ruta->estado = 0;
            $ruta->save();

            $asignaciones = DB::table('asignacion_rutas')->where('id_ruta', $id)->get();

            if ($asignaciones) {
                foreach($asignaciones as $asignacion)
                {
                   if ($asignacion->id_estado != 4) {
                        $detalle =  DB::table('detalle_orden')
                        ->join('ordenes', 'ordenes.id', '=', 'detalle_orden.id_orden')
                        ->select('detalle_orden.id_paquete', 'detalle_orden.id_orden', 'ordenes.numero_seguimiento')
                        ->where('detalle_orden.id_paquete', $asignacion->id_paquete)->first();

                        if ($detalle) {
                            $kardexSalida = new Kardex();
                            $kardexSalida->id_paquete = $detalle->id_paquete;
                            $kardexSalida->id_orden = $detalle->id_orden;
                            $kardexSalida->cantidad = 1;
                            $kardexSalida->numero_ingreso = $detalle->numero_seguimiento;
                            $kardexSalida->tipo_movimiento = 'SALIDA';
                            $kardexSalida->tipo_transaccion = 'DESASIGNADO_RUTA'; //Consultar
                            $kardexSalida->fecha = now();
                            $kardexSalida->save(); // Guardar el registro de SALIDA en kardex

                            $kardexSalida->id_paquete = $detalle->id_paquete;
                            $kardexSalida->id_orden = $detalle->id_orden;
                            $kardexSalida->cantidad = 1;
                            $kardexSalida->numero_ingreso = $detalle->numero_seguimiento;
                            $kardexSalida->tipo_movimiento = 'ENTRADA';
                            $kardexSalida->tipo_transaccion = 'ALMACENADO'; //Consultar
                            $kardexSalida->fecha = now();
                            $kardexSalida->save(); // Guardar el registro de SALIDA en kardex
                        }
                    }
                }
            }

            DB::commit();
            return response()->json(['message' => 'Ruta eliminada', 'status' => 200], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar la ruta:', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error al eliminar la ruta', 'status' => 500], 500);
        }
    }

    /**
     * Transforma una ruta para mostrar nombres en lugar de IDs.
     *
     * @param  \App\Models\Rutas  $ruta
     * @return array
     */
    private function transformRuta(Rutas $ruta)
    {
        return [
            'id' => $ruta->id,
            'nombre' => $ruta->nombre,
            'tipo' => $ruta->tipo,
            'bodega' => $ruta->bodega->nombre, // Obtener el nombre de la bodega
            'estado' => $ruta->estado ? 'Activo' : 'Inactivo', // Mostrar estado como texto
            'fecha_programada' => $ruta->fecha_programada,
            'created_at' => $ruta->created_at,
            'updated_at' => $ruta->updated_at,
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\DetalleOrden;
use App\Models\Kardex;
use App\Models\Orden;
use App\Models\Paquete;
use App\Models\PaqueteReporte;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException as ValidationValidationException;

class PaqueteReporteController extends Controller
{
    public function index()
    {
        $reportes = PaqueteReporte::all();
        return response()->json($reportes);
    }

    public function show($id)
    {
        $reporte = PaqueteReporte::find($id);

        if (!$reporte) {
            return response()->json(['message' => 'Reporte no encontrado'], 404);
        }

        return response()->json($reporte);
    }

    public function store(Request $request)
    {
        // Validar los datos de entrada
        $validated = $request->validate([
            'id_paquete' => 'required|integer|exists:paquetes,id',
            'id_empleado_reporta' => 'required|integer|exists:empleados,id',
            'descripcion_dano' => 'required|string|max:255',
            'costo_reparacion' => 'nullable|numeric|min:0',
            'estado' => 'required|in:reparado,no reparado',
        ]);

        try {
             // Verificar si el id_paquete ya ha sido ingresado en la tabla paquete_reporte
            $existingReport = PaqueteReporte::where('id_paquete', $validated['id_paquete'])->first();
            if ($existingReport) {
                return response()->json([
                    'success' => false,
                    'message' => 'El paquete no coincide con los datos necesarios.',
                ], 409); 
            }

            // Obtener el id_orden basado en id_paquete
            $detalleOrden = DetalleOrden::where('id_paquete', $validated['id_paquete'])->firstOrFail();
            $id_orden = $detalleOrden->id_orden;

            // Obtener el numero_seguimiento basado en id_orden
            $numero_seguimiento = Orden::where('id', $id_orden)
                                            ->value('numero_seguimiento');

            // Manejar el caso en que no se encuentre numero_seguimiento
            if ($numero_seguimiento === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Número de seguimiento no encontrado para el ID de orden especificado.',
                ], 404);
            }

            // Obtener el id_cliente basado en id_orden
            $orden = Orden::where('id', $id_orden)->firstOrFail();
            $id_cliente = $orden->id_cliente;

            // Crear un nuevo reporte
            $report = PaqueteReporte::create([
                'id_paquete' => $validated['id_paquete'],
                'id_orden' => $id_orden,
                'id_cliente' => $id_cliente,
                'id_empleado_reporta' => $validated['id_empleado_reporta'],
                'descripcion_dano' => $validated['descripcion_dano'],
                'costo_reparacion' => $validated['costo_reparacion'],
                'estado' => $validated['estado'],
            ]);

            // Actualizar el campo id_ubicacion del paquete 
            $paquete = Paquete::findOrFail($validated['id_paquete']);
            $paquete->id_ubicacion = 100; 
            $paquete->save();

            // Crear un nuevo registro en Kardex
            $kardex = new Kardex();
            $kardex->id_paquete = $validated['id_paquete'];
            $kardex->id_orden = $id_orden;
            $kardex->cantidad = 1; // Asumiendo que la cantidad siempre es 1
            $kardex->numero_ingreso = $numero_seguimiento; // Usar numero_seguimiento obtenido
            $kardex->tipo_movimiento = 'ENTRADA';
            $kardex->tipo_transaccion = 'PAQUETE_REPORTADO';
            $kardex->fecha = Carbon::now();
            $kardex->save();


            // Incluir los datos ingresados en la respuesta
            return response()->json([
                'success' => true,
                'message' => 'Reporte de daño y registro en Kardex creados exitosamente.',
                'data' => [
                    'paquete' => $report,
                    'kardex' => $kardex,
                    'input' => $validated, 
                ],
            ], 201);
        } catch (ModelNotFoundException $e) {
            // Manejo si no se encuentra el detalle de orden o la orden
            return response()->json([
                'success' => false,
                'message' => 'No se encontró el detalle de orden o la orden especificada.',
            ], 404);
        } catch (QueryException $e) {
            // Manejo de excepciones de base de datos
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el reporte de daño o el registro en Kardex.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (ValidationException $e) {
            // Manejo de excepciones de validación
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada no válidos.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Manejo de otras excepciones generales
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function update(Request $request, $id)
    {
        // Validar los datos de entrada
        $validated = $request->validate([
            'id_paquete' => 'required|integer|exists:paquetes,id',
            'id_empleado_reporta' => 'required|integer|exists:empleados,id',
            'descripcion_dano' => 'required|string|max:255',
            'costo_reparacion' => 'nullable|numeric|min:0',
            'estado' => 'required|in:reparado,no reparado',
        ]);

        try {
            // Encontrar el reporte de daño existente
            $report = Paquete::findOrFail($id);

            // Obtener el id_orden basado en id_paquete
            $detalleOrden = DetalleOrden::where('id_paquete', $validated['id_paquete'])->firstOrFail();
            $id_orden = $detalleOrden->id_orden;

            // Obtener el numero_seguimiento basado en id_orden desde la tabla Orden
            $numero_seguimiento = Orden::where('id', $id_orden)
                                    ->value('numero_seguimiento');

            // Manejar el caso en que no se encuentre numero_seguimiento
            if ($numero_seguimiento === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Número de seguimiento no encontrado para el ID de orden especificado.',
                ], 404);
            }

            // Obtener el id_cliente basado en id_orden
            $orden = Orden::where('id', $id_orden)->firstOrFail();
            $id_cliente = $orden->id_cliente;

            // Actualizar el reporte de daño
            $report->update([
                'id_paquete' => $validated['id_paquete'],
                'id_orden' => $id_orden,
                'id_cliente' => $id_cliente,
                'id_empleado_reporta' => $validated['id_empleado_reporta'],
                'descripcion_dano' => $validated['descripcion_dano'],
                'costo_reparacion' => $validated['costo_reparacion'],
                'estado' => $validated['estado'],
            ]);

            // Actualizar el campo id_ubicacion del paquete
            $paquete = Paquete::findOrFail($validated['id_paquete']);
            $paquete->id_ubicacion = 100;
            $paquete->save();

            // Actualizar o crear un registro en Kardex si es necesario
            $kardex = Kardex::updateOrCreate(
                ['id_paquete' => $validated['id_paquete'], 'id_orden' => $id_orden],
                [
                    'cantidad' => 1, 
                    'numero_ingreso' => $numero_seguimiento,
                    'tipo_movimiento' => 'ENTRADA',
                    'tipo_transaccion' => 'PAQUETE_REPORTADO',
                    'fecha' => Carbon::now()
                ]
            );

            // Incluir los datos actualizados en la respuesta
            return response()->json([
                'success' => true,
                'message' => 'Reporte de daño y actualización de ubicación actualizados exitosamente.',
                'data' => [
                    'paquete' => $report,
                    'kardex' => $kardex,
                    'input' => $validated, // Datos validados y procesados
                ],
            ], 200);
        } catch (ModelNotFoundException $e) {
            // Manejo si no se encuentra el reporte de daño, el detalle de orden, la orden o el paquete
            return response()->json([
                'success' => false,
                'message' => 'No se encontró el reporte de daño, el detalle de orden, la orden o el paquete especificado.',
            ], 404);
        } catch (QueryException $e) {
            // Manejo de excepciones de base de datos
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el reporte de daño, el paquete o el registro en Kardex.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (ValidationException $e) {
            // Manejo de excepciones de validación
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada no válidos.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Manejo de otras excepciones generales
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        $reporte = PaqueteReporte::find($id);

        if (!$reporte) {
            return response()->json(['message' => 'Reporte no encontrado'], 404);
        }

        $reporte->delete();

        return response()->json(['message' => 'Reporte eliminado']);
    }
}

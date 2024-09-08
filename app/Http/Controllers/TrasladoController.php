<?php

namespace App\Http\Controllers;

use App\Models\Bodegas;
use App\Models\Destinos;
use App\Models\Rutas;
use App\Models\DetalleOrden;
use App\Models\Paquete;
use App\Models\Departamento;
use App\Models\Traslado;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class TrasladoController extends Controller
{
    /**
     * Listar traslados con filtros y paginación.
     */
    public function index(Request $request)
    {
        try {
            // Iniciar la consulta con las relaciones necesarias
            $query = Traslado::with(['bodega', 'ubicacionPaquete', 'asignacionRuta', 'orden'])
                ->where('estado', 'Activo');

            // Filtros opcionales
            if ($request->has('id_bodega')) {
                $query->where('id_bodega', $request->id_bodega);
            }

            if ($request->has('fecha_traslado')) {
                $query->whereDate('fecha_traslado', $request->fecha_traslado);
            }

            if ($request->has('codigo_qr')) {
                $query->where('codigo_qr', 'like', '%' . $request->codigo_qr . '%');
            }

            if ($request->has('id_ubicacion_paquete')) {
                $query->where('id_ubicacion_paquete', $request->id_ubicacion_paquete);
            }

            if ($request->has('id_asignacion_ruta')) {
                $query->where('id_asignacion_ruta', $request->id_asignacion_ruta);
            }

            if ($request->has('id_orden')) {
                $query->where('id_orden', $request->id_orden);
            }

            // Filtro por rango de fechas de traslado
            if ($request->has('fecha_traslado_from') && $request->has('fecha_traslado_to')) {
                $query->whereBetween('fecha_traslado', [$request->fecha_traslado_from, $request->fecha_traslado_to]);
            }

            // Paginación
            $traslados = $query->paginate(10);

            // Formatear la respuesta utilizando el método `getFormattedData()` del modelo
            $formattedData = $traslados->map(function ($traslado) {
                return $traslado->getFormattedData();
            });

            return response()->json($formattedData, Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error al listar traslados: ' . $e->getMessage());
            return response()->json(['error' => 'Error al listar traslados'], 500);
        }
    }

    public function show($id)
    {
        try {
            $traslado = Traslado::with(['bodega', 'ubicacionPaquete', 'asignacionRuta', 'orden'])->find($id);

            if (!$traslado) {
                return response()->json(['message' => 'Traslado no encontrado'], Response::HTTP_NOT_FOUND);
            }

            // Formatear la respuesta utilizando el método `getFormattedData()` del modelo
            return response()->json($traslado->getFormattedData(), Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error al mostrar traslado: ' . $e->getMessage());
            return response()->json(['error' => 'Error al mostrar traslado'], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            // Validar los datos de entrada con reglas personalizadas para relaciones
            $validatedData = $request->validate([
                'id_bodega' => 'required|exists:bodegas,id',
                'codigo_qr' => 'required|string|max:255|unique:traslados,codigo_qr', // Validación de unicidad del QR
                'id_ubicacion_paquete' => 'required|exists:ubicaciones_paquetes,id',
                'id_asignacion_ruta' => 'required|exists:asignacion_rutas,id',
                'id_orden' => 'required|exists:ordenes,id',
                'numero_ingreso' => 'required|string|max:100|unique:traslados,numero_ingreso', // Validación de unicidad del número de ingreso
                'fecha_traslado' => 'required|date',
                'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
            ], [
                'id_bodega.exists' => 'La bodega especificada no existe.',
                'id_ubicacion_paquete.exists' => 'La ubicación del paquete especificada no existe.',
                'id_asignacion_ruta.exists' => 'La asignación de ruta especificada no existe.',
                'id_orden.exists' => 'La orden especificada no existe.',
                'estado.in' => 'El estado debe ser Activo o Inactivo.',
            ]);

            // Crear el traslado
            $traslado = Traslado::create($validatedData);

            return response()->json([
                'message' => 'Traslado creado con éxito',
                'data' => $traslado->getFormattedData()
            ], Response::HTTP_CREATED);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Capturar y devolver errores de validación con detalles específicos
            return response()->json(['error' => 'Error al crear traslado.', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Capturar cualquier otra excepción y registrar el error
            Log::error('Error al crear traslado: ' . $e->getMessage());
            return response()->json(['error' => 'Error al crear traslado. ' . $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar un traslado existente.
     */
    public function update(Request $request, $id)
    {
        try {
            // Buscar el traslado por ID
            $traslado = Traslado::find($id);

            if (!$traslado) {
                return response()->json(['error' => 'Traslado no encontrado'], Response::HTTP_NOT_FOUND);
            }

            // Validar los datos de entrada con reglas personalizadas
            $validatedData = $request->validate([
                'id_bodega' => 'nullable|exists:bodegas,id',
                'codigo_qr' => [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::unique('traslados')->ignore($traslado->id),
                ],
                'id_ubicacion_paquete' => 'nullable|exists:ubicaciones_paquetes,id',
                'id_asignacion_ruta' => 'nullable|exists:asignacion_rutas,id',
                'id_orden' => 'nullable|exists:ordenes,id',
                'numero_ingreso' => [
                    'nullable',
                    'string',
                    'max:100',
                    Rule::unique('traslados')->ignore($traslado->id),
                ],
                'fecha_traslado' => 'nullable|date',
                'estado' => ['nullable', Rule::in(['Activo', 'Inactivo'])],
            ], [
                'id_bodega.exists' => 'La bodega seleccionada no es válida.',
                'codigo_qr.unique' => 'El código QR ya está en uso.',
                'id_ubicacion_paquete.exists' => 'La ubicación del paquete seleccionada no es válida.',
                'id_asignacion_ruta.exists' => 'La asignación de ruta seleccionada no es válida.',
                'id_orden.exists' => 'La orden seleccionada no es válida.',
                'numero_ingreso.unique' => 'El número de ingreso ya está en uso.',
                'estado.in' => 'El estado debe ser Activo o Inactivo.',
            ]);

            // Validación de unicidad adicional para `codigo_qr` y `numero_ingreso`
            if ($request->has('codigo_qr') && $request->codigo_qr != $traslado->codigo_qr) {
                $codigoQRExists = Traslado::where('codigo_qr', $request->codigo_qr)->exists();
                if ($codigoQRExists) {
                    return response()->json(['error' => 'El código QR ya está en uso.'], 400);
                }
            }

            if ($request->has('numero_ingreso') && $request->numero_ingreso != $traslado->numero_ingreso) {
                $numeroIngresoExists = Traslado::where('numero_ingreso', $request->numero_ingreso)->exists();
                if ($numeroIngresoExists) {
                    return response()->json(['error' => 'El número de ingreso ya está en uso.'], 400);
                }
            }

            // Actualizar solo los valores proporcionados en la solicitud
            $traslado->fill($validatedData);

            // Guardar los cambios
            $traslado->save();

            return response()->json([
                'message' => 'Traslado actualizado con éxito',
                'data' => $traslado->getFormattedData()
            ], Response::HTTP_OK);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Captura de errores de validación
            return response()->json(['error' => 'Datos inválidos', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Captura de otros errores no esperados
            Log::error('Error al actualizar traslado: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar traslado'], 500);
        }
    }

    /**
     * Eliminar (marcar como inactivo) un traslado.
     */
    public function destroy($id)
    {
        try {
            $traslado = Traslado::find($id);

            if (!$traslado) {
                return response()->json(['message' => 'Traslado no encontrado'], Response::HTTP_NOT_FOUND);
            }

            $traslado->estado = 'Inactivo';
            $traslado->save();

            return response()->json(['message' => 'Traslado eliminado (marcado como inactivo) con éxito'], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error al eliminar traslado: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar traslado'], 500);
        }
    }

    public function trasladoPdf($id = null)
    {
        if ($id) {
            // Generar PDF para un solo traslado
            $traslado = Traslado::findOrFail($id);

            // Obtener el destino y departamento
            $destino = Destinos::find($traslado->id_asignacion_ruta);
            $departamento = $destino ? Departamento::find($destino->id_departamento) : null;

            // Obtener el nombre de la bodega
            $bodega = Bodegas::find($traslado->id_bodega);

            // Obtener todos los paquetes asociados a la orden
            $detalleOrdenes = DetalleOrden::where('id_orden', $traslado->id_orden)->get();
            $paquetes = $detalleOrdenes->map(function($detalle) {
                return Paquete::find($detalle->id_paquete);
            })->filter(); // Filter to remove null values

            // Preparar los datos para el PDF
            $data = [
                'fecha' => now()->format('d/m/Y'),
                'destino' => $destino ? $destino->nombre . ' ' . ($departamento ? $departamento->nombre : 'Departamento no disponible') : 'Destino no disponible',
                'paquetes' => $paquetes,
                'traslado' => $traslado,
                'bodega' => $bodega ? $bodega->nombre : 'Bodega no disponible', // Agregar el nombre de la bodega
                'single' => true
            ];
        } else {
            // Generar PDF para múltiples traslados
            $traslados = Traslado::all();

            // Reemplazar codigo_qr con uuid para cada traslado
            $trasladosConPaquetes = $traslados->map(function($traslado) {
                $detalleOrdenes = DetalleOrden::where('id_orden', $traslado->id_orden)->get();
                $paquetes = $detalleOrdenes->map(function($detalle) {
                    return Paquete::find($detalle->id_paquete);
                })->filter(); // Filter to remove null values
                
                $traslado->paquetes = $paquetes;
                return $traslado;
            });

            // Obtener los nombres de las bodegas para cada traslado
            $trasladosConBodegas = $trasladosConPaquetes->map(function($traslado) {
                $traslado->bodega_nombre = Bodegas::find($traslado->id_bodega)->nombre ?? 'Bodega no disponible';
                return $traslado;
            });

            $data = [
                'fecha' => now()->format('d/m/Y'),
                'traslados' => $trasladosConBodegas,
                'single' => false
            ];
        }

        // Generar el PDF
        $pdf = Pdf::loadView('pdf.traslado', $data);
        $pdfContent = $pdf->output();
        $base64Pdf = base64_encode($pdfContent);

        return response()->json(['pdf' => $base64Pdf]);
    }
    
}

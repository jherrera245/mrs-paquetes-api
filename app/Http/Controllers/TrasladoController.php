<?php

namespace App\Http\Controllers;

use App\Models\Traslado;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class TrasladoController extends Controller
{
    public function index()
    {
        $traslados = Traslado::with(['bodega', 'ubicacionPaquete', 'asignacionRuta', 'orden'])
            ->where('estado', 'Activo')
            ->get();

        $resultados = $traslados->map(function ($traslado) {
            return $traslado->getFormattedData();
        });

        return response()->json($resultados, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_bodega' => 'required|exists:bodegas,id',
            'codigo_qr' => 'required|string|max:255|unique:traslados,codigo_qr', // Validar unicidad del QR
            'id_ubicacion_paquete' => 'nullable|exists:ubicaciones_paquetes,id',
            'id_asignacion_ruta' => 'nullable|exists:asignacion_rutas,id',
            'id_orden' => 'nullable|exists:ordenes,id',
            'numero_ingreso' => 'nullable|string|max:100|unique:traslados,numero_ingreso', // Validar unicidad del número de ingreso
            'fecha_traslado' => 'required|date',
            'estado' => ['required', Rule::in(['Activo', 'Inactivo'])],
        ]);

        $traslado = Traslado::create($validatedData);

        return response()->json([
            'message' => 'Traslado creado con éxito',
            'data' => $traslado->getFormattedData()
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $traslado = Traslado::with(['bodega', 'ubicacionPaquete', 'asignacionRuta', 'orden'])->find($id);

        if (!$traslado) {
            return response()->json(['message' => 'Traslado no encontrado'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($traslado->getFormattedData(), Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        // Buscar el traslado
        $traslado = Traslado::find($id);

        if (!$traslado) {
            return response()->json(['message' => 'Traslado no encontrado'], Response::HTTP_NOT_FOUND);
        }

        // Validar los datos de entrada
        $validatedData = $request->validate([
            'id_bodega' => 'nullable|exists:bodegas,id',
            'codigo_qr' => ['nullable', 'string', 'max:255', Rule::unique('traslados')->ignore($traslado->id)],
            'id_ubicacion_paquete' => 'nullable|exists:ubicaciones_paquetes,id',
            'id_asignacion_ruta' => 'nullable|exists:asignacion_rutas,id',
            'id_orden' => 'nullable|exists:ordenes,id',
            'numero_ingreso' => ['nullable', 'string', 'max:100', Rule::unique('traslados')->ignore($traslado->id)],
            'fecha_traslado' => 'nullable|date',
            'estado' => ['nullable', Rule::in(['Activo', 'Inactivo'])],
        ]);

        // Actualizar los valores solo si están presentes en la solicitud
        $traslado->id_bodega = $request->input('id_bodega', $traslado->id_bodega);
        $traslado->codigo_qr = $request->input('codigo_qr', $traslado->codigo_qr);
        $traslado->id_ubicacion_paquete = $request->input('id_ubicacion_paquete', $traslado->id_ubicacion_paquete);
        $traslado->id_asignacion_ruta = $request->input('id_asignacion_ruta', $traslado->id_asignacion_ruta);
        $traslado->id_orden = $request->input('id_orden', $traslado->id_orden);
        $traslado->numero_ingreso = $request->input('numero_ingreso', $traslado->numero_ingreso);
        $traslado->fecha_traslado = $request->input('fecha_traslado', $traslado->fecha_traslado);
        $traslado->estado = $request->input('estado', $traslado->estado);

        // Guardar los cambios
        $traslado->save();

        return response()->json([
            'message' => 'Traslado actualizado con éxito',
            'data' => $traslado->getFormattedData()
        ], Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $traslado = Traslado::find($id);

        if (!$traslado) {
            return response()->json(['message' => 'Traslado no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $traslado->estado = 'Inactivo';
        $traslado->save();

        return response()->json(['message' => 'Traslado eliminado (marcado como inactivo) con éxito'], Response::HTTP_OK);
    }
}

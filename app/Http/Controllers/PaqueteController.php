<?php

namespace App\Http\Controllers;

use App\Models\Paquete;
use App\Models\HistorialPaquete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

class PaqueteController extends Controller
{
    public function index(Request $request)
    {
        // Definir el número de elementos por página con un valor predeterminado de 10
        $perPage = $request->input('per_page', 10);

        // Obtener los paquetes no eliminados paginados
        $paquetes = Paquete::whereNull('eliminado_at')->paginate($perPage);

        return response()->json($paquetes);
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'id_tipo_paquete',
            'id_empaque',
            'peso',
            'id_estado_paquete',
            'fecha_envio',
            'fecha_entrega_estimada',
            'descripcion_contenido',
        ]);

        // Genera un UUID para el paquete
        $uuid = Str::uuid();
        $data['uuid'] = $uuid;

        // Generar el código QR
        try {
            $result = Builder::create()
                ->writer(new PngWriter())
                ->data($uuid)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(new ErrorCorrectionLevelLow())
                ->size(200)
                ->margin(10)
                ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
                ->build();

            // Guardar la imagen del código QR en el directorio de almacenamiento
            $filename = $uuid . '.png';
            $path = 'qr_codes/' . $filename;
            Storage::disk('public')->put($path, $result->getString());

            // La función asset() genera la URL completa del archivo
            $qrCodeUrl = asset('storage/' . $path);
            $data['tag'] = $qrCodeUrl;
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al generar el código QR: ' . $e->getMessage()], 500);
        }

        $validator = Validator::make($data, [
            'id_tipo_paquete' => 'required|exists:tipo_paquete,id',
            'id_empaque' => 'required|exists:empaquetado,id',
            'peso' => 'required|numeric|min:0',
            'uuid' => 'required|unique:paquetes',
            'tag' => 'required',
            'id_estado_paquete' => 'required|exists:estado_paquetes,id',
            'fecha_envio' => 'required|date',
            'fecha_entrega_estimada' => 'required|date',
            'descripcion_contenido' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        try {
            // Crear el paquete
            $paquete = Paquete::create($data);

            // Obtener el ID de usuario actualmente autenticado
            $userId = auth()->id();

            // Registrar la acción en el historial
            HistorialPaquete::create([
                'id_paquete' => $paquete->id,
                'fecha_hora' => now(),
                'id_usuario' => $userId, // Asignar el ID de usuario actual
                'accion' => 'Paquete creado',
            ]);

            return response()->json([
                'paquete' => $paquete,
                'qr_code_url' => $qrCodeUrl,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear el paquete: ' . $e->getMessage()], 500);
        }
    }

    public function show($idOrQrCode)
    {
        try {
            if (is_numeric($idOrQrCode)) {
                $paquete = Paquete::whereNull('eliminado_at')->findOrFail($idOrQrCode);
            } else {
                $paquete = Paquete::whereNull('eliminado_at')->where(function ($query) use ($idOrQrCode) {
                    $query->where('tag', $idOrQrCode)->orWhere('uuid', $idOrQrCode);
                })->firstOrFail();
            }

            return response()->json($paquete);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Paquete no encontrado: ' . $e->getMessage()], 404);
        }
    }

    public function update(Request $request, $param)
    {
        try {
            $paquete = is_numeric($param) ? Paquete::whereNull('eliminado_at')->findOrFail($param) : Paquete::whereNull('eliminado_at')->where('uuid', $param)->firstOrFail();

            $validator = Validator::make($request->all(), [
                'id_tipo_paquete' => 'sometimes|required|exists:tipo_paquete,id',
                'id_empaque' => 'sometimes|required|exists:empaquetado,id',
                'peso' => 'sometimes|required|numeric|min:0',
                'id_estado_paquete' => 'sometimes|required|exists:estado_paquetes,id',
                'fecha_envio' => 'sometimes|required|date',
                'fecha_entrega_estimada' => 'sometimes|required|date',
                'descripcion_contenido' => 'sometimes|required|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->all()], 400);
            }

            $originalData = $paquete->getOriginal();

            $paquete->update($request->all());

            if (!empty(array_diff_assoc($request->all(), $originalData))) {
                HistorialPaquete::create([
                    'id_paquete' => $paquete->id,
                    'fecha_hora' => now(),
                    'id_usuario' => auth()->id(),
                    'accion' => 'Paquete actualizado',
                ]);
            }

            return response()->json(['message' => 'Paquete actualizado correctamente', 'paquete' => $paquete]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar el paquete: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $paquete = Paquete::withEliminados()->findOrFail($id);

            // Actualizar el campo 'eliminado_at' en lugar de eliminar físicamente el paquete
            $paquete->update(['eliminado_at' => now()]);

            // Guardar la acción en el historial
            $this->registerHistory($paquete->id, 'Paquete eliminado');

            return response()->json(['message' => 'Paquete marcado como eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al marcar el paquete como eliminado: ' . $e->getMessage()], 500);
        }
    }

    public function restore($id)
    {
        try {
            // Encontrar el paquete, incluyendo los eliminados (sin filtro de `eliminado_at`)
            $paquete = Paquete::withEliminados()->findOrFail($id);

            // Verificar si el paquete está marcado como eliminado
            if ($paquete->eliminado_at) {
                // Actualizar el campo 'eliminado_at' a NULL para restaurar el paquete
                $paquete->update(['eliminado_at' => null]);

                // Guardar la acción en el historial
                $this->registerHistory($paquete->id, 'Paquete restaurado');

                return response()->json(['message' => 'Paquete restaurado correctamente']);
            } else {
                return response()->json(['message' => 'El paquete no está marcado como eliminado'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al restaurar el paquete: ' . $e->getMessage()], 500);
        }
    }

    private function registerHistory($paqueteId, $accion)
    {
        HistorialPaquete::create([
            'id_paquete' => $paqueteId,
            'fecha_hora' => now(),
            'id_usuario' => auth()->id(),
            'accion' => $accion,
        ]);
    }
}

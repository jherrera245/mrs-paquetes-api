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
        $filters = $request->only([
            'tipo_paquete', 'empaque', 'peso', 'estado_paquete', 'fecha_envio', 'fecha_entrega_estimada', 'descripcion_contenido', 'palabra_clave'
        ]);

        $perPage = $request->input('per_page', 10);

        // Filtrar los paquetes utilizando el método search del modelo Paquete
        $paquetesQuery = Paquete::search($filters);

        // Aplicar paginación después de la búsqueda
        $paquetes = $paquetesQuery->paginate($perPage);

        // Verificar si hay resultados
        if ($paquetes->isEmpty()) {
            return response()->json(['message' => 'No se encontraron coincidencias.'], 404);
        }

        // Transformar la colección de paquetes antes de devolverla como JSON
        $paquetes->getCollection()->transform(function ($paquete) {
            return $this->transformPaquete($paquete); // Asegúrate de que $this esté vinculado correctamente aquí
        });

        return response()->json($paquetes, 200);
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'id_tipo_paquete', 'id_empaque', 'peso', 'id_estado_paquete', 'fecha_envio', 'fecha_entrega_estimada', 'descripcion_contenido'
        ]);

        $uuid = Str::uuid();
        $data['uuid'] = $uuid;

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

            $filename = $uuid . '.png';
            $path = 'qr_codes/' . $filename;
            Storage::disk('s3')->put($path, $result->getString());

            $bucketName = env('AWS_BUCKET');
            $region = env('AWS_DEFAULT_REGION');
            $qrCodeUrl = "https://{$bucketName}.s3.{$region}.amazonaws.com/{$path}";
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
            $paquete = Paquete::create($data);
            $userId = auth()->id();

            HistorialPaquete::create([
                'id_paquete' => $paquete->id,
                'fecha_hora' => now(),
                'id_usuario' => $userId,
                'accion' => 'Paquete creado',
            ]);

            return response()->json([
                'paquete' => $this->transformPaquete($paquete),
                'qr_code_url' => $qrCodeUrl,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear el paquete: ' . $e->getMessage()], 500);
        }
    }

    public function show($idOrQrCode)
    {
        try {
            $paquete = is_numeric($idOrQrCode)
                ? Paquete::with('tipoPaquete', 'empaquetado', 'estado')->whereNull('eliminado_at')->findOrFail($idOrQrCode)
                : Paquete::with('tipoPaquete', 'empaquetado', 'estado')->whereNull('eliminado_at')->where(function ($query) use ($idOrQrCode) {
                    $query->where('tag', $idOrQrCode)->orWhere('uuid', $idOrQrCode);
                })->firstOrFail();

            return response()->json($this->transformPaquete($paquete));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Paquete no encontrado: ' . $e->getMessage()], 404);
        }
    }

    public function update(Request $request, $param)
    {
        try {
            $paquete = is_numeric($param)
                ? Paquete::whereNull('eliminado_at')->findOrFail($param)
                : Paquete::whereNull('eliminado_at')->where('uuid', $param)->firstOrFail();

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

            $estadoActual = $paquete->estado ? $paquete->estado->nombre : null;

            if ($request->has('id_estado_paquete') && $paquete->id_estado_paquete != $originalData['id_estado_paquete']) {
                HistorialPaquete::create([
                    'id_paquete' => $paquete->id,
                    'fecha_hora' => now(),
                    'id_usuario' => auth()->id(),
                    'accion' => 'Estado del paquete actualizado a ' . $estadoActual,
                ]);
            }

            return response()->json([
                'message' => 'Paquete actualizado correctamente',
                'paquete' => $this->transformPaquete($paquete),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar el paquete: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $paquete = Paquete::withEliminados()->findOrFail($id);

            $paquete->update(['eliminado_at' => now()]);

            $this->registerHistory($paquete->id, 'Paquete eliminado');

            return response()->json(['message' => 'Paquete marcado como eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al marcar el paquete como eliminado: ' . $e->getMessage()], 500);
        }
    }

    public function restore($id)
    {
        try {
            $paquete = Paquete::withEliminados()->findOrFail($id);

            if ($paquete->eliminado_at) {
                $paquete->update(['eliminado_at' => null]);

                $this->registerHistory($paquete->id, 'Paquete restaurado');

                return response()->json(['message' => 'Paquete restaurado correctamente']);
            } else {
                return response()->json(['error' => 'Paquete no está eliminado'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al restaurar el paquete: ' . $e->getMessage()], 500);
        }
    }

    private function transformPaquete($paquete)
    {
        return [
            'id' => $paquete->id,
            'tipo_paquete' => $paquete->tipoPaquete ? $paquete->tipoPaquete->nombre : null,
            'empaque' => $paquete->empaquetado ? $paquete->empaquetado->empaquetado : null,
            'peso' => $paquete->peso,
            'uuid' => $paquete->uuid,
            'tag' => $paquete->tag,
            'estado_paquete' => $paquete->estado ? $paquete->estado->nombre : null,
            'fecha_envio' => $paquete->fecha_envio,
            'fecha_entrega_estimada' => $paquete->fecha_entrega_estimada,
            'descripcion_contenido' => $paquete->descripcion_contenido,
            'created_at' => $paquete->created_at,
            'updated_at' => $paquete->updated_at,
        ];
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
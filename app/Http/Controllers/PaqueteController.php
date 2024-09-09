<?php

namespace App\Http\Controllers;

use App\Models\Paquete;
use App\Models\Clientes;
use App\Models\HistorialPaquete;
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Exception;
use DB;

class PaqueteController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only([
            'tipo_paquete',
            'empaque',
            'peso',
            'estado_paquete',
            'fecha_envio_desde',
            'fecha_envio_hasta',
            'fecha_entrega_estimada_desde',
            'fecha_entrega_estimada_hasta',
            'descripcion_contenido',
            'palabra_clave'
        ]);

        $perPage = $request->input('per_page', 10);

        $paquetesQuery = Paquete::search($filters);
        $paquetes = $paquetesQuery->paginate($perPage);

        if ($paquetes->isEmpty()) {
            return response()->json(['message' => 'No se encontraron coincidencias.'], 404);
        }

        $paquetes->getCollection()->transform(function ($paquete) {
            return $this->transformPaquete($paquete);
        });

        return response()->json($paquetes, 200);
    }

    public function getPaquetesByUser(Request $request)
    {
        $user = Auth::user();

        $filters = $request->only([
            'tipo_paquete',
            'empaque',
            'peso',
            'estado_paquete',
            'fecha_envio_desde',
            'fecha_envio_hasta',
            'fecha_entrega_estimada_desde',
            'fecha_entrega_estimada_hasta',
            'descripcion_contenido',
            'palabra_clave'
        ]);

        $perPage = $request->input('per_page', 10);

        $query = DB::table('paquetes')
            ->select([
                'paquetes.id',
                'tipo_paquete.nombre as empaque',
                'paquetes.peso',
                'paquetes.uuid',
                'paquetes.tag',
                'estado_paquetes.nombre as estado_paquete',
                'paquetes.fecha_envio',
                'paquetes.fecha_entrega_estimada',
                'paquetes.descripcion_contenido',
                'ubicaciones.nomenclatura as ubicacion',
                'paquetes.created_at',
                'paquetes.updated_at',
            ])
            ->join('tipo_paquete', 'paquetes.id_tipo_paquete', '=', 'tipo_paquete.id')
            ->join('empaquetado', 'paquetes.id_empaque', '=', 'tipo_paquete.id')
            ->join('estado_paquetes', 'paquetes.id_estado_paquete', '=', 'estado_paquetes.id')
            ->join('ubicaciones', 'paquetes.id_ubicacion', '=', 'ubicaciones.id')
            ->join('detalle_orden', 'detalle_orden.id_paquete', '=', 'paquetes.id')
            ->join('ordenes', 'ordenes.id', '=', 'detalle_orden.id_orden');

        if ($user->hasRole('cliente')) {
            $cliente = Cliente::find($user->id);
            $query->where('ordenes.id_cliente', $cliente->id);
        }

        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                switch ($key) {
                    case 'tipo_paquete':
                        $query->where(function ($q) use ($value) {
                            $q->where('tipo_paquete.nombre', 'like', '%' . $value . '%')
                                ->orWhere('paquetes.id_tipo_paquete', $value);
                        });
                        break;

                    case 'empaque':
                        $query->where(function ($q) use ($value) {
                            $q->where('empaquetado.nombre', 'like', '%' . $value . '%')
                                ->orWhere('paquetes.id_empaque', $value);
                        });
                        break;

                    case 'estado_paquete':
                        $query->where(function ($q) use ($value) {
                            $q->where('estado_paquetes.nombre', 'like', '%' . $value . '%')
                                ->orWhere('paquetes.id_estado_paquete', $value);
                        });
                        break;

                    case 'descripcion_contenido':
                        $query->where('paquetes.descripcion_contenido', 'like', '%' . $value . '%');
                        break;

                    case 'peso':
                        $query->where('paquetes.peso', $value);
                        break;

                    case 'fecha_envio_desde':
                        $query->whereDate('paquetes.fecha_envio', '>=', $value);
                        break;

                    case 'fecha_envio_hasta':
                        $query->whereDate('paquetes.fecha_envio', '<=', $value);
                        break;

                    case 'fecha_entrega_estimada_desde':
                        $query->whereDate('paquetes.fecha_entrega_estimada', '>=', $value);
                        break;

                    case 'fecha_entrega_estimada_hasta':
                        $query->whereDate('paquetes.fecha_entrega_estimada', '<=', $value);
                        break;

                    case 'palabra_clave':
                        $query->where(function ($q) use ($value) {
                            $q->where('paquetes.descripcion_contenido', 'like', '%' . $value . '%')
                                ->orWhere('paquetes.uuid', 'like', '%' . $value . '%')
                                ->orWhere('paquetes.tag', 'like', '%' . $value . '%')
                                ->orWhere('tipo_paquete.nombre', 'like', '%' . $value . '%')
                                ->orWhere('empaquetado.nombre', 'like', '%' . $value . '%')
                                ->orWhere('estado_paquetes.nombre', 'like', '%' . $value . '%');
                        });
                        break;

                    default:
                        break;
                }
            }
        }

        $paquetes = $query->paginate($perPage);

        return response()->json($paquetes, 200);
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'id_tipo_paquete',
            'id_tamano_paquete',
            'id_empaque',
            'peso',
            'id_estado_paquete',
            'fecha_envio',
            'fecha_entrega_estimada',
            'descripcion_contenido',
            'id_ubicacion'
        ]);

        $uuid = Str::uuid();
        $data['uuid'] = $uuid->toString(); // Asegúrate de convertir el UUID a string

        try {
            // Genera el código QR
            $result = Builder::create()
                ->writer(new PngWriter())
                ->data($uuid->toString())
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(new ErrorCorrectionLevelLow())
                ->size(200)
                ->margin(10)
                ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
                ->build();

            $filename = $uuid . '.png';
            $path = 'qr_codes/' . $filename;

            // Guarda el código QR en S3
            Storage::disk('s3')->put($path, $result->getString());

            $bucketName = env('AWS_BUCKET');
            $region = env('AWS_DEFAULT_REGION');
            $qrCodeUrl = "https://{$bucketName}.s3.{$region}.amazonaws.com/{$path}";
            $data['tag'] = $qrCodeUrl;
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al generar el código QR: ' . $e->getMessage()], 500);
        }

        // Valida los datos del paquete
        $validator = Validator::make($data, [
            'id_tipo_paquete' => 'required|exists:tipo_paquete,id',
            'id_tamano_paquete' => 'required|exists:tamano_paquete,id',
            'id_empaque' => 'required|exists:empaquetado,id',
            'peso' => 'required|numeric|min:0',
            'uuid' => 'required|unique:paquetes,uuid',
            'tag' => 'required',
            'id_estado_paquete' => 'required|exists:estado_paquetes,id',
            'fecha_envio' => 'required|date',
            'fecha_entrega_estimada' => 'required|date|after_or_equal:fecha_envio',
            'descripcion_contenido' => 'required|string|max:1000',
            'id_ubicacion' => 'required|exists:ubicaciones,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        try {
            // Crea el paquete
            $paquete = Paquete::create($data);
            $userId = auth()->id();

            // Registra el historial del paquete
            HistorialPaquete::create([
                'id_paquete' => $paquete->id,
                'fecha_hora' => now(),
                'id_usuario' => $userId,
                'accion' => 'Paquete creado',
                'estado' => 'Creado' 
            ]);

            return response()->json([
                'paquete' => $this->transformPaquete($paquete),
                'qr_code_url' => $qrCodeUrl,
            ], 201);
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
                'id_tamano_paquete' => 'required|exists:tamano_paquete,id',
                'id_empaque' => 'sometimes|required|exists:empaquetado,id',
                'peso' => 'sometimes|required|numeric|min:0',
                'id_estado_paquete' => 'sometimes|required|exists:estado_paquetes,id',
                'fecha_envio' => 'sometimes|required|date',
                'fecha_entrega_estimada' => 'sometimes|required|date|after_or_equal:fecha_envio',
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
                    'estado' => $estadoActual
                ]);
            }

            return response()->json([
                'message' => 'Paquete actualizado correctamente',
                'paquete' => $this->transformPaquete($paquete),
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al actualizar el paquete: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $paquete = Paquete::withEliminados()->findOrFail($id);

            // Marcar el paquete como eliminado
            $paquete->update(['eliminado_at' => now()]);

            // Definir el estado actual para el registro de historial
            $estadoActual = 'Eliminado';

            // Registrar el historial del paquete con el estado actual
            $this->registerHistory($paquete->id, 'Paquete eliminado', $estadoActual);

            return response()->json(['message' => 'Paquete marcado como eliminado correctamente']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al marcar el paquete como eliminado: ' . $e->getMessage()], 500);
        }
    }

    public function restore($id)
    {
        try {
            $paquete = Paquete::withEliminados()->findOrFail($id);

            if ($paquete->eliminado_at) {
                $paquete->update(['eliminado_at' => null]);

                // Definir el estado actual para el registro de historial
                $estadoActual = 'Restaurado';

                // Registrar el historial del paquete con el estado actual
                $this->registerHistory($paquete->id, 'Paquete restaurado', $estadoActual);

                return response()->json(['message' => 'Paquete restaurado correctamente']);
            } else {
                return response()->json(['error' => 'Paquete no está eliminado'], 400);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al restaurar el paquete: ' . $e->getMessage()], 500);
        }
    }


    private function transformPaquete($paquete)
{
    return [
        'id' => $paquete->id,
        'tipo_paquete' => $paquete->tipoPaquete ? $paquete->tipoPaquete->nombre : null,
        'tamano_paquete' => $paquete->tamanoPaquete ? $paquete->tamanoPaquete->nombre : null,
        'empaque' => $paquete->empaquetado ? $paquete->empaquetado->empaquetado : null,
        'peso' => $paquete->peso,
        'uuid' => $paquete->uuid,
        'tag' => $paquete->tag,
        'estado_paquete' => $paquete->estado ? $paquete->estado->nombre : null,
        'fecha_envio' => $paquete->fecha_envio,
        'fecha_entrega_estimada' => $paquete->fecha_entrega_estimada,
        'descripcion_contenido' => $paquete->descripcion_contenido,
        'ubicacion' => $paquete->ubicacion ? $paquete->ubicacion->nomenclatura : null,
        'created_at' => $paquete->created_at,
        'updated_at' => $paquete->updated_at,
    ];
    }


    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class, 'id_ubicacion');
    }


    private function registerHistory($paqueteId, $accion, $estado = null)
    {
        HistorialPaquete::create([
            'id_paquete' => $paqueteId,
            'fecha_hora' => now(),
            'id_usuario' => auth()->id(),
            'accion' => $accion,
            'estado' => $estado,
        ]);
    }

    public function filterByLocation(Request $request)
    {
        // Obtiene los parámetros de la consulta
        $idDepartamento = $request->query('id_departamento');
        $idMunicipio = $request->query('id_municipio');

        // Validar que los parámetros sean numéricos si están presentes
        if (($idDepartamento && !is_numeric($idDepartamento)) || ($idMunicipio && !is_numeric($idMunicipio))) {
            return response()->json(['error' => 'Parámetros inválidos.'], 400);
        }

        // Construye la consulta con joins
        $paquete = Paquete::select('paquetes.*')
            ->join('detalle_orden', 'paquetes.id', '=', 'detalle_orden.id_paquete')
            ->join('ordenes', 'detalle_orden.id_orden', '=', 'ordenes.id')
            ->join('direcciones', 'ordenes.id_direccion', '=', 'direcciones.id')
            ->join('departamento', 'direcciones.id_departamento', '=', 'departamento.id')
            ->join('municipios', 'direcciones.id_municipio', '=', 'municipios.id');

        // Aplicar filtro de departamento si existe
        if ($idDepartamento) {
            $paquete->where('departamentos.id', $idDepartamento);
        }

        // Aplicar filtro de municipio si existe
        if ($idMunicipio) {
            $paquete->where('municipios.id', $idMunicipio);
        }

        // Obtener los resultados
        $paquete = $paquete->get();

        // Comprobar si hay resultados
        if ($paquete->isEmpty()) {
            return response()->json(['message' => 'No se encontraron paquetes para los filtros especificados.'], 404);
        }

        return response()->json($paquete, 200);
    }
}

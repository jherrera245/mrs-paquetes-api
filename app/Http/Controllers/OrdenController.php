<?php

namespace App\Http\Controllers;

use App\Models\Direcciones;
use App\Models\DetalleOrden;
use App\Models\Orden;
use App\Models\Paquete;
use App\Models\HistorialPaquete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\HttpFoundation\Response;

class OrdenController extends Controller
{
    public function index(Request $request)
    {
        // Implementación del método index
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_cliente' => 'required|integer|exists:clientes,id',
            'nombre_contacto' => 'required|string',
            'telefono' => 'required|string',
            'id_departamento' => 'required|integer|exists:departamento,id',
            'id_municipio' => 'required|integer|exists:municipios,id',
            'direccion' => 'required|string',
            'referencia' => 'nullable|string',
            'id_tipo_pago' => 'required|integer|exists:tipo_pago,id',
            'total_pagar' => 'required|numeric',
            'costo_adicional' => 'nullable|numeric',
            'concepto' => 'required|string',
            'detalles' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();
        try {
            $orden = $this->createOrder($request);

            if ($orden) {
                foreach ($request->input('detalles') as $detalle) {
                    $this->createOrderDetail($orden, $detalle);
                }

                DB::commit();
                return response()->json(['message' => 'Orden creada con éxito'], Response::HTTP_CREATED);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'message' => 'Error',
                    'error' => $e->getMessage(),
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    private function createOrder($request)
    {
        $direccion_emisor = new Direcciones();
        $direccion_emisor->id_cliente = $request->input('id_cliente');
        $direccion_emisor->nombre_contacto = $request->input('nombre_contacto');
        $direccion_emisor->telefono = $request->input('telefono');
        $direccion_emisor->id_departamento = $request->input('id_departamento');
        $direccion_emisor->id_municipio = $request->input('id_municipio');
        $direccion_emisor->direccion = $request->input('direccion');
        $direccion_emisor->referencia = $request->input('referencia');
        $direccion_emisor->save();

        if (!$direccion_emisor) {
            throw new \Exception('Error al guardar la dirección del cliente emisor');
        }

        $orden = new Orden();
        $orden->id_cliente = $request->input('id_cliente');
        $orden->id_tipo_pago = $request->input('id_tipo_pago');
        $orden->id_direccion = $direccion_emisor->id;
        $orden->total_pagar = $request->input('total_pagar');
        $orden->costo_adicional = $request->input('costo_adicional');
        $orden->concepto = $request->input('concepto');
        $orden->save();

        return $orden;
    }

    private function createOrderDetail($orden, $detalle)
    {
        $uuid = Str::uuid();
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
        $tag = $qrCodeUrl;

        $paquete = new Paquete();
        $paquete->id_tipo_paquete = $detalle["id_tipo_paquete"];
        $paquete->id_empaque = $detalle["id_empaque"];
        $paquete->peso = $detalle["peso"];
        $paquete->uuid = $uuid;
        $paquete->tag = $tag;
        $paquete->id_estado_paquete = $detalle["id_estado_paquete"];
        $paquete->fecha_envio = $detalle["fecha_envio"];
        $paquete->fecha_entrega_estimada = $detalle["fecha_entrega_estimada"];
        $paquete->descripcion_contenido = $detalle["descripcion_contenido"];
        $paquete->save();

        $userId = auth()->id();

        HistorialPaquete::create([
            'id_paquete' => $paquete->id,
            'fecha_hora' => now(),
            'id_usuario' => $userId,
            'accion' => 'Paquete creado',
        ]);

        if ($paquete) {
            $detalleOrden = new DetalleOrden();
            $detalleOrden->id_orden = $orden->id;
            $detalleOrden->id_tipo_entrega = $detalle["id_tipo_entrega"];
            $detalleOrden->id_estado_paquetes = $detalle["id_estado_paquete"];
            $detalleOrden->id_cliente_entrega = $detalle["id_cliente_entrega"];
            $detalleOrden->id_paquete = $paquete->id;
            $detalleOrden->validacion_entrega = 0;
            $detalleOrden->instrucciones_entrega = $detalle['instrucciones_entrega'];
            $detalleOrden->descripcion = $detalle['descripcion'];
            $detalleOrden->precio = $detalle['precio'];
            $detalleOrden->fecha_ingreso = now();
            $detalleOrden->fecha_entrega = $detalle['fecha_entrega'];;

            $direccion_receptor = new Direcciones();
            $direccion_receptor->id_cliente = $detalle['id_cliente_entrega'];
            $direccion_receptor->nombre_contacto = $detalle['nombre_contacto'];
            $direccion_receptor->telefono = $detalle['telefono'];
            $direccion_receptor->id_departamento = $detalle['id_departamento'];
            $direccion_receptor->id_municipio = $detalle['id_municipio'];
            $direccion_receptor->direccion = $detalle['direccion'];
            $direccion_receptor->referencia = $detalle['referencia'];
            $direccion_receptor->save();

            if ($direccion_receptor) {
                $detalleOrden->id_direccion_entrega = $direccion_receptor->id;
                $detalleOrden->save();
            } else {
                throw new \Exception('Error al guardar la dirección del receptor');
            }
        } else {
            throw new \Exception('Error al generar el paquete');
        }
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_cliente' => 'required|integer|exists:clientes,id',
            'direccion_emisor' => 'required',
            'id_tipo_pago' => 'required|integer|exists:tipo_pago,id',
            'total_pagar' => 'required|numeric',
            'costo_adicional' => 'nullable|numeric',
            'concepto' => 'required|string',
            'detalles' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();
        try {
            $orden = Orden::find($id);
            if (!$orden) {
                return response()->json(['message' => 'Orden no encontrada'], Response::HTTP_NOT_FOUND);
            }

            $orden->id_cliente = $request->input('id_cliente');
            $orden->id_tipo_pago = $request->input('id_tipo_pago');
            $orden->total_pagar = $request->input('total_pagar');
            $orden->costo_adicional = $request->input('costo_adicional');
            $orden->concepto = $request->input('concepto');
            $orden->save();

            $direccion_emisior = Direcciones::find($orden->id_direccion);
            if (!$direccion_emisior) {
                return response()->json(['message' => 'Dirección no encontrada'], Response::HTTP_NOT_FOUND);
            }

            $direccion_emisior->id_cliente = $request->input('id_cliente');
            $direccion_emisior->nombre_contacto = $request->input('nombre_contacto');
            $direccion_emisior->telefono = $request->input('telefono');
            $direccion_emisior->id_departamento = $request->input('id_departamento');
            $direccion_emisior->id_municipio = $request->input('id_municipio');
            $direccion_emisior->direccion = $request->input('direccion');
            $direccion_emisior->referencia = $request->input('referencia');
            $direccion_emisior->save();

            // Eliminar los detalles existentes
            DetalleOrden::where('id_orden', $orden->id)->delete();

            // Crear los nuevos detalles
            foreach ($request->input('detalles') as $detalle) {
                $this->createOrderDetail($orden, $detalle);
            }

            DB::commit();
            return response()->json(['message' => 'Orden actualizada con éxito'], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'message' => 'Error',
                    'error' => $e->getMessage(),
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $orden = Orden::find($id);
            if (!$orden) {
                return response()->json(['message' => 'No se encontró la orden.'], Response::HTTP_NOT_FOUND);
            }

            // Eliminar los detalles de la orden
            DetalleOrden::where('id_orden', $orden->id)->delete();

            // Eliminar la orden
            $orden->delete();

            DB::commit();
            return response()->json(['message' => 'Orden eliminada.'], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'message' => 'Error',
                    'error' => $e->getMessage(),
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    public function show($id)
    {
        $orden = Orden::find($id);

        if (!$orden) {
            return response()->json(['message' => 'Orden no encontrada'], Response::HTTP_NOT_FOUND);
        }

        $direccion = Direcciones::find($orden->id_direccion);

        // Estructurar la respuesta
        $response = [
            'id' => $orden->id,
            'id_cliente' => $orden->id_cliente,
            'tipo_pago' => $orden->tipo_pago->pago ?? 'NA',
            'total_pagar' => $orden->total_pagar,
            'costo_adicional' => $orden->costo_adicional,
            'id_direccion' => $orden->id_direccion,
            'concepto' => $orden->concepto,
            'direccion_emisor' => [
                'id_direccion' => $direccion->id,
                'direccion' => $direccion->direccion,
                'nombre_cliente' => $direccion->cliente->nombre,
                'apellido_cliente' => $direccion->cliente->apellido,
                'nombre_contacto' => $direccion->nombre_contacto,
                'telefono' => $direccion->telefono,
                'id_departamento' => $direccion->departamento->nombre,
                'id_municipio' => $direccion->municipio->nombre,
                'referencia' => $direccion->referencia,
            ],
            'detalles' => $orden->detalles
        ];

        return response()->json($response, Response::HTTP_OK);
    }

}

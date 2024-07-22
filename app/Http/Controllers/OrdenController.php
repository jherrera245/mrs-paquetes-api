<?php

namespace App\Http\Controllers;

use App\Models\Direcciones;
use App\Models\DetalleOrden;
use App\Models\Orden;
use App\Models\Paquete;
use App\Models\HistorialPaquete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

class OrdenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

    }

    public function show($id)
    {

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_cliente' => 'required|integer|exists:clientes,id',
            'direccion_emisor' => 'required',
            'id_tipo_pago' => 'required|integer|exists:tipo_pagos,id',
            'total_pagar' => 'required|numeric',
            'costo_adicional' => 'nullable|numeric',
            'concepto' => 'required|string',
            'detalles' => 'required|array'
        ]);


        DB::beginTransaction();
        try {
            // creacion de la orden
            $orden = new Orden();
            $orden->id_cliente = $request->input('id_cliente');
            $orden->id_tipo_pago = $request->input('id_tipo_pago');
            $orden->total_pagar = $request->input('total_pagar');
            $orden->costo_adicional = $request->input('costo_adicional');
            $orden->concepto = $request->input('concepto');

            //definicion de detalles del emisor
            $direccion_emisior = new Direcciones();
            $direccion_emisior->id_cliente = $request->input('id_cliente');
            $direccion_emisior->nombre_contacto = $request->input('nombre_contacto');
            $direccion_emisior->telefono = $request->input('telefono');
            $direccion_emisior->id_departamento = $request->input('id_departamento');
            $direccion_emisior->id_municipio = $request->input('id_municipio');
            $direccion_emisior->direccion = $request->input('direccion');
            $direccion_emisior->referencia = $request->input('referencia');
            $direccion_emisior->save();

            if ($direccion_emisior) {
                $orden->id_direccion = $direccion_emisior->id_direccion;
                $orden->save();

                if ($orden) {
                    // creacion de detalles de la orden
                    $numero_detalle = 0;
                    foreach ($request->input('detalles') as $detalle) {
                        $numero_detalle++;

                        //logica para la generacion de un paquete
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
                            //detalle de orden
                            $detalleOrden = new DetalleOrden();
                            $detalleOrden->id_orden = $orden->id_orden;
                            $detalleOrden->id_tipo_entrega = $detalle["id_tipo_entrega"];
                            $detalleOrden->id_estado_paquetes = $detalle["id_estado_paquete"];
                            $detalleOrden->id_cliente_entrega = $detalle["id_cliente_entrega"];
                            $detalleOrden->id_paquete = $paquete->id;
                            $detalleOrden->validacion_entrega = 0;
                            $detalleOrden->instrucciones_entrega = $detalle['instrucciones_entrega'];
                            $detalleOrden->descripcion = $detalle['descripcion'];
                            $detalleOrden->precio = $detalle['precio'];
                            $detalleOrden->fecha_ingreso = now();
                            $detalleOrden->fecha_entrega = null;

                            //definicion de detalles del emisor
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
                                $detalleOrden->id_direccion_receptor = $direccion_receptor->id;

                                $detalleOrden->save();
                            } else {
                                return response()->json(['message' => 'Error al guardar la direccion de receptor n#'.$numero_detalle],  Response::HTTP_UNPROCESSABLE_ENTITY);
                            }

                        } else {
                            return response()->json(['message' => 'Error al guardar al generar paquete n#'.$numero_detalle],  Response::HTTP_UNPROCESSABLE_ENTITY);
                        }

                    }

                    DB::commit();
                    return response()->json(['message' => 'Orden creada con exito'], Response::HTTP_CREATED);
                }

            } else {
                return response()->json(['message' => 'Error al guardar la direccion del cliente emisor'],  Response::HTTP_UNPROCESSABLE_ENTITY);
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

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {

    }
}

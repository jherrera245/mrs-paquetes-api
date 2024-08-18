<?php

namespace App\Http\Controllers;

use App\Models\Direcciones;
use App\Models\DetalleOrden;
use App\Models\Orden;
use App\Models\Paquete;
use App\Models\HistorialPaquete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;// Importar el facade de DomPDF
use Barryvdh\DomPDF\Facade\Pdf;
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
        $filters = $request->only([
            'id_cliente',
            'estado_pago',
            'fecha_inicio',
            'fecha_fin'
        ]);

        $perPage = $request->input('per_page', 10);

        $ordenesQuery = Orden::with(['cliente', 'tipoPago', 'direccion', 'detalles'])
        ->search($filters);

        $ordenes = $ordenesQuery->paginate($perPage);

        if ($ordenes->isEmpty()) {
            return response()->json(['message' => 'No se encontraron órdenes.'], Response::HTTP_NOT_FOUND);
        }

        $ordenes->getCollection()->transform(function ($orden) {
            return $this->transformOrden($orden);
        });

        return response()->json($ordenes, Response::HTTP_OK);
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
            $detalleOrden->fecha_entrega = $detalle['fecha_entrega'];

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

    // funcion para actualizar estado de entrega.
    public function updateEstadoEntrega(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'validacion_entrega' => 'required|integer',
            'id_estado_paquetes' => 'required|integer',
            'fecha_entrega' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();
        try {
            $detalleOrden = DetalleOrden::find($id);
            if (!$detalleOrden) {
                return response()->json(['message' => 'Detalle de orden no encontrado'], Response::HTTP_NOT_FOUND);
            }

            $detalleOrden->validacion_entrega = $request->input('validacion_entrega');
            $detalleOrden->id_estado_paquetes = $request->input('id_estado_paquetes');
            $detalleOrden->fecha_entrega = $request->input('fecha_entrega');
            $detalleOrden->save();

            DB::commit();
            return response()->json(['message' => 'Estado de entrega actualizado con éxito'], Response::HTTP_OK);

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

    // funcion para listar los paquetes y sus estados de entrega segun la ruta a la que fueron asignados.
    public function listarPaquetesRuta($id)
    {
        $detalleOrden = DetalleOrden::where('id_ruta', $id)->get();

        if ($detalleOrden->isEmpty()) {
            return response()->json(['message' => 'No se encontraron paquetes en la ruta'], Response::HTTP_NOT_FOUND);
        }

        $detalleOrden->transform(function ($detalle) {
            return [
                'id' => $detalle->id,
                'id_paquete' => $detalle->id_paquete,
                'id_tipo_entrega' => $detalle->id_tipo_entrega,
                'id_estado_paquetes' => $detalle->id_estado_paquetes,
                'id_cliente_entrega' => $detalle->id_cliente_entrega,
                'validacion_entrega' => $detalle->validacion_entrega,
                'instrucciones_entrega' => $detalle->instrucciones_entrega,
                'descripcion' => $detalle->descripcion,
                'precio' => $detalle->precio,
                'fecha_ingreso' => $detalle->fecha_ingreso,
                'fecha_entrega' => $detalle->fecha_entrega,
                'direccion_entrega' => $detalle->direccion_entrega,
                'paquete' => $detalle->paquete,
            ];
        });

        return response()->json($detalleOrden, Response::HTTP_OK);
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

    /**
     * Requerimiento 2: Genera un PDF con los detalles de la orden especificada.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    

    public function generatePDF($id)
    {
        $orden = Orden::with([
            'cliente:id,nombre,apellido', // Asegura que cargas el nombre del cliente
            'direccion:id,direccion,nombre_contacto,telefono,referencia', // Carga la dirección con sus detalles
            'detalles:id,id_orden,id_paquete,id_tipo_entrega,descripcion,precio',
            'detalles.paquete:id,descripcion_contenido,peso',
            'tipoPago:id,pago' // Carga el nombre del tipo de pago
        ])->find($id);

        // Manejar el caso donde la orden no se encuentra
        if (!$orden) {
            return response()->json(['message' => 'Orden no encontrada'], Response::HTTP_NOT_FOUND);
        }

        // Obtener la dirección del emisor
        $direccion_emisor = $orden->direccion;

        // Cargar la vista y generar el PDF
        $pdf = PDF::loadView('pdf.orden', compact('orden', 'direccion_emisor'));

        // Devolver el PDF como string base64 para que el frontend pueda manejarlo
        $pdfContent = $pdf->output();
        
        // Devolver la respuesta con el PDF codificado en base64
        return response()->json(['pdf' => base64_encode($pdfContent)], 200);

        // Devolver el PDF sin codificación, directamente como un archivo PDF
    // return $pdf->download('orden.pdf'); // Puedes cambiar 'orden.pdf' por el nombre que desees para el archivo
    }

    /**
     * Requerimiento 8: Mostrar órdenes del cliente autenticado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function misOrdenes(Request $request)
    {
        // Obtener el cliente autenticado
        $cliente_id = Auth::id();

        // Buscar las órdenes del cliente autenticado y cargar las relaciones necesarias
        $ordenes = Orden::where('id_cliente', $cliente_id)
                        ->with(['detalles', 'direccion', 'tipoPago', 'cliente'])
                        ->get();

        // Verificar si el cliente tiene órdenes
        if ($ordenes->isEmpty()) {
            return response()->json(['message' => 'No se encontraron órdenes'], Response::HTTP_NOT_FOUND);
        }

        // Formatear la respuesta para incluir los nombres en lugar de los IDs
        $ordenes = $ordenes->map(function($orden) {
            return [
                'id' => $orden->id,
                'cliente' => optional($orden->cliente)->nombre, // Usar optional para manejar nulos
                'direccion' => [
                    'nombre_contacto' => optional($orden->direccion)->nombre_contacto,
                    'telefono' => optional($orden->direccion)->telefono,
                    'direccion' => optional($orden->direccion)->direccion,
                    'referencia' => optional($orden->direccion)->referencia,
                ],
                'tipo_pago' => optional($orden->tipoPago)->pago, // Usar optional para manejar nulos
                'total_pagar' => $orden->total_pagar,
                'costo_adicional' => $orden->costo_adicional,
                'concepto' => $orden->concepto,
                'finished' => $orden->finished,
                'created_at' => $orden->created_at,
                'updated_at' => $orden->updated_at,
                'detalles' => $orden->detalles->map(function($detalle) {
                    return [
                        'id_paquete' => $detalle->id_paquete,
                        'descripcion' => $detalle->descripcion,
                        'precio' => $detalle->precio,
                    ];
                }),
            ];
        });

        // Devolver las órdenes del cliente
        return response()->json($ordenes, Response::HTTP_OK);
    }

    private function transformOrden($orden)
    {
        $direccion = $orden->direccion;

        return [
            'id' => $orden->id,
            'id_cliente' => $orden->id_cliente,
            'cliente' => [
                'nombre' => $orden->cliente->nombre,
                'apellido' => $orden->cliente->apellido,
            ],
            'tipo_pago' => $orden->tipoPago->pago ?? 'NA',
            'total_pagar' => $orden->total_pagar,
            'costo_adicional' => $orden->costo_adicional,
            'concepto' => $orden->concepto,
            'estado_pago' => $orden->estado_pago,
            'direccion_emisor' => [
                'id_direccion' => $direccion->id,
                'direccion' => $direccion->direccion,
                'nombre_contacto' => $direccion->nombre_contacto,
                'telefono' => $direccion->telefono,
                'departamento' => $direccion->departamento->nombre,
                'municipio' => $direccion->municipio->nombre,
                'referencia' => $direccion->referencia,
            ],
            'detalles' => $orden->detalles->map(function ($detalle) {
                return [
                    'id_paquete' => $detalle->id_paquete,
                    'descripcion' => $detalle->descripcion,
                    'precio' => $detalle->precio,
                ];
            }),
            'created_at' => $orden->created_at,
            'updated_at' => $orden->updated_at,
        ];
    }

    public function procesarPago(Request $request, $id)
    {
        $orden = Orden::findOrFail($id);

        if ($orden->estado_pago === 'pagado') {
            return response()->json(['message' => 'Esta orden ya ha sido pagada'], 400);
        }

        $tipoPago = $orden->tipoPago->pago;

        if ($tipoPago === 'Tarjeta') {
            $validator = Validator::make($request->all(), [
                'nombre_titular' => 'required|string|max:255',
                'numero_tarjeta' => 'required|string|size:16',
                'fecha_vencimiento' => 'required|date_format:m/Y|after:today',
                'cvv' => 'required|string|size:3',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Aquí iría la lógica de procesamiento del pago con tarjeta
            // Por ahora, simularemos un pago exitoso
        }

        // Procesar el pago (simulado)
        $orden->estado_pago = 'pagado';
        $orden->save();

        // Generar comprobante
        $comprobante = $this->generarComprobante($orden->id);

        return response()->json([
            'message' => 'Pago procesado con éxito',
            'comprobante' => $comprobante
        ],
            200
        );
    }

    public function generarComprobante($id)
    {
        $orden = Orden::with(['cliente', 'detalles', 'tipoPago'])->findOrFail($id);

        if ($orden->estado_pago !== 'pagado') {
            return response()->json(['error' => 'La orden aún no ha sido pagada'], 400);
        }

        $subtotal = $orden->total_pagar / 1.13; // Asumiendo que el total incluye IVA
        $iva = $orden->total_pagar - $subtotal;

        $data = [
            'numeroFactura' => 'F-' . str_pad($orden->id, 6, '0', STR_PAD_LEFT),
            'fecha' => date('d/m/Y'),
            'cliente' => [
                'nombre' => $orden->cliente->nombre . ' ' . $orden->cliente->apellido,
                'nit' => $orden->cliente->nit,
                'direccion' => $orden->cliente->direccion,
            ],
            'detalles' => $orden->detalles->map(function ($detalle) {
                return [
                    'descripcion' => $detalle->descripcion,
                    'precio' => $detalle->precio,
                ];
            }),
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => $orden->total_pagar,
            'metodoPago' => $orden->tipoPago->pago,
        ];

        $pdf = PDF::loadView('pdf.comprobante_pago', $data);

        $output = $pdf->output();

        return base64_encode($output);
    }

    public function visualizarComprobante($id)
    {
        $orden = Orden::with(['cliente', 'detalles', 'tipoPago'])->findOrFail($id);

        if ($orden->estado_pago !== 'pagado') {
            return response()->json(['error' => 'La orden aún no ha sido pagada'], 400);
        }

        $subtotal = $orden->total_pagar / 1.13; // Asumiendo que el total incluye IVA
        $iva = $orden->total_pagar - $subtotal;

        $data = [
            'numeroFactura' => 'F-' . str_pad($orden->id, 6, '0', STR_PAD_LEFT),
            'fecha' => date('d/m/Y'),
            'cliente' => [
                'nombre' => $orden->cliente->nombre . ' ' . $orden->cliente->apellido,
                'nit' => $orden->cliente->nit,
                'direccion' => $orden->cliente->direccion,
            ],
            'detalles' => $orden->detalles->map(function ($detalle) {
                return [
                    'descripcion' => $detalle->descripcion,
                    'precio' => $detalle->precio,
                ];
            }),
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => $orden->total_pagar,
            'metodoPago' => $orden->tipoPago->pago,
        ];

        $pdf = PDF::loadView('pdf.comprobante_pago', $data);

        return $pdf->stream('comprobante.pdf');
    }

    public function generarHojaDeTrabajo($idRuta)
    {
        // Obtén los paquetes asignados a la ruta
        $asignaciones = AsignacionRutas::with(['paquete', 'paquete.tipoPaquete', 'paquete.empaquetado', 'paquete.estado', 'ruta', 'vehiculo'])
            ->where('id_ruta', $idRuta)
            ->get();

        if ($asignaciones->isEmpty()) {
            return response()->json(['message' => 'No se encontraron paquetes para esta ruta'], 404);
        }

        // Genera el PDF
        $pdf = Pdf::loadView('pdf.hoja_de_trabajo', ['asignaciones' => $asignaciones]);

        // Convierte el PDF a base64
        $pdfContent = $pdf->output();
        $pdfBase64 = base64_encode($pdfContent);

        // Retorna el PDF en formato base64
        $data = [
            'pdf_base64' => $pdfBase64,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function misOrdenesAsignadas(Request $request)
    {
        // Validar los parámetros de la solicitud
        $request->validate([
            'id_ruta' => 'required|exists:rutas,id',
        ]);

        // Obtener los filtros de la solicitud
        $filters = $request->only([
            'uuid', 
            'fecha', 
            'nombre_cliente_entrega', 
            'nombre_cliente_recibe', 
            'id_ruta'
        ]);

        // Obtener las órdenes asignadas con los filtros aplicados
        $ordenes = Orden::whereHas('detalles', function ($query) use ($filters) {
                $query->whereHas('asignacionRuta', function ($subq) use ($filters) {
                    $subq->where('id_ruta', $filters['id_ruta'])
                        ->when(isset($filters['uuid']), function ($q) use ($filters) {
                            $q->whereHas('paquete', function ($q) use ($filters) {
                                $q->where('uuid', 'like', '%' . $filters['uuid'] . '%');
                            });
                        })
                        ->when(isset($filters['fecha']), function ($q) use ($filters) {
                            $q->whereDate('fecha', $filters['fecha']);
                        });
                });
            })
            ->when(isset($filters['nombre_cliente_entrega']) || isset($filters['nombre_cliente_recibe']), function ($query) use ($filters) {
                $query->whereHas('cliente', function ($subq) use ($filters) {
                    if (isset($filters['nombre_cliente_entrega'])) {
                        $subq->where('nombre', 'like', '%' . $filters['nombre_cliente_entrega'] . '%');
                    }
                    if (isset($filters['nombre_cliente_recibe'])) {
                        $subq->where('nombre', 'like', '%' . $filters['nombre_cliente_recibe'] . '%');
                    }
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($ordenes);
    }
}

    

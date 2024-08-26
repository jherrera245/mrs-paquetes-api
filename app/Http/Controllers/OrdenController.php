<?php

namespace App\Http\Controllers;

use App\Models\AsignacionRutas;
use App\Models\Direcciones;
use App\Models\Transaccion;
use App\Models\DetalleOrden;
use App\Models\Orden;
use App\Models\Paquete;
use App\Models\User;
use App\Models\HistorialPaquete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; // Importar el facade de DomPDF
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\HttpFoundation\Response;
use App\Libraries\FormatterNumberLetter;
use App\Notifications\SendDocuments;
use Illuminate\Support\Facades\Notification;

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
                'numero_seguimiento' => $orden->numero_seguimiento,
                'estado_pago' => $orden->estado_pago,
                'detalles' => $orden->detalles->map(function ($detalle) {
                    return [
                        'id_paquete' => $detalle->id_paquete,
                        'descripcion' => $detalle->descripcion,
                        'precio' => $detalle->precio,
                        'tipo_entrega' => $detalle->tipoEntrega->entrega,
                        'recibe' => $detalle->direccionEntrega->nombre_contacto,
                        'telefono' => $detalle->direccionEntrega->telefono,
                        'departamento' => $detalle->direccionEntrega->departamento->nombre,
                        'municipio' => $detalle->direccionEntrega->municipio->nombre,
                        'direccion' => $detalle->direccionEntrega->direccion,
                        'tipo_paquete' => $detalle->paquete->tipoPaquete->nombre,
                        'tipo_caja' => $detalle->paquete->empaquetado->empaquetado,
                        'peso' => $detalle->paquete->peso,
                        'estado_paquete' => $detalle->paquete->estado->nombre,
                        "validacion_entrega" => $detalle->validacion_entrega,
                    ];
                }),
                'created_at' => $orden->created_at,
                'updated_at' => $orden->updated_at,
            ];
        });

        return response()->json($ordenes, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_cliente' => 'required|integer|exists:clientes,id',
            'nombre_contacto' => 'required|string',
            'telefono' => 'required|string',
            'id_direccion' => 'required|integer|exists:direcciones,id',
            'id_tipo_pago' => 'required|integer|exists:tipo_pago,id',
            'total_pagar' => 'required|numeric',
            'costo_adicional' => 'nullable|numeric',
            'concepto' => 'required|string',
            'tipo_documento' => 'required|string',
            'detalles' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();
        try {
            $orden = $this->createOrder($request);

            // Generar el número de seguimiento con el formato ORD00000000001
            $numeroSeguimiento = 'ORD' . str_pad($orden->id, 10, '0', STR_PAD_LEFT);
            $orden->numero_seguimiento = $numeroSeguimiento;
            $orden->save();

            foreach ($request->input('detalles') as $detalle) {
                $this->createOrderDetail($orden, $detalle);
            }

            DB::commit();
            return response()->json(['message' => 'Orden creada con éxito'], Response::HTTP_CREATED);
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
        $orden = new Orden();
        $orden->id_cliente = $request->input('id_cliente');
        $orden->id_tipo_pago = $request->input('id_tipo_pago');
        $orden->id_direccion = $request->id_direccion;
        $orden->total_pagar = $request->input('total_pagar');
        $orden->costo_adicional = $request->input('costo_adicional');
        $orden->concepto = $request->input('concepto');
        $orden->tipo_documento = $request->input('tipo_documento');
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

        // Creamos la transaccion del paquete.
        Transaccion::create([
            'id_paquete' => $paquete->id,
            'tipoMovimiento' => 'ENTRADA',
            'fecha' => now(),
        ]);

        if ($paquete) {
            $detalleOrden = new DetalleOrden();
            $detalleOrden->id_orden = $orden->id;
            $detalleOrden->id_tipo_entrega = $detalle["id_tipo_entrega"];
            $detalleOrden->id_estado_paquetes = $detalle["id_estado_paquete"];
            $detalleOrden->id_paquete = $paquete->id;
            $detalleOrden->validacion_entrega = 0;
            $detalleOrden->instrucciones_entrega = $detalle['instrucciones_entrega'];
            $detalleOrden->descripcion = $detalle['descripcion'];
            $detalleOrden->precio = $detalle['precio'];
            $detalleOrden->fecha_ingreso = now();
            $detalleOrden->fecha_entrega = $detalle['fecha_entrega'];
            $detalleOrden->id_direccion_entrega = $detalle['id_direccion'];

            // Guardar el detalle de la orden para obtener su ID
            $detalleOrden->save();

           
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
        $orden = Orden::with('detalles')->find($id);

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
            'numero_seguimiento' => $orden->numero_seguimiento,
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
            'detalles' => $orden->detalles->map(function ($detalle) {
                return [
                    'id' => $detalle->id,
                    'id_orden' => $detalle->id_orden,
                    'id_paquete' => $detalle->id_paquete,
                    'id_tipo_entrega' => $detalle->id_tipo_entrega,
                    'id_estado_paquetes' => $detalle->id_estado_paquetes,
                    'id_direccion_entrega' => $detalle->id_direccion_entrega,
                    "validacion_entrega" => $detalle->validacion_entrega,
                    'descripcion' => $detalle->descripcion,
                    'precio' => $detalle->precio,
                    'tipo_entrega' => $detalle->tipoEntrega->entrega,
                    'recibe' => $detalle->direccionEntrega->nombre_contacto,
                    'telefono' => $detalle->direccionEntrega->telefono,
                    'departamento' => $detalle->direccionEntrega->departamento->nombre,
                    'municipio' => $detalle->direccionEntrega->municipio->nombre,
                    'direccion' => $detalle->direccionEntrega->direccion,
                    'tipo_paquete' => $detalle->paquete->tipoPaquete->nombre,
                    'tipo_caja' => $detalle->paquete->empaquetado->empaquetado,
                    'peso' => $detalle->paquete->peso,
                    'estado_paquete' => $detalle->paquete->estado->nombre,
                ];
            }),
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
        $ordenes = $ordenes->map(function ($orden) {
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
                'detalles' => $orden->detalles->map(function ($detalle) {
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

        if (array_key_exists('error', $comprobante)) {
            return response()->json($comprobante['error'], $comprobante['status']);
        }

        $user = User::find($comprobante["id_user"]);
        $user->notify(new SendDocuments($comprobante));

        return response()->json(
            [
                'message' => 'Pago procesado con éxito',
                'comprobante' => $comprobante
            ],
            Response::HTTP_OK
        );
    }

    public function getComprobante($id)
    {
        $comprobante = $this->generarComprobante($id);

        if (array_key_exists('error', $comprobante)) {
            return response()->json($comprobante['error'], $comprobante['status']);
        }

        return response()->json(['comprobante' => $comprobante], Response::HTTP_OK);
    }

    public function reenviarComprobante(Request $request, $id)
    {
        $comprobante = $this->generarComprobante($id);

        if (array_key_exists('error', $comprobante)) {
            return response()->json($comprobante['error'], $comprobante['status']);
        }

        $user = User::find($comprobante["id_user"]);
        $user->notify(new SendDocuments($comprobante));

        return response()->json(['message' => "Documento enviado correctamente"], Response::HTTP_OK);
    }

    public function generarComprobante($id)
    {
        $orden = DB::table('ordenes')->where('id', $id)->first();

        if (!$orden) {
            return [
                'error' => ["error" => "Order not found"],
                'status' =>  Response::HTTP_NOT_FOUND
            ];
        }

        $tipo_dte = $orden->tipo_documento == 'consumidor_final' ? '01' : '03';
        $view_render = $tipo_dte == '01' ? 'pdf.consumidor_final' : 'pdf.credito_fiscal';
        $numero_control = 'DTE-'.$tipo_dte.'-M001P001-' . str_pad($id, 15, '0', STR_PAD_LEFT);
        $codigo_generacion = Str::uuid();
        $sello_registro = sha1($codigo_generacion);
        $sello_registro = date('Y').substr($sello_registro, 0, 36);

        $formater = new FormatterNumberLetter();
        $total_letras = $formater->to_invoice($orden->total_pagar, 2, "DOLARES ESTADOUNIDENSES");

        // Generar el código QR
        $makeQr = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($orden->numero_seguimiento)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->size(200)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->build();

        $qrCodeBase64 = base64_encode($makeQr->getString());
        
        $cliente = $results = DB::table('clientes as cli')
        ->join('users as u', 'u.id', '=', 'cli.id_user')
        ->select(
            'cli.id',
            'cli.nombre',
            'cli.apellido',
            'cli.nombre_comercial',
            'cli.nombre_empresa',
            'cli.direccion',
            'cli.dui',
            'cli.nit',
            'cli.id_tipo_persona',
            'cli.es_contribuyente',
            'u.email',
            'cli.telefono',
            'cli.id_user',
        )->where('cli.id', $orden->id_cliente)->first();

        $detalles =  DB::table('detalle_orden as do')
        ->select(
            'p.id as codigo_paquete',
            'p.peso',
            'p.uuid',
            'do.descripcion',
            'do.instrucciones_entrega',
            'do.precio'
        )
        ->join('paquetes as p', 'p.id', '=', 'do.id_paquete')
        ->where('do.id_orden', $id)
        ->get();

        $pdf = PDF::loadView($view_render, 
            [
                "orden" => $orden, 
                "numero_control" => $numero_control,
                "codigo_generacion" => $codigo_generacion,
                "sello_recepcion" => $sello_registro,
                "qrCodeBase64" => $qrCodeBase64,
                'logo' => 'images/logo-claro.png',
                "cliente" => $cliente, 
                "detalles" => $detalles,
                "total_letras" => $total_letras
            ]
        );

        $output = $pdf->output();

        return [
            'id_user' => $cliente->id_user,
            'cliente' => $cliente->nombre.' '.$cliente->apellido . ($cliente->id_tipo_persona == 2 ?: ' de '.$cliente->nombre_comercial),
            'numero_control' => $numero_control,
            'fecha' =>  $orden->created_at,
            'tipo_documento' =>  $tipo_dte == '01' ? 'Factura Consumidor Final' : 'Credito Fiscal',
            'total_pagar' => $orden->total_pagar,
            'pdfBase64' => base64_encode($output),
        ];
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
    
    public function buscarPorNumeroSeguimiento(Request $request)
    {
        // Validar el número de seguimiento
        $request->validate([
            'numero_seguimiento' => 'required|string|max:255'
        ]);

        // Buscar la orden por numero_seguimiento
        $orden = Orden::where('numero_seguimiento', $request->numero_seguimiento)
                      ->with('detalleOrden.paquete.estado') // Cargar la relación estado del paquete
                      ->first();

        if ($orden) {
            // Obtener detalles de la orden (suponiendo que todos los detalles son similares)
            $detalleOrden = $orden->detalleOrden->first();

            // Extraer paquetes únicos asociados a la orden y eliminar valores nulos
            $paquetes = $orden->detalleOrden->map(function ($detalle) {
                return $detalle->paquete;
            })->filter()->unique('id')->values(); // Usa filter() para eliminar valores nulos y values() para resetear las claves

            // Mapear paquetes con sus estados
            $paquetesConEstados = $paquetes->map(function ($paquete) {
                return [
                    'id' => $paquete->id,
                    'id_tipo_paquete' => $paquete->id_tipo_paquete,
                    'id_empaque' => $paquete->id_empaque,
                    'peso' => $paquete->peso,
                    'uuid' => $paquete->uuid,
                    'tag' => $paquete->tag,
                    'fecha_envio' => $paquete->fecha_envio,
                    'fecha_entrega_estimada' => $paquete->fecha_entrega_estimada,
                    'descripcion_contenido' => $paquete->descripcion_contenido,
                    'estado' => $paquete->estado ? [
                        'id' => $paquete->estado->id,
                        'nombre' => $paquete->estado->nombre
                    ] : null
                ];
            });

            return response()->json([
                'id' => $orden->id,
                'id_cliente' => $orden->id_cliente,
                'id_direccion' => $orden->id_direccion,
                'id_tipo_pago' => $orden->id_tipo_pago,
                'total_pagar' => $orden->total_pagar,
                'costo_adicional' => $orden->costo_adicional,
                'concepto' => $orden->concepto,
                'numero_seguimiento' => $orden->numero_seguimiento,
                'tipo_documento' => $orden->tipo_documento,
                'estado_pago' => $orden->estado_pago,
                'created_at' => $orden->created_at,
                'updated_at' => $orden->updated_at,
                'detalleOrden' => $detalleOrden ? [
                    'id' => $detalleOrden->id,
                    'id_paquete' => $detalleOrden->id_paquete,
                    'id_tipo_entrega' => $detalleOrden->id_tipo_entrega,
                    'id_estado_paquete' => $detalleOrden->id_estado_paquete,
                    'id_direccion_entrega' => $detalleOrden->id_direccion_entrega,
                    'validacion_entrega' => $detalleOrden->validacion_entrega,
                    'instrucciones_entrega' => $detalleOrden->instrucciones_entrega,
                    'descripcion' => $detalleOrden->descripcion,
                    'precio' => $detalleOrden->precio,
                    'fecha_ingreso' => $detalleOrden->fecha_ingreso,
                    'fecha_entrega' => $detalleOrden->fecha_entrega
                ] : null,
                'paquetes' => $paquetesConEstados
            ]);
        }

        return response()->json(['message' => 'Orden no encontrada'], 404);
    }

}

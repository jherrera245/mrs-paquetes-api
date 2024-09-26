<?php

namespace App\Http\Controllers;

use App\Models\AsignacionRutas;
use App\Models\Direcciones;
use App\Models\Transaccion;
use App\Models\DetalleOrden;
use App\Models\Orden;
use App\Models\Paquete;
use Carbon\Carbon;
use App\Models\User;
use App\Models\HistorialPaquete;
use App\Models\HistorialOrdenTracking;
use App\Models\EstadoPaquete;
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
use App\Models\Clientes;
use App\Models\Inventario;
use App\Models\Kardex;
use App\Notifications\SendDocuments;
use Illuminate\Support\Facades\Notification;
use JWTAuth;

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

        $ordenesQuery = Orden::with(['cliente', 'tipoPago', 'direccion', 'detalles' => function ($query) {
            // Excluir detalles de órdenes canceladas
            $query->where('id_estado_paquetes', '!=', 13);
        }])->where('estado', '!=', 'Cancelada')->search($filters);

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
            'id_direccion' => 'required|integer|exists:direcciones,id',
            'id_tipo_pago' => 'required|integer|exists:tipo_pago,id',
            // relacion con ubicacion paquete, puede ser nulo.
            'id_ubicacion_paquete' => 'nullable|integer|exists:ubicacion_paquete,id',
            'total_pagar' => 'required|numeric',
            'costo_adicional' => 'nullable|numeric',
            'concepto' => 'required|string',
            'tipo_documento' => 'required|string',
            'tipo_orden' => 'required|string',
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

            // generar el numero de tracking con el formato TR-anio-(10 digitos random)
            $numeroTracking = 'TR-' . date('Y') . '-' . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
            $orden->numero_tracking = $numeroTracking;
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
        $orden->tipo_orden = $request->input('tipo_orden');
        $orden->estado = 'En_Proceso';
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
        $paquete->id_tamano_paquete = $detalle["id_tamano_paquete"];
        // enviamos la ubicacion del paquete como nulo.
        $paquete->id_ubicacion = null;
        $paquete->id_empaque = $detalle["id_empaque"];
        $paquete->peso = $detalle["peso"];
        $paquete->uuid = $uuid;
        $paquete->tag = $tag;
        $paquete->id_estado_paquete = $orden->tipo_orden === 'preorden' ? 3 : 1;
        $paquete->fecha_envio = $detalle["fecha_envio"];
        $paquete->fecha_entrega_estimada = $detalle["fecha_entrega_estimada"];
        $paquete->descripcion_contenido = $detalle["descripcion_contenido"];
        $paquete->save();

        if ($paquete) {
            $detalleOrden = new DetalleOrden();
            $detalleOrden->id_orden = $orden->id;
            $detalleOrden->id_tipo_entrega = $detalle["id_tipo_entrega"];
            // Hago una validacion ternaria para el estado, si la orden es preorden, el estado es 3, si no, es 1.
            $detalleOrden->id_estado_paquetes = $orden->tipo_orden === 'preorden' ? 3 : 1;
            $detalleOrden->id_paquete = $paquete->id;
            $detalleOrden->validacion_entrega = 0;
            $detalleOrden->instrucciones_entrega = $detalle['instrucciones_entrega'];
            $detalleOrden->descripcion = $detalle['descripcion'];
            $detalleOrden->precio = $detalle['precio'];
            $detalleOrden->fecha_ingreso = now();
            $detalleOrden->fecha_entrega = $detalle['fecha_entrega'];
            $detalleOrden->id_direccion_entrega = $detalle['id_direccion'];
            $detalleOrden->direccion_registrada = json_encode(Direcciones::find($detalle['id_direccion']));

            $orden->estado = 'En_Proceso';
            // Guardar el detalle de la orden para obtener su ID
            $detalleOrden->save();

            // si el tipo de orden es orden, se marca el la entrada en el kardex como "EN_ESPERA_RECOLECCION"
            if ($orden->tipo_orden === 'orden') {
                $kardex = new Kardex();
                $kardex->id_paquete = $paquete->id;
                $kardex->id_orden = $orden->id;
                $kardex->cantidad = 1;
                $kardex->numero_ingreso = $orden->numero_seguimiento;
                $kardex->tipo_movimiento = 'ENTRADA';
                $kardex->tipo_transaccion = 'RECEPCION';
                $kardex->fecha = now();

                $kardex->save();
            } else {
                // crear la transaccion en el kardex e inventario.
                $kardex = new Kardex();
                $kardex->id_paquete = $paquete->id;
                $kardex->id_orden = $orden->id;
                $kardex->cantidad = 1;
                $kardex->numero_ingreso = $orden->numero_seguimiento;
                $kardex->tipo_movimiento = 'ENTRADA';
                $kardex->tipo_transaccion = 'PREORDEN';
                $kardex->fecha = now();

                $kardex->save();
            }

            // entrada en inventario.
            $inventario = new Inventario();

            $inventario->id_paquete = $paquete->id;
            $inventario->numero_ingreso = $orden->numero_seguimiento;
            $inventario->cantidad = 1;
            $inventario->fecha_entrada = now();
            $inventario->estado = 1;

            $inventario->save();
        } else {
            throw new \Exception('Error al generar el paquete');
        }
    }

    public function updateOrder($id_orden, Request $request)
    {
        try {
            // Validamos los datos del request
            $validatedData = $request->validate([
                'id_cliente' => 'required|integer|exists:clientes,id',
                'id_tipo_pago' => 'required|integer|exists:tipo_pago,id',
                'id_direccion' => 'required|integer|exists:direcciones,id',
                'total_pagar' => 'required|numeric',
                'costo_adicional' => 'nullable|numeric',
                'concepto' => 'required|string',
                'tipo_documento' => 'required|string',
            ]);

            // Buscar la orden por ID
            $orden = Orden::findOrFail($id_orden);

            // Actualizar los campos con los datos validados
            $orden->id_cliente = $validatedData['id_cliente'] ?? $orden->id_cliente;
            $orden->id_tipo_pago = $validatedData['id_tipo_pago'] ?? $orden->id_tipo_pago;
            $orden->id_direccion = $validatedData['id_direccion'] ?? $orden->id_direccion;
            $orden->total_pagar = $validatedData['total_pagar'] ?? $orden->total_pagar;
            $orden->costo_adicional = $validatedData['costo_adicional'] ?? $orden->costo_adicional; // Si no hay costo adicional, no se cambia
            $orden->concepto = $validatedData['concepto'] ?? $orden->concepto;
            $orden->tipo_documento = $validatedData['tipo_documento'] ?? $orden->tipo_documento;

            $orden->save();

            return response()->json(['message' => 'Orden actualizada correctamente', 'orden' => $orden], Response::HTTP_OK);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Hubo un error al actualizar la orden', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // funcion para actualizar estado de entrega.
    public function updateEstadoEntrega(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'validacion_entrega' => 'required|integer',
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

    public function cancelOrder($id)
    {
        DB::beginTransaction();
        try {
            // Buscar la orden por ID
            $orden = Orden::find($id);
            if (!$orden) {
                return response()->json(['message' => 'No se encontró la orden.'], Response::HTTP_NOT_FOUND);
            }

            // Verificar si la orden ya está cancelada
            if ($orden->estado === 'Cancelada') {
                return response()->json(['message' => 'La orden ya está cancelada.'], Response::HTTP_CONFLICT);
            }

            // Verificar si la orden ya está completada
            if ($orden->estado === 'Completada') {
                return response()->json(['message' => 'No se puede cancelar una orden que ya está completada.'], Response::HTTP_CONFLICT);
            }

            // Verificar si el estado de la orden es 'En_proceso'
            if ($orden->estado !== 'En_proceso') {
                return response()->json(['message' => 'Solo se pueden cancelar órdenes en estado En_proceso.'], Response::HTTP_CONFLICT);
            }

            // Cambiar el estado de la orden a "Cancelada"
            $orden->estado = 'Cancelada';
            $orden->save();

            // Si hay paquetes relacionados, también cambiar su estado a "Cancelado"
            $paquetes = DetalleOrden::where('id_orden', $orden->id)->get();
            foreach ($paquetes as $paquete) {
                // Encontrar el paquete relacionado y actualizar su estado
                $detallePaquete = Paquete::find($paquete->id_paquete);
                if ($detallePaquete) {
                    $detallePaquete->id_estado_paquete = 13; // Usar el ID 13 para estado 'Cancelado'
                    $detallePaquete->save();
                }

                // Registrar la salida en el Kardex
                $kardex = new Kardex();
                $kardex->id_paquete = $detallePaquete->id;
                $kardex->id_orden = $orden->id;
                $kardex->cantidad = 1;
                $kardex->numero_ingreso = $orden->numero_seguimiento;
                $kardex->tipo_movimiento = 'SALIDA';
                $kardex->tipo_transaccion = 'ORDEN_CANCELADA';
                $kardex->fecha = now();
                $kardex->save();
            }

            DB::commit();
            return response()->json(['message' => 'Orden cancelada correctamente.'], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'message' => 'Error al cancelar la orden',
                    'error' => $e->getMessage(),
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }


    public function destroyDetalleOrden($id)
    {
        try {
            // Encuentra el detalle de la orden por ID
            $detalleOrden = DetalleOrden::find($id);

            $paquete = Paquete::find($detalleOrden->id_paquete);
            if ($paquete) {
                $paquete->id_estado_paquete = 13;
                $paquete->save();
            }

            if (!$detalleOrden) {
                return response()->json(['mensaje' => 'Detalle de orden no encontrado'], Response::HTTP_NOT_FOUND);
            }

            // Encuentra la orden asociada al detalle de la orden
            $orden = Orden::find($detalleOrden->id_orden);



            // Verificar si la orden existe
            if (!$orden) {
                return response()->json(['mensaje' => 'Orden no encontrada'], Response::HTTP_NOT_FOUND);
            }

            // Verificar si el estado de la orden es "Completada"
            if ($orden->estado === 'Completada') {
                return response()->json(['mensaje' => 'No se puede eliminar el detalle de una orden completada'], Response::HTTP_CONFLICT);
            }

            // REGISTRAR SALIDA DEL PAQUETE EN KARDEX.
            $kardex = new Kardex();
            $kardex->id_paquete = $detalleOrden->id_paquete;
            $kardex->id_orden = $detalleOrden->id_orden;
            $kardex->cantidad = 1;
            $kardex->numero_ingreso = $orden->numero_seguimiento;
            $kardex->tipo_movimiento = 'SALIDA';
            $kardex->tipo_transaccion = 'CANCELADO';
            $kardex->fecha = now();
            $kardex->save();

            // REGISTRAR SALIDA EN INVENTARIO
            $inventario = new Inventario();
            $inventario->id_paquete = $detalleOrden->id_paquete;
            $inventario->numero_ingreso = $orden->numero_seguimiento;
            $inventario->cantidad = -1;
            $inventario->fecha_salida = now();
            $inventario->estado = 1;
            $inventario->save();

            // Eliminar el detalle de la orden
            $detalleOrden->delete();

            return response()->json(['mensaje' => 'Detalle de orden eliminado correctamente'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al eliminar el detalle de la orden.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        $orden = Orden::with([
            'tipoPago',
            'detalles.tipoEntrega',
            'detalles.direccionEntrega.departamento',
            'detalles.direccionEntrega.municipio',
            'detalles.paquete.tipoPaquete',
            'detalles.paquete.empaquetado',
            'detalles.paquete.estado'
        ])->find($id);

        if (!$orden) {
            return response()->json(['message' => 'Orden no encontrada'], Response::HTTP_NOT_FOUND);
        }

        $direccion = Direcciones::with(['cliente', 'departamento', 'municipio'])->find($orden->id_direccion);

        $response = [
            'id' => $orden->id,
            'id_cliente' => $orden->id_cliente,
            'id_tipo_pago' => $orden->id_tipo_pago,
            'total_pagar' => $orden->total_pagar,
            'costo_adicional' => $orden->costo_adicional,
            'estado' => $orden->estado,
            'estado_pago' => $orden->estado_pago,
            'tipo_documento' => $orden->tipo_documento,
            'tipo_orden' => $orden->tipo_orden,
            'id_direccion' => $orden->id_direccion,
            'concepto' => $orden->concepto,
            'numero_seguimiento' => $orden->numero_seguimiento,
            'numero_tracking' => $orden->numero_tracking,
            'direccion_emisor' => $direccion ? [
                'id_direccion' => $direccion->id,
                'direccion' => $direccion->direccion,
                'nombre_cliente' => $direccion->cliente->nombre ?? 'NA',
                'apellido_cliente' => $direccion->cliente->apellido ?? 'NA',
                'nombre_contacto' => $direccion->nombre_contacto,
                'telefono' => $direccion->telefono,
                'id_departamento' => $direccion->departamento->nombre ?? 'NA',
                'id_municipio' => $direccion->municipio->nombre ?? 'NA',
                'referencia' => $direccion->referencia,
            ] : null,
            'detalles' => $orden->detalles->map(function ($detalle) {
                return [
                    'id' => $detalle->id,
                    'id_orden' => $detalle->id_orden,
                    'id_paquete' => $detalle->id_paquete,
                    'id_tipo_entrega' => $detalle->id_tipo_entrega,
                    'id_estado_paquetes' => $detalle->id_estado_paquetes,
                    'id_direccion_entrega' => $detalle->id_direccion_entrega,
                    'validacion_entrega' => $detalle->validacion_entrega,
                    'instrucciones_entrega' => $detalle->instrucciones_entrega,
                    'descripcion' => $detalle->descripcion,
                    'precio' => $detalle->precio,
                    'tipo_entrega' => $detalle->tipoEntrega->entrega ?? 'NA',
                    'recibe' => $detalle->direccionEntrega->nombre_contacto ?? 'NA',
                    'telefono' => $detalle->direccionEntrega->telefono ?? 'NA',
                    'departamento' => $detalle->direccionEntrega->departamento->nombre ?? 'NA',
                    'municipio' => $detalle->direccionEntrega->municipio->nombre ?? 'NA',
                    'direccion' => $detalle->direccionEntrega->direccion ?? 'NA',
                    'fecha_ingreso' => $detalle->fecha_ingreso,
                    'fecha_entrega' => $detalle->fecha_entrega,
                    'id_tipo_paquete' => $detalle->paquete->id_tipo_paquete ?? 'NA',
                    'id_tamano_paquete' => $detalle->paquete->id_tamano_paquete,
                    'tipo_caja' => $detalle->paquete->empaquetado->id ?? 'NA',
                    'peso' => $detalle->paquete->peso ?? 'NA',
                    'id_estado_paquete' => $detalle->paquete->id_estado_paquete ?? 'NA',
                    'fecha_envio' => $detalle->paquete->fecha_envio,
                    'fecha_entrega_estimada' => $detalle->paquete->fecha_entrega_estimada,
                    'descripcion_contenido' => $detalle->paquete->descripcion_contenido
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
            'cliente:id,nombre,apellido',
            'direccion:id,direccion,nombre_contacto,telefono,referencia',
            'detalles' => function ($query) {
                // Excluir detalles de órdenes canceladas
                $query->where('id_estado_paquetes', '!=', 13);
            },
            'detalles.paquete:id,descripcion_contenido,peso',
            'tipoPago:id,pago'
        ])->find($id);

        if (!$orden || $orden->estado === 'Cancelada') {
            return response()->json(['message' => 'Orden no encontrada o está cancelada'], Response::HTTP_NOT_FOUND);
        }


        $direccion_emisor = $orden->direccion;

        // Filtrar detalles de órdenes canceladas antes de enviarlos a la vista
        $detalles = $orden->detalles->filter(function ($detalle) {
            return $detalle->id_estado_paquetes != 13;
        });

        $pdf = PDF::loadView('pdf.orden', compact('orden', 'direccion_emisor', 'detalles'));
        $pdfContent = $pdf->output();

        return response()->json(['pdf' => base64_encode($pdfContent)], 200);
    }



    /**
     * Requerimiento 8: Mostrar órdenes del cliente autenticado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function misOrdenes(Request $request)
    {
        $cliente_id = Auth::id();

        // Obtener todas las órdenes del cliente, sin filtrar los detalles cancelados
        $ordenes = Orden::where('id_cliente', $cliente_id)
            ->with(['detalles', 'direccion', 'tipoPago', 'cliente']) // Incluir todas las relaciones sin filtrar
            ->get();

        if ($ordenes->isEmpty()) {
            return response()->json(['message' => 'No se encontraron órdenes'], Response::HTTP_NOT_FOUND);
        }

        // Mapear las órdenes para incluir el estado de la orden
        $ordenes = $ordenes->map(function ($orden) {
            return [
                'id' => $orden->id,
                'cliente' => optional($orden->cliente)->nombre,
                'estado' => $orden->estado,
                'direccion' => [
                    'nombre_contacto' => optional($orden->direccion)->nombre_contacto,
                    'telefono' => optional($orden->direccion)->telefono,
                    'direccion' => optional($orden->direccion)->direccion,
                    'referencia' => optional($orden->direccion)->referencia,
                ],
                'tipo_pago' => optional($orden->tipoPago)->pago,
                'total_pagar' => $orden->total_pagar,
                'costo_adicional' => $orden->costo_adicional,
                'tipo_documento' => $orden->tipo_documento,
                'tipo_orden' => $orden->tipo_orden,
                'concepto' => $orden->concepto,
                'finished' => $orden->finished,
                'created_at' => $orden->created_at,
                'updated_at' => $orden->updated_at,
                'detalles' => $orden->detalles->map(function ($detalle) {
                    return [
                        'id_paquete' => $detalle->id_paquete,
                        'descripcion' => $detalle->descripcion,
                        'precio' => $detalle->precio,
                        'id_tamano_paquete' => $detalle->paquete->id_tamano_paquete,
                        'tamano_paquete' => $detalle->paquete->tamanoPaquete->nombre,
                    ];
                }),
            ];
        });

        return response()->json($ordenes, Response::HTTP_OK);
    }



    private function transformOrden($orden)
    {
        $direccion = $orden->direccion;

        return [
            // Datos principales de la orden
            'id' => $orden->id,
            'id_cliente' => $orden->id_cliente,
            'cliente' => [
                'nombre' => $orden->cliente->nombre ?? 'NA',
                'apellido' => $orden->cliente->apellido ?? 'NA',
            ],
            'id_tipo_pago' => $orden->id_tipo_pago,
            'tipo_pago' => $orden->tipoPago->pago ?? 'NA',
            'total_pagar' => $orden->total_pagar,
            'costo_adicional' => $orden->costo_adicional,
            'estado' => $orden->estado,
            'estado_pago' => $orden->estado_pago,
            'tipo_documento' => $orden->tipo_documento,
            'tipo_orden' => $orden->tipo_orden,
            'id_direccion' => $orden->id_direccion,
            'concepto' => $orden->concepto,
            'numero_seguimiento' => $orden->numero_seguimiento,
            'numero_tracking' => $orden->numero_tracking,

            // Dirección del emisor
            'direccion_emisor' => $direccion ? [
                'id_direccion' => $direccion->id,
                'direccion' => $direccion->direccion,
                'nombre_cliente' => $direccion->cliente->nombre ?? 'NA',
                'apellido_cliente' => $direccion->cliente->apellido ?? 'NA',
                'nombre_contacto' => $direccion->nombre_contacto,
                'telefono' => $direccion->telefono,
                'id_departamento' => $direccion->id_departamento,
                'departamento' => $direccion->departamento->nombre ?? 'NA',
                'id_municipio' => $direccion->id_municipio,
                'municipio' => $direccion->municipio->nombre ?? 'NA',
                'referencia' => $direccion->referencia,
            ] : null,

            // Detalles de la orden
            'detalles' => $orden->detalles->map(function ($detalle) {
                return [
                    'id' => $detalle->id,
                    'id_orden' => $detalle->id_orden,
                    'id_paquete' => $detalle->id_paquete,
                    'id_tipo_entrega' => $detalle->id_tipo_entrega,
                    'tipo_entrega' => $detalle->tipoEntrega->entrega ?? 'NA',
                    'id_estado_paquetes' => $detalle->id_estado_paquetes,
                    'id_direccion_entrega' => $detalle->id_direccion_entrega,
                    'validacion_entrega' => $detalle->validacion_entrega,
                    'instrucciones_entrega' => $detalle->instrucciones_entrega,
                    'descripcion' => $detalle->descripcion,
                    'precio' => $detalle->precio,
                    'recibe' => $detalle->direccionEntrega->nombre_contacto ?? 'NA',
                    'telefono' => $detalle->direccionEntrega->telefono ?? 'NA',
                    'departamento' => $detalle->direccionEntrega->departamento->nombre ?? 'NA',
                    'municipio' => $detalle->direccionEntrega->municipio->nombre ?? 'NA',
                    'direccion' => $detalle->direccionEntrega->direccion ?? 'NA',
                    'fecha_ingreso' => $detalle->fecha_ingreso,
                    'fecha_entrega' => $detalle->fecha_entrega,

                    // Información del paquete
                    'paquete' => [
                        'id_tipo_paquete' => $detalle->paquete->id_tipo_paquete ?? 'NA',
                        'id_tamano_paquete' => $detalle->paquete->id_tamano_paquete ?? 'NA',
                        'tipo_caja' => $detalle->paquete->empaquetado->id ?? 'NA',
                        'peso' => $detalle->paquete->peso ?? 'NA',
                        'id_estado_paquete' => $detalle->paquete->id_estado_paquete ?? 'NA',
                        'fecha_envio' => $detalle->paquete->fecha_envio,
                        'fecha_entrega_estimada' => $detalle->paquete->fecha_entrega_estimada,
                        'descripcion_contenido' => $detalle->paquete->descripcion_contenido
                    ]
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

        // Verificar si la orden ya está cancelada
        if ($orden->estado === 'Cancelada') {
            return response()->json(['message' => 'No se puede hacer el pago porque la orden está cancelada.'], Response::HTTP_CONFLICT);
        }
        // verifica el tipo de orden para cambiar el estado del paquete.
        if ($orden->tipo_orden === 'orden') {
            $paquetes = DetalleOrden::where('id_orden', $orden->id)->get();
            foreach ($paquetes as $paquete) {
                $paquete->id_estado_paquetes = 14;
                $paquete->save();

                // cambia estado de paquete tambien en la tabla de paquetes.
                $paquete = Paquete::find($paquete->id_paquete);
                $paquete->id_estado_paquete = 14;
                $paquete->save();
            }
        } else {
            $paquetes = DetalleOrden::where('id_orden', $orden->id)->get();
            foreach ($paquetes as $paquete) {
                $paquete->id_estado_paquetes = 15;
                $paquete->save();

                // cambia estado de paquete tambien en la tabla de paquetes.
                $paquete = Paquete::find($paquete->id_paquete);
                $paquete->id_estado_paquete = 15;
                $paquete->save();
            }
        }

        // Actualizar el estado de la orden
        $orden->estado_pago = 'pagado';
        $orden->tipo_orden = 'orden';
        $orden->estado = 'Completada';
        $orden->save();

        // Generar y enviar el comprobante
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
        $orden = Orden::find($id);

        // Verificar que la orden no esté cancelada
        if (!$orden || $orden->estado === 'Cancelada') {
            return response()->json(['message' => 'No se puede generar comprobante para una orden cancelada o la orden no fue encontrada.'], Response::HTTP_NOT_FOUND);
        }

        $comprobante = $this->generarComprobante($id);

        if (array_key_exists('error', $comprobante)) {
            return response()->json($comprobante['error'], $comprobante['status']);
        }

        return response()->json(['comprobante' => $comprobante], Response::HTTP_OK);
    }

    public function reenviarComprobante(Request $request, $id)
    {
        $orden = Orden::find($id);

        // Verificar que la orden no esté cancelada
        if (!$orden || $orden->estado === 'Cancelada') {
            return response()->json(['message' => 'No se puede reenviar comprobante para una orden cancelada o la orden no fue encontrada.'], Response::HTTP_NOT_FOUND);
        }

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
        $orden = DB::table('ordenes')->where('id', $id)->where('estado', '!=', 'Cancelada')->first();

        if (!$orden) {
            return [
                'error' => ["error" => "Order not found or is canceled"],
                'status' =>  Response::HTTP_NOT_FOUND
            ];
        }
        $numero_tracking = $orden->numero_tracking;
        $tipo_dte = $orden->tipo_documento == 'consumidor_final' ? '01' : '03';
        $view_render = $tipo_dte == '01' ? 'pdf.consumidor_final' : 'pdf.credito_fiscal';
        $numero_control = 'DTE-' . $tipo_dte . '-M001P001-' . str_pad($id, 15, '0', STR_PAD_LEFT);
        $codigo_generacion = Str::uuid();
        $sello_registro = sha1($codigo_generacion);
        $sello_registro = date('Y') . substr($sello_registro, 0, 36);

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
        $pdf = PDF::loadView(
            $view_render,
            [
                "orden" => $orden,
                "numero_control" => $numero_control,
                "codigo_generacion" => $codigo_generacion,
                "sello_recepcion" => $sello_registro,
                "numero_tracking" => $numero_tracking,
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
            'cliente' => $cliente->nombre . ' ' . $cliente->apellido . ($cliente->id_tipo_persona == 2 ?: ' de ' . $cliente->nombre_comercial),
            'numero_control' => $numero_control,
            'numero_tracking' => $numero_tracking,
            'fecha' =>  $orden->created_at,
            'tipo_documento' =>  $tipo_dte == '01' ? 'Factura Consumidor Final' : 'Credito Fiscal',
            'total_pagar' => $orden->total_pagar,
            'pdfBase64' => base64_encode($output),
        ];
    }

    public function misOrdenesAsignadas(Request $request)
    {
        $validated = $request->validate([
            'id_ruta' => 'required|integer',
            'uuid_paquete' => 'nullable|string',
            'fecha_desde' => 'nullable|date',
            'fecha_hasta' => 'nullable|date',
            'nombre_cliente_entrega' => 'nullable|string',
            'nombre_cliente_recibe' => 'nullable|string',
        ]);

        $query = DetalleOrden::with(['paquete', 'clienteEntrega', 'orden.cliente'])
            ->whereHas('orden', function ($q) use ($validated) {
                $q->whereHas('asignacionRuta', function ($subq) use ($validated) {
                    $subq->where('id_ruta', $validated['id_ruta']);
                });
            });

        if (!empty($validated['uuid_paquete'])) {
            $query->whereHas('paquete', function ($q) use ($validated) {
                $q->where('uuid', 'like', '%' . $validated['uuid_paquete'] . '%');
            });
        }

        if (!empty($validated['fecha_desde'])) {
            $query->whereDate('fecha_entrega', '>=', $validated['fecha_desde']);
        }

        if (!empty($validated['fecha_hasta'])) {
            $query->whereDate('fecha_entrega', '<=', $validated['fecha_hasta']);
        }

        if (!empty($validated['nombre_cliente_entrega'])) {
            $query->whereHas('clienteEntrega', function ($q) use ($validated) {
                $q->where('nombre', 'like', '%' . $validated['nombre_cliente_entrega'] . '%');
            });
        }

        if (!empty($validated['nombre_cliente_recibe'])) {
            $query->whereHas('orden.cliente', function ($q) use ($validated) {
                $q->where('nombre', 'like', '%' . $validated['nombre_cliente_recibe'] . '%');
            });
        }

        $detalles = $query->orderBy('fecha_entrega', 'desc')->get();

        return Response::json($detalles);
    }


    public function buscarPorNumeroSeguimiento(Request $request)
    {
        // Validar el número de seguimiento
        $request->validate([
            'numero_tracking' => 'required|string|max:255'
        ]);

        // Buscar la orden por numero_seguimiento
        $orden = Orden::where('numero_tracking', $request->numero_tracking)
            ->where('estado', '!=', 'Cancelada') // Excluir órdenes canceladas
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
                'numero_tracking' => $orden->numero_tracking,
                'tipo_documento' => $orden->tipo_documento,
                'estado_pago' => $orden->estado_pago,
                'created_at' => $orden->created_at,
                'updated_at' => $orden->updated_at,
                'detalleOrden' => $detalleOrden ? [
                    'id' => $detalleOrden->id,
                    'id_paquete' => $detalleOrden->id_paquete,
                    'id_tipo_entrega' => $detalleOrden->id_tipo_entrega,
                    'id_estado_paquetes' => $detalleOrden->id_estado_paquetes,
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

    public function ordenCliente(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrado'], Response::HTTP_UNAUTHORIZED);
            }

            // Obtén el cliente asociado al usuario
            $cliente = $user->cliente;
            if (!$cliente) {
                return response()->json(['error' => 'Cliente no encontrado'], Response::HTTP_UNAUTHORIZED);
            }

            // Valida los datos de la solicitud
            $validator = Validator::make($request->all(), [
                'id_direccion' => 'required|integer|exists:direcciones,id',
                'id_tipo_pago' => 'required|integer|exists:tipo_pago,id',
                'id_ubicacion_paquete' => 'nullable|integer|exists:ubicacion_paquete,id',
                'total_pagar' => 'required|numeric',
                'costo_adicional' => 'nullable|numeric',
                'concepto' => 'required|string',
                'tipo_documento' => 'required|string',
                'tipo_orden' => 'required|string',
                'detalles' => 'required|array'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            DB::beginTransaction();
            try {
                $orden = new Orden();
                $orden->id_cliente = $cliente->id; // Usa el ID del cliente asociado
                $orden->id_tipo_pago = $request->input('id_tipo_pago');
                $orden->id_direccion = $request->id_direccion;
                $orden->total_pagar = $request->input('total_pagar');
                $orden->costo_adicional = $request->input('costo_adicional');
                $orden->concepto = $request->input('concepto');
                $orden->tipo_documento = $request->input('tipo_documento');
                $orden->tipo_orden = 'preorden';
                $orden->save();

                // Generar el número de seguimiento con el formato ORD00000000001
                $numeroSeguimiento = 'ORD' . str_pad($orden->id, 10, '0', STR_PAD_LEFT);
                $numeroTracking = 'TR-' . date('Y') . '-' . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
                $orden->numero_tracking = $numeroTracking;
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
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token is invalid'], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function misOrdenesCliente()
    {
        try {
            // Obtener el usuario autenticado
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['error' => 'usuario no encontrado'], Response::HTTP_UNAUTHORIZED);
            }

            // Obtener el cliente asociado al usuario
            $cliente = $user->cliente;
            if (!$cliente) {
                return response()->json(['error' => 'Cliente no encontrado'], Response::HTTP_UNAUTHORIZED);
            }

            $hoy = Carbon::today(); // Fecha actual
            $inicio = $hoy->copy()->subDays(29); // 29 días anteriores

            $start = Carbon::parse($inicio)->format('Y-m-d');
            $end = Carbon::parse($hoy)->format('Y-m-d');

            // Recuperar las órdenes asociadas al cliente junto con sus detalles
            $ordenes = Orden::where('id_cliente', $cliente->id)
                ->where('estado', '!=', 'Cancelada') // Excluir órdenes canceladas
                ->with(['detalles' => function ($query) {
                    $query->where('id_estado_paquetes', '!=', 13); // Excluir detalles cancelados
                }, 'direccion', 'tipoPago', 'cliente'])
                ->get();


            // Filtrar detalles cancelados antes de procesar
            $result = $ordenes->map(function ($orden) {
                $orden->detalles = $orden->detalles->filter(function ($detalle) {
                    return $detalle->id_estado_paquetes != 13;
                });

                return [
                    'id' => $orden->id,
                    'id_cliente' => $orden->id_cliente,
                    'id_direccion' => $orden->id_direccion,
                    'id_tipo_pago' => $orden->id_tipo_pago,
                    'total_pagar' => $orden->total_pagar,
                    'costo_adicional' => $orden->costo_adicional,
                    'concepto' => $orden->concepto,
                    'finished' => $orden->finished,
                    'numero_seguimiento' => $orden->numero_seguimiento,
                    'numero_tracking' => $orden->numero_tracking,
                    'tipo_documento' => $orden->tipo_documento,
                    'tipo_orden' => $orden->tipo_orden,
                    'tipo_pago' => $orden->tipoPago->pago ?? 'NA',
                    'detalles' => $orden->detalles->map(function ($detalle) {
                        return [
                            'id_orden' => $detalle->id_orden,
                            'id_paquete' => $detalle->id_paquete,
                            'id_tamano_paquete' => $detalle->paquete->id_tamano_paquete,
                            'tamano_paquete' => $detalle->paquete->tamanoPaquete->nombre,
                            'id_tipo_entrega' => $detalle->id_tipo_entrega,
                            'id_estado_paquetes' => $detalle->id_estado_paquetes,
                            'id_direccion_entrega' => $detalle->id_direccion_entrega,
                            'validacion_entrega' => $detalle->validacion_entrega,
                            'instrucciones_entrega' => $detalle->instrucciones_entrega,
                            'descripcion' => $detalle->descripcion,
                            'precio' => $detalle->precio,
                            'fecha_ingreso' => $detalle->fecha_ingreso,
                            'fecha_entrega' => $detalle->fecha_entrega
                        ];
                    })
                ];
            });

            return response()->json($result, Response::HTTP_OK);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token es invalido'], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'id_cliente' => 'required|integer|exists:clientes,id',
            'id_direccion' => 'required|integer|exists:direcciones,id',
            'id_tipo_pago' => 'required|integer|exists:tipo_pago,id',
            'id_ubicacion_paquete' => 'nullable|integer|exists:ubicacion_paquete,id',
            'total_pagar' => 'required|numeric',
            'costo_adicional' => 'nullable|numeric',
            'concepto' => 'required|string',
            'tipo_documento' => 'required|string',
            'tipo_orden' => 'required|string',
            'detalles' => 'required|array',
            'detalles.*.id_tipo_paquete' => 'required|integer',
            'detalles.*.id_tamano_paquete' => 'required|integer',
            'detalles.*.id_empaque' => 'required|integer',
            'detalles.*.peso' => 'required|numeric',
            'detalles.*.descripcion_contenido' => 'nullable|string',
            'detalles.*.id_estado_paquete' => 'required|integer',
            'detalles.*.fecha_envio' => 'required|date',
            'detalles.*.fecha_entrega_estimada' => 'required|date',
            'detalles.*.id_tipo_entrega' => 'required|integer',
            'detalles.*.instrucciones_entrega' => 'nullable|string',
            'detalles.*.descripcion' => 'nullable|string',
            'detalles.*.precio' => 'required|numeric',
            'detalles.*.fecha_entrega' => 'nullable|date',
            'detalles.*.id_direccion' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();
        try {

            $orden = Orden::findOrFail($id);
            $orden->id_cliente = $request->input('id_cliente');
            $orden->id_tipo_pago = $request->input('id_tipo_pago');
            $orden->id_direccion = $request->input('id_direccion');
            $orden->total_pagar = $request->input('total_pagar');
            $orden->costo_adicional = $request->input('costo_adicional');
            $orden->concepto = $request->input('concepto');
            $orden->tipo_documento = $request->input('tipo_documento');
            $orden->tipo_orden = $request->input('tipo_orden');
            $orden->save();

            $existingPackageIds = DB::table('ordenes')
                ->join('detalle_orden', 'ordenes.id', '=', 'detalle_orden.id_orden')
                ->join('paquetes', 'detalle_orden.id_paquete', '=', 'paquetes.id')
                ->where('ordenes.id', $orden->id)
                ->pluck('detalle_orden.id_paquete');

            DetalleOrden::where('id_orden', $orden->id)->delete();

            $newPackageIds = [];

            foreach ($request->input('detalles') as $detalle) {
                // Check if 'id_paquete' exists in the detail
                $idPaquete = isset($detalle['id_paquete']) ? $detalle['id_paquete'] : null;

                // Generate UUID and QR code for the package
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

                $paquete = Paquete::updateOrCreate(
                    ['id' => $idPaquete],
                    [
                        'id_tipo_paquete' => $detalle['id_tipo_paquete'],
                        'id_tamano_paquete' => $detalle['id_tamano_paquete'],
                        'id_empaque' => $detalle['id_empaque'],
                        'peso' => $detalle['peso'],
                        'descripcion_contenido' => $detalle['descripcion_contenido'] ?? null,
                        'id_estado_paquete' => $detalle['id_estado_paquete'],
                        'fecha_envio' => $detalle['fecha_envio'],
                        'fecha_entrega_estimada' => $detalle['fecha_entrega_estimada'],
                        'uuid' => $uuid,
                        'tag' => $qrCodeUrl
                    ]
                );

                Kardex::create([
                    'id_paquete' => $paquete->id,
                    'id_orden' => $orden->id,
                    'cantidad' => 1,
                    'numero_ingreso' => $orden->numero_seguimiento,
                    'tipo_movimiento' => 'ENTRADA',
                    'tipo_transaccion' => 'ORDEN',
                    'fecha' => now()
                ]);

                Inventario::create([
                    'id_paquete' => $paquete->id,
                    'numero_ingreso' => $orden->numero_seguimiento,
                    'cantidad' => 1,
                    'fecha_entrada' => now(),
                    'estado' => 1
                ]);

                $newPackageIds[] = $paquete->id;

                $detalleOrden = new DetalleOrden();
                $detalleOrden->id_orden = $orden->id;
                $detalleOrden->id_tipo_entrega = $detalle['id_tipo_entrega'];
                $detalleOrden->id_estado_paquetes = $detalle['id_estado_paquete'];
                $detalleOrden->id_paquete = $paquete->id;
                $detalleOrden->validacion_entrega = $detalle['validacion_entrega'] ?? 0;
                $detalleOrden->instrucciones_entrega = $detalle['instrucciones_entrega'] ?? null;
                $detalleOrden->descripcion = $detalle['descripcion'] ?? null;
                $detalleOrden->precio = $detalle['precio'];
                $detalleOrden->fecha_ingreso = $detalle['fecha_ingreso'] ?? now();
                $detalleOrden->fecha_entrega = $detalle['fecha_entrega'];
                $detalleOrden->id_direccion_entrega = $detalle['id_direccion'];
                $detalleOrden->direccion_registrada = json_encode(Direcciones::find($detalle['id_direccion']));
                $detalleOrden->save();
            }

            $packagesToSoftDelete = $existingPackageIds->diff($newPackageIds);
            if ($packagesToSoftDelete->isNotEmpty()) {
                Paquete::whereIn('id', $packagesToSoftDelete)->update(['eliminado_at' => now()]);
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

    // funcion para actualizar el detalle de una orden.
    public function updateDetalleOrden(Request $request, $id)
    {
        // Validación de los datos
        $validator = Validator::make($request->all(), [
            'id_tipo_entrega' => 'required|integer|exists:tipo_entrega,id',
            'id_estado_paquetes' => 'required|integer|exists:estado_paquetes,id',
            'validacion_entrega' => 'integer',
            'instrucciones_entrega' => 'nullable|string',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric',
            'fecha_ingreso' => 'required|date',
            'fecha_entrega' => 'required|date',
            'id_direccion_entrega' => 'required|integer|exists:direcciones,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Inicio de la transacción
        DB::beginTransaction();
        try {
            // Obtener el detalle de la orden
            $detalleOrden = DetalleOrden::findOrFail($id);

            // Actualizar los campos opcionales y obligatorios
            $detalleOrden->id_tipo_entrega = $request->input('id_tipo_entrega') ?? $detalleOrden->id_tipo_entrega;
            $detalleOrden->id_estado_paquetes = $request->input('id_estado_paquetes') ?? $detalleOrden->id_estado_paquetes;
            $detalleOrden->id_paquete = $request->input('id_paquete') ?? $detalleOrden->id_paquete;
            $detalleOrden->id_direccion_entrega = $request->input('id_direccion_entrega') ?? $detalleOrden->id_direccion_entrega;
            $detalleOrden->validacion_entrega = $request->input('validacion_entrega') ?? $detalleOrden->validacion_entrega;
            $detalleOrden->instrucciones_entrega = $request->input('instrucciones_entrega') ?? $detalleOrden->instrucciones_entrega;
            $detalleOrden->descripcion = $request->input('descripcion') ?? $detalleOrden->descripcion;
            $detalleOrden->precio = $request->input('precio');
            $detalleOrden->fecha_ingreso = $request->input('fecha_ingreso');
            $detalleOrden->fecha_entrega = $request->input('fecha_entrega');
            $detalleOrden->direccion_registrada = json_encode(Direcciones::find($detalleOrden->id_direccion_entrega));

            // Guardar cambios
            $detalleOrden->save();

            // Confirmar la transacción
            DB::commit();

            return response()->json(['message' => 'Detalle de orden actualizado con éxito'], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Rollback en caso de error
            DB::rollBack();

            return response()->json(
                [
                    'message' => 'Error al actualizar el detalle de la orden',
                    'error' => $e->getMessage(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    // funcion para crear un nuevo detalle de una orden existente.
    public function createOrderDetailByOrdenId($id_orden, $numero_seguimiento, Request $detalle)
    {
        DB::beginTransaction(); // Iniciar una transacción para que todo sea atómico

        try {
            // Generar el QR Code
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

            // Guardar el QR Code en S3
            $filename = $uuid . '.png';
            $path = 'qr_codes/' . $filename;
            Storage::disk('s3')->put($path, $result->getString());

            $bucketName = env('AWS_BUCKET');
            $region = env('AWS_DEFAULT_REGION');
            $qrCodeUrl = "https://{$bucketName}.s3.{$region}.amazonaws.com/{$path}";
            $tag = $qrCodeUrl;

            // Crear el paquete
            $paquete = new Paquete();
            $paquete->id_tipo_paquete = $detalle["id_tipo_paquete"];
            $paquete->id_tamano_paquete = $detalle["id_tamano_paquete"];
            $paquete->id_ubicacion = null; // Inicialmente nulo
            $paquete->id_empaque = $detalle["id_empaque"];
            $paquete->peso = $detalle["peso"];
            $paquete->uuid = $uuid;
            $paquete->tag = $tag;
            $paquete->id_estado_paquete = $detalle["id_estado_paquete"];
            $paquete->fecha_envio = Carbon::parse($detalle["fecha_envio"]);
            $paquete->fecha_entrega_estimada = Carbon::parse($detalle["fecha_entrega_estimada"]);
            $paquete->descripcion_contenido = $detalle["descripcion_contenido"];
            $paquete->save();

            // Si el paquete se crea correctamente
            if ($paquete) {
                // Crear el detalle de la orden
                $detalleOrden = new DetalleOrden();
                $detalleOrden->id_orden = $id_orden;
                $detalleOrden->id_tipo_entrega = $detalle["id_tipo_entrega"];
                $detalleOrden->id_estado_paquetes = $detalle["id_estado_paquete"];
                $detalleOrden->id_paquete = $paquete->id;
                $detalleOrden->validacion_entrega = 0;
                $detalleOrden->instrucciones_entrega = $detalle['instrucciones_entrega'] ?? null;
                $detalleOrden->descripcion = $detalle['descripcion'] ?? null;
                $detalleOrden->precio = $detalle['precio'];
                $detalleOrden->fecha_ingreso = Carbon::now();
                $detalleOrden->fecha_entrega = Carbon::parse($detalle['fecha_entrega']);
                $detalleOrden->id_direccion_entrega = $detalle['id_direccion'];
                $detalleOrden->direccion_registrada = json_encode(Direcciones::find($detalle['id_direccion']));
                $detalleOrden->save();

                // Registrar el movimiento en el Kardex
                $kardex = new Kardex();
                $kardex->id_paquete = $paquete->id;
                $kardex->id_orden = $id_orden;
                $kardex->cantidad = 1;
                $kardex->numero_ingreso = $numero_seguimiento;
                $kardex->tipo_movimiento = 'ENTRADA';
                $kardex->tipo_transaccion = 'ORDEN';
                $kardex->fecha = Carbon::now();
                $kardex->save();

                // Registrar en Inventario
                $inventario = new Inventario();
                $inventario->id_paquete = $paquete->id;
                $inventario->numero_ingreso = $numero_seguimiento;
                $inventario->cantidad = 1;
                $inventario->fecha_entrada = Carbon::now();
                $inventario->estado = 1;
                $inventario->save();

                DB::commit(); // Confirmar la transacción si todo va bien
                return response()->json(['message' => 'Detalle de la orden creado con éxito.'], 201);
            } else {
                throw new \Exception('Error al generar el paquete.');
            }
        } catch (\Exception $e) {
            DB::rollBack(); // Revertir la transacción si ocurre algún error
            return response()->json(['message' => 'Error', 'error' => $e->getMessage()], 500);
        }
    }
}

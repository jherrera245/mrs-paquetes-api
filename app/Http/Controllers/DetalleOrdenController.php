<?php

namespace App\Http\Controllers;
use App\Models\DetalleOrden;
use App\Models\Inventario;
use App\Models\Orden;
use Illuminate\Http\Request;
use App\Services\KardexService;
use App\Models\Paquete;
use Exception;
use Illuminate\Support\Facades\DB;

class DetalleOrdenController extends Controller
{

    public function filter(Request $request)
    {
        $idOrden = $request->input('id_orden');
        $idPaquete = $request->input('id_paquete');

        $detalleOrden = DetalleOrden::filterByOrderAndPackage($idOrden, $idPaquete)->get();

        return response()->json($detalleOrden);
    }

    public function store(Request $request)
    {
        // Validar y crear un nuevo detalle de orden
        $validatedData = $request->validate([
            'id_orden' => 'required|integer|exists:ordenes,id', // Asegurarse de que exista una orden agregada
            'id_paquete' => 'required|integer|exists:paquetes,id', // Asegurarse que el id del paquete exista
            'descripcion' => 'string',
            'total_pago' => 'required|numeric',
        ]);

        $detalleOrden = DetalleOrden::create($validatedData);
        return response()->json($detalleOrden, 201);
    }

    //Se encarga de mostrar un detalle de orden por medio del ID
    public function show($id)
    {
        // Obtener un detalle de orden específico
        $detalleOrden = DetalleOrden::findOrFail($id);
        return response()->json($detalleOrden);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //valida los datos
        $request->validate([
            'id_orden' => 'required|integer|exists:ordenes,id',
            'id_paquete' => 'required|integer|exists:paquetes,id',
            'id_tipo_entrega' => 'required|integer|exists:tipo_entrega,id',
            'id_estado_paquetes' => 'required|integer|exists:estado_paquetes,id',
            'id_direccion_entrega' => 'required|integer|exists:direcciones,id',
            'instrucciones_entrega' => 'nullable|string',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric',
            'fecha_entrega' => 'required|date',
        ]);

        // Buscar el registro por su ID
        $detalleOrden = DetalleOrden::find($id);

        // Verificar si el registro existe
        if (!$detalleOrden) {
            return response()->json(['message' => 'Registro no encontrado'], 404);
        }

        // Actualizar el registro con los datos de la solicitud
        $detalleOrden->update($request->all());

        // Devolver una respuesta exitosa
        return response()->json(['message' => 'Registro actualizado exitosamente', 'data' => $detalleOrden], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Eliminar un detalle de orden específico
        $detalleOrden = DetalleOrden::findOrFail($id);
        $detalleOrden->delete();

        return response()->json(null, 204);
    }

    public function detalles_orden(Request $request)
    {
       // Obtener los filtros de la solicitud
       $filters = $request->only([
        'id', 'id_orden', 'id_paquete', 'id_tipo_entrega', 'id_estado_paquetes', 'id_direccion_entrega', 'validacion_entrega',
        'instrucciones_entrega', 'descripcion', 'precio', 'fecha_ingreso', 'fecha_entrega'
    ]);

    // Aplicar los filtros a la consulta y cargar las relaciones
    $query = DetalleOrden::filtrarDetalleOrden($filters);

    // Obtener los datos con las relaciones
    $detalleOrden = $query->get()->map(function ($item) {
        return
        [
        'id'=> $item->id,
        'orden' => $item->orden->id,
        'paquete' =>  $item->paquete->tipoPaquete->nombre,
        'tipoEntrega' => $item->tipoEntrega->entrega,
        'estadoEntrega' => $item->estadoEntrega->nombre,
        'clienteEntrega' => $item->clienteEntrega->nombre.' '.$item->clienteEntrega->apellido,
        'direccionEntrega' =>   $item->clienteEntrega->direccion.' '.
                                $item->departamentoEntrega->nombre. ' '.
                                $item->municipioEntrega->nombre,
        'validacion_entrega' => $item->validacion_entrega,
        'instrucciones_entrega' => $item->instrucciones_entrega,
        'descripcion' => $item->descripcion,
        'precio' => $item->precio,
        'fecha_ingreso' => $item->fecha_ingreso,
        'fecha_entrega' => $item->fecha_entrega,
        ];
    });

        $data = [
            'detalleorden' => $detalleOrden,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function detalles_orden_id($id)
    {
        // Buscar el detalle de orden por ID
        $detalleOrden = DetalleOrden::with([
            'orden',
            'paquete',
            'tipoEntrega',
            'estadoEntrega',
            'clienteEntrega',
            'direccionEntrega',
        ])->find($id);

        // Verificar si se encontró el detalle de orden
        if (!$detalleOrden) {
            return response()->json(['message' => 'Detalle de orden no encontrado'], 404);
        }

        // Transformar los datos para excluir los campos no deseados
        $detalleOrdenData = [
            'id'=> $detalleOrden->id,
            'orden' => $detalleOrden->orden?
                [
                'id' => $detalleOrden->orden->id,
                'nombre' => $detalleOrden->clienteEntrega->nombre.' '.$detalleOrden->clienteEntrega->apellido,
                'direccion' => $detalleOrden->clienteEntrega->direccion.' '.
                $detalleOrden->departamentoEntrega->nombre. ' '. $detalleOrden->municipioEntrega->nombre,
                'pago' => $detalleOrden->orden->tipoPago->pago,
                'total' => $detalleOrden->orden->total_pagar,
                'costo adicional' => $detalleOrden->orden->costo_adicional,
                'descripcion' => $detalleOrden->orden->concepto
                ] : null,
            'paquete' => $detalleOrden->paquete?
                [
                'id' => $detalleOrden->paquete->id,
                'tipo paquete'=> $detalleOrden->paquete->tipoPaquete->nombre,
                'empaquetado'=> $detalleOrden->paquete->empaquetado->empaquetado,
                'peso' => $detalleOrden->paquete->peso,
                'uuid' => $detalleOrden->paquete->uuid,
                'tag' => $detalleOrden->paquete->tag,
                'estado paquete' => $detalleOrden->estadoEntrega->nombre,
                'fecha envio' => $detalleOrden->paquete->fecha_envio,
                'fecha entrega estimada' => $detalleOrden->paquete->fecha_entrega_estimada,
                'descripcion' => $detalleOrden->paquete->descripcion_contenido
                ] : null,
            'tipoEntrega' => $detalleOrden->tipoEntrega ?
                [
                'id' => $detalleOrden->tipoEntrega->id,
                'entrega' => $detalleOrden->tipoEntrega->entrega
                ] : null,
            'estadoEntrega' => $detalleOrden->estadoEntrega ?
                [
                'id' => $detalleOrden->estadoEntrega->id,
                'estado' => $detalleOrden->estadoEntrega->nombre,
                ] : null,
            'clienteEntrega' => $detalleOrden->clienteEntrega?
                [
                'id' => $detalleOrden->clienteEntrega->id,
                'nombre' => $detalleOrden->clienteEntrega->nombre.' '.$detalleOrden->clienteEntrega->apellido,
                'dui' => $detalleOrden->clienteEntrega->dui,
                'telefono' => $detalleOrden->clienteEntrega->telefono,
                'direccion' => $detalleOrden->clienteEntrega->direccion.' '.
                $detalleOrden->departamentoEntrega->nombre. ' '. $detalleOrden->municipioEntrega->nombre,
                ] : null,
            'direccionEntrega' => $detalleOrden->direccionEntrega?
                [
                'id' => $detalleOrden->direccionEntrega->id,
                'nombre' => $detalleOrden->clienteEntrega->nombre.' '.$detalleOrden->clienteEntrega->apellido,
                'contacto' => $detalleOrden->direccionEntrega->nombre_contacto,
                'telefono' => $detalleOrden->direccionEntrega->telefono,
                'id_departamento' => $detalleOrden->departamentoEntrega->nombre,
                'id_municipio' => $detalleOrden->municipioEntrega->nombre,
                'direccion' => $detalleOrden->clienteEntrega->direccion,
                'referencia' => $detalleOrden->direccionEntrega->referencia
                ] : null,
            'validacion_entrega' => $detalleOrden->validacion_entrega,
            'instrucciones_entrega' => $detalleOrden->instrucciones_entrega,
            'descripcion' => $detalleOrden->descripcion,
            'precio' => $detalleOrden->precio,
            'fecha_ingreso' => $detalleOrden->fecha_ingreso,
            'fecha_entrega' => $detalleOrden->fecha_entrega,
        ];

        // Formar la respuesta JSON
        $data = [
            'detalleorden' => $detalleOrdenData,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function validacionEntrega(Request $request)
    {
        // Validar la imagen y el ID de la orden
        $request->validate([
            'uuid' => 'required|exists:paquetes,uuid',
            'validacion_entrega' => 'required|image|mimes:jpeg,png,jpg,gif|max:20480'
        ]);

        // Capturar los datos de la solicitud
        $uuid = $request->input('uuid');
        $imageFile = $request->file('validacion_entrega');

        DB::beginTransaction();

        try {
        // Obtener el paquete usando el UUID
        $paquete = Paquete::where('uuid', $uuid)->firstOrFail();

        // Comprobar si el estado del paquete ya es 8
        if ($paquete->id_estado_paquete == 8) {
            return response()->json(['error' => 'El paquete ya fue validado.'], 400);
        }

        // Obtener el detalle de la orden
        $detalleOrden = DetalleOrden::where('id_paquete', $paquete->id)->firstOrFail();


            // Guardar la imagen en S3
            $filename = 'entrega_' . $uuid . '_' . time() . '.' . $imageFile->getClientOriginalExtension();
            $path = $imageFile->storeAs('validacion_entregas', $filename, 's3');
            $bucketName = env('AWS_BUCKET');
            $region = env('AWS_DEFAULT_REGION');
            $imageUrl = "https://{$bucketName}.s3.{$region}.amazonaws.com/{$path}";

            // Actualizar el campo validacion_entrega en el detalle de la orden
            $detalleOrden->validacion_entrega = $imageUrl;

            // actualizar el estado del detalle de la orden.
            $detalleOrden->id_estado_paquetes = 8;
            $detalleOrden->save();

            // en PAQUETES actualizamos el estado tambien.
            $paquete = Paquete::findOrFail($detalleOrden->id_paquete);
            $paquete->id_estado_paquete = 8;
            $paquete->save();

            // Obtener el número de seguimiento y la info de la orden
            $kardexService = new KardexService();
            $detalleOrdenInfo = $kardexService->getOrdenInfo($detalleOrden->id_paquete);

            if (!$detalleOrdenInfo) {
                throw new Exception("No se encontró información de la orden para el paquete ID: {$detalleOrden->id_paquete}");
            }

            $idOrden = $detalleOrdenInfo->id_orden;
            $numeroSeguimiento = $detalleOrdenInfo->numero_seguimiento;

            // Registrar en Kardex (SALIDA por TRASLADO)
            $kardexService->registrarMovimientoKardex($detalleOrden->id_paquete, $idOrden, 'SALIDA', 'EN_VEHICULO_ENTREGA', $numeroSeguimiento);

            // **Registrar en Kardex (ENTRADA por ENTREGADO)**
            $kardexService->registrarMovimientoKardex($detalleOrden->id_paquete, $idOrden, 'ENTRADA', 'ENTREGADO', $numeroSeguimiento);

            // Actualizar Inventario
            $inventario = Inventario::where('id_paquete', $detalleOrden->id_paquete)->first();
            if ($inventario) {
                $inventario->cantidad = 0;  // Marcar como sin stock
                // enviamos la fecha_salida
                $inventario->fecha_salida = now();
                $inventario->save();
            }

            DB::commit();

            return response()->json(['message' => 'Validación de entrega enviada con éxito', 'data' => $detalleOrden], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error en el proceso: ' . $e->getMessage()], 500);
        }
    }

    public function finalizarOrden(Request $request)
    {
        // Validar que el número de seguimiento esté presente
        $request->validate([
            'numero_seguimiento' => 'required|string',
        ]);

        $numero_seguimiento = $request->input('numero_seguimiento');

        // Verificar si la orden existe por número de seguimiento
        $orden = Orden::where('numero_seguimiento', $numero_seguimiento)->first();
        
        if (!$orden) {
            return response()->json(['message' => 'Orden no encontrada.'], 404);
        }

        // Validar si el estado de pago es "pendiente"
        if ($orden->estado_pago === 'pendiente') {
            return response()->json(['message' => 'No se puede finalizar la orden, el estado de pago es pendiente.'], 400);
        }

        // Validar si la orden ya ha sido finalizada
        if ($orden->finished === 1) {
            return response()->json(['message' => 'La orden ya ha sido finalizada.'], 400);
        }

        // Iniciar una transacción
        DB::beginTransaction();

        try {
            $entregados = DetalleOrden::where('id_orden', $orden->id)
            ->where('id_estado_paquetes', 8)
            ->join('paquetes', 'detalle_orden.id_paquete', '=', 'paquetes.id')
            ->join('estado_paquetes', 'detalle_orden.id_estado_paquetes', '=', 'estado_paquetes.id') 
            ->select('detalle_orden.*', 'paquetes.uuid', 'estado_paquetes.nombre as estado') 
            ->get();

            // Validar que al menos un paquete ha sido entregado
            if ($entregados->isEmpty()) {
                return response()->json(['message' => 'No se puede finalizar la orden, ningún paquete ha sido entregado.'], 400);
            }

            // Actualizar el estado de la orden a "completado" y el campo finished a 1
            $orden->estado = 'Completada';
            $orden->finished = 1; 
            $orden->save();

            // Commit de la transacción
            DB::commit();

            // Preparar los datos de respuesta
            $response = [
                'message' => 'Orden finalizada exitosamente.',
                'orden' => [
                    'id' => $orden->id,
                    'numero_seguimiento' => $orden->numero_seguimiento,
                    'estado' => $orden->estado,
                    'finished' => $orden->finished,
                    'total_pagar' => $orden->total_pagar,
                    'costo_adicional' => $orden->costo_adicional,
                    'fecha_entrega' => $orden->updated_at, 
                    'paquetes_entregados' => $entregados->map(function ($paquete) {
                        return [
                            'id' => $paquete->id,
                            'uuid' => $paquete->uuid, 
                            'descripcion' => $paquete->descripcion,
                            'precio' => $paquete->precio,
                            'estado' => $paquete->estado,
                            'fecha_entrega' => $paquete->fecha_entrega, 
                        ];
                    }),
                ]
            ];

        return response()->json($response, 200);
        }
        catch (\Exception $e) {
        // Si hay algún error, revertir la transacción

        DB::rollBack();

        return response()->json(['message' => 'Error al finalizar la orden: ' . $e->getMessage()], 500);
        
        }
    }
}
<?php

namespace App\Http\Controllers;
use App\Models\DetalleOrden;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Validator;

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
        //id de la orden e imagen a enviar
    $request->validate([
        'id' => 'required|exists:detalle_orden,id',
        'validacion_entrega' => 'required|image|mimes:jpeg,png,jpg,gif|max:20480' 
    ]);

        //datos de entrada para el endpoint
    $id = $request->input('id');
    $imageFile = $request->file('validacion_entrega');

        try {
            $filename = time() . '.' . $imageFile->getClientOriginalExtension();

            // guardar la imagen en s3
            $path = $imageFile->storeAs('validacion_entregas', $filename, 's3');
            $bucketName = env('AWS_BUCKET');
            $region = env('AWS_DEFAULT_REGION');
            $imageUrl = "https://{$bucketName}.s3.{$region}.amazonaws.com/{$path}";

            // actualiza el campo de la base de datos con el id de la orden para agregar el url a validacion_entrega
            $detalleOrden = DetalleOrden::findOrFail($id);
            $detalleOrden->validacion_entrega = $imageUrl;
            $detalleOrden->save();
            
            return response()->json(['message' => 'Validación de entrega enviada con éxito', 'data' => $detalleOrden], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error uploading image: ' . $e->getMessage()], 500);
        }
    }
}

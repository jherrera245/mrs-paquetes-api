<?php

namespace App\Http\Controllers;

use App\Models\DetalleOrden;
use App\Models\Incidencia;
use App\Models\EstadoIncidencia;
use App\Models\Kardex;
use App\Models\Orden;
use App\Models\Paquete;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IncidenciaController extends Controller
{
    private $estadosIncidencia;

    public function __construct()
    {
        // Cargar los estados de incidencia al inicializar el controlador
        $this->estadosIncidencia = EstadoIncidencia::pluck('estado', 'id')->toArray();
    }

    public function index(Request $request)
    {
        // Definir el número de elementos por página con un valor predeterminado de 10
        $perPage = $request->input('per_page', 10);

        // Obtener los filtros de búsqueda desde la solicitud
        $filters = $request->only([
            'tipo_incidencia',
            'paquete',
            'usuario_reporta',
            'usuario_asignado',
            'estado',
            'fecha_hora',
            'palabra_clave'
        ]);

        // Filtrar las incidencias utilizando el método search del modelo Incidencia
        $incidencias = Incidencia::search($filters)->paginate($perPage);

        // Verificar si no se encontraron incidencias
        if ($incidencias->isEmpty()) {
            return response()->json(['message' => 'No se encontraron incidencias'], 404);
        }

        // Transformar los datos para incluir nombres en lugar de IDs y mejorar el UUID del paquete
        $incidencias->getCollection()->transform(function ($incidencia) {
            return $this->transformIncidencia($incidencia);
        });

        // Devolver una respuesta JSON con las incidencias transformadas
        return response()->json($incidencias);
    }

    public function show($id)
    {
        try {
            $incidencia = Incidencia::with([
                'tipoIncidencia',
                'paquete',
                'usuarioReporta.empleado',
                'usuarioAsignado.empleado'
            ])->findOrFail($id);

            // Transformar los datos antes de devolver la respuesta
            $incidenciaTransformed = $this->transformIncidencia($incidencia);

            return response()->json($incidenciaTransformed);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Incidencia no encontrada', 'message' => $e->getMessage()], 404);
        }
    }

    public function store(Request $request)
    {
    // Validar los datos de entrada
    $validator = Validator::make($request->all(), [
        'id_paquete' => 'required|exists:paquetes,id',
        'fecha_hora' => 'required|date',
        'id_tipo_incidencia' => 'required|exists:tipo_incidencia,id',
        'descripcion' => 'required|string|max:1000',
        'estado' => 'required|exists:estado_incidencias,id',
        'id_usuario_asignado' => 'nullable|exists:users,id',
        'solucion' => 'nullable|string|max:1000',
        'fecha_resolucion' => 'nullable|date',
    ]);

    // Si la validación falla, devolver errores de validación
    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()->all()], 400);
    }

    // Iniciar una transacción
    DB::beginTransaction();

    try {
        // Obtener el usuario autenticado
        $usuarioReporta = Auth::user();

        // Crear la incidencia con los datos validados
        $incidenciaData = $validator->validated();
        $incidenciaData['id_usuario_reporta'] = $usuarioReporta->id; // Asignar el usuario autenticado como reportador

        $incidencia = Incidencia::create($incidenciaData);

        // Obtener el paquete y el detalle de la orden
        $paquete = $incidencia->paquete;
        $detalleOrden = DetalleOrden::where('id_paquete', $incidenciaData['id_paquete'])->firstOrFail();
        $id_orden = $detalleOrden->id_orden;

        // Obtener el numero_seguimiento basado en id_orden
        $numero_seguimiento = Orden::where('id', $id_orden)->value('numero_seguimiento');

        // Verificar si ya existe un movimiento de SALIDA para el paquete
        $existeKardexSalida = Kardex::where('id_paquete', $incidenciaData['id_paquete'])
            ->where('tipo_movimiento', 'SALIDA')
            ->exists();

        if ($existeKardexSalida) {
            // Si ya existe un movimiento de SALIDA, devolver un error y revertir la transacción
            DB::rollBack();
            return response()->json(['message' => 'Ya existe un registro de SALIDA para este paquete.'], 400);
        }

        // Obtener el tipo_transaccion del Kardex para el paquete
        $tipoTransaccion = Kardex::where('id_paquete', $incidenciaData['id_paquete'])->value('tipo_transaccion');

        // Verificar el tipo de incidencia y actualizar el estado del paquete
        if ($incidencia->id_tipo_incidencia == 2) { // 2 es "Daño"
            $paquete->id_estado_paquete = 11; // 11 es "Dañado"
            $paquete->id_ubicacion = 100; // Ubicación específica para "Dañado"

            // Crear registros en Kardex basados en el tipo de transacción
            if ($tipoTransaccion == 'RECEPCION') {
                Kardex::create([
                    'id_paquete' => $incidenciaData['id_paquete'],
                    'id_orden' => $id_orden,
                    'cantidad' => 1,
                    'numero_ingreso' => $numero_seguimiento,
                    'tipo_movimiento' => 'SALIDA',
                    'tipo_transaccion' => 'RECEPCION',
                    'fecha' => Carbon::now(),
                ]);

                Kardex::create([
                    'id_paquete' => $incidenciaData['id_paquete'],
                    'id_orden' => $id_orden,
                    'cantidad' => 1,
                    'numero_ingreso' => $numero_seguimiento,
                    'tipo_movimiento' => 'ENTRADA',
                    'tipo_transaccion' => 'PAQUETE_DAÑADO',
                    'fecha' => Carbon::now(),
                ]);
            } else if ($tipoTransaccion == 'ALMACENADO') {
                Kardex::create([
                    'id_paquete' => $incidenciaData['id_paquete'],
                    'id_orden' => $id_orden,
                    'cantidad' => 1,
                    'numero_ingreso' => $numero_seguimiento,
                    'tipo_movimiento' => 'SALIDA',
                    'tipo_transaccion' => 'ALMACENADO',
                    'fecha' => Carbon::now(),
                ]);

                Kardex::create([
                    'id_paquete' => $incidenciaData['id_paquete'],
                    'id_orden' => $id_orden,
                    'cantidad' => 1,
                    'numero_ingreso' => $numero_seguimiento,
                    'tipo_movimiento' => 'ENTRADA',
                    'tipo_transaccion' => 'PAQUETE_DAÑADO',
                    'fecha' => Carbon::now(),
                ]);
            } else {
                // Revertir la transacción en caso de error
                DB::rollBack();
                throw new \Exception('El paquete no se encuentra en el tipo de transacción correcta para realizar este proceso.');
            }

        } else if ($incidencia->id_tipo_incidencia == 3) { // 3 es "Pérdida"
            $paquete->id_estado_paquete = 12; // 12 es "Perdido"
        }

        // Guardar el cambio de estado del paquete
        $paquete->save();

        // Confirmar la transacción
        DB::commit();

        // Cargar las relaciones necesarias
        $incidencia->load(['tipoIncidencia', 'paquete', 'usuarioReporta', 'usuarioAsignado']);

        // Transformar los datos antes de devolver la respuesta
        $data = $this->transformIncidencia($incidencia);

        // Devolver una respuesta JSON con los datos transformados y un código de estado 201 (Created)
        return response()->json(['message' => 'Incidencia creada', 'incidencia' => $data], 201);

        } catch (\Exception $e) {
            // Revertir la transacción en caso de excepción
            DB::rollBack();
            // Capturar cualquier excepción y devolver un mensaje de error
            return response()->json(['error' => 'Error al crear la incidencia', 'message' => $e->getMessage()], 500);
        }
    }



    public function update(Request $request, $id)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'id_paquete' => 'sometimes|required|exists:paquetes,id',
            'fecha_hora' => 'sometimes|required|date',
            'id_tipo_incidencia' => 'sometimes|required|exists:tipo_incidencia,id',
            'descripcion' => 'sometimes|required|string|max:1000',
            'estado' => 'sometimes|required|exists:estado_incidencias,id',
            'id_usuario_asignado' => 'nullable|exists:users,id',
            'solucion' => 'nullable|string|max:1000',
            'fecha_resolucion' => 'nullable|date',
        ]);

        // Si la validación falla, devolver errores de validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        try {
            // Buscar la incidencia por su ID
            $incidencia = Incidencia::findOrFail($id);

            // Actualizar la incidencia con los datos validados
            $incidencia->update($validator->validated());

            // Actualizar el estado del paquete según el tipo de incidencia
            $paquete = $incidencia->paquete;

            if ($request->id_tipo_incidencia == 2) { // Daño
                $paquete->id_estado = 11; // Dañado
            } elseif ($request->id_tipo_incidencia == 3) { // Perdido
                $paquete->id_estado = 12; // Perdido
            }

            // Guardar el cambio de estado del paquete
            $paquete->save();

            // Cargar las relaciones necesarias después de la actualización
            $incidencia->load(['tipoIncidencia', 'paquete', 'usuarioReporta', 'usuarioAsignado']);

            // Transformar los datos antes de devolver la respuesta
            $data = $this->transformIncidencia($incidencia);

            // Devolver una respuesta JSON con los datos transformados y un mensaje de éxito
            return response()->json(['message' => 'Incidencia actualizada', 'incidencia' => $data]);
        } catch (\Exception $e) {
            // Capturar cualquier excepción y devolver un mensaje de error
            return response()->json(['error' => 'Error al actualizar la incidencia', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
    // Iniciar una transacción
    DB::beginTransaction();

    try {
        // Buscar la incidencia a eliminar
        $incidencia = Incidencia::findOrFail($id);
        $id_paquete = $incidencia->id_paquete;
        
        // Obtener el paquete y su estado actual
        $paquete = Paquete::findOrFail($id_paquete);
        $estadoActual = $paquete->id_estado_paquete;

        // Determinar el tipo_transaccion basado en el id_paquete
        $kardexRecords = Kardex::where('id_paquete', $id_paquete)
            ->whereIn('tipo_transaccion', ['RECEPCION', 'ALMACENADO'])
            ->get();

        if ($kardexRecords->isEmpty()) {
            throw new \Exception('No se encontraron registros de Kardex para revertir.');
        }

        // Revertir los registros en Kardex y restaurar el estado del paquete
        foreach ($kardexRecords as $record) {
            if ($record->tipo_transaccion == 'RECEPCION') {
                // Revertir los registros de Kardex relacionados con RECEPCION
                Kardex::where('id_paquete', $id_paquete)
                    ->where('tipo_movimiento', 'SALIDA')
                    ->where('tipo_transaccion', 'RECEPCION')
                    ->delete();

                Kardex::where('id_paquete', $id_paquete)
                    ->where('tipo_movimiento', 'ENTRADA')
                    ->where('tipo_transaccion', 'PAQUETE_DAÑADO')
                    ->delete();

                // Restaurar el estado anterior del paquete para RECEPCION
                $paquete->id_estado_paquete = 1; // 1 es el estado original para RECEPCION
            } else if ($record->tipo_transaccion == 'ALMACENADO') {
                // Revertir los registros de Kardex relacionados con ALMACENADO
                Kardex::where('id_paquete', $id_paquete)
                    ->where('tipo_movimiento', 'SALIDA')
                    ->where('tipo_transaccion', 'ALMACENADO')
                    ->delete();

                Kardex::where('id_paquete', $id_paquete)
                    ->where('tipo_movimiento', 'ENTRADA')
                    ->where('tipo_transaccion', 'PAQUETE_DAÑADO')
                    ->delete();

                // Restaurar el estado anterior del paquete para ALMACENADO
                $paquete->id_estado_paquete = 3; // 3 es el estado original para ALMACENADO
            }
        }

        // Limpiar el campo id_ubicacion
        $paquete->id_ubicacion = null;

        // Guardar los cambios en el estado del paquete
        $paquete->save();

        // Eliminar la incidencia
        $incidencia->delete();

        // Confirmar la transacción
        DB::commit();

        // Devolver una respuesta JSON con un mensaje de éxito
        return response()->json(['message' => 'Incidencia eliminada y registros de Kardex revertidos'], 200);

    } catch (\Exception $e) {
        // Revertir la transacción en caso de excepción
        DB::rollBack();
        // Capturar cualquier excepción y devolver un mensaje de error
        return response()->json(['error' => 'Error al eliminar la incidencia', 'message' => $e->getMessage()], 500);
    }
}


    // Función de transformación de incidencia
    private function transformIncidencia($incidencia)
    {
        $usuarioReporta = $incidencia->usuarioReporta;
        $usuarioAsignado = $incidencia->usuarioAsignado;

        // Obtener el empleado que reporta la incidencia
        $empleadoReporta = $usuarioReporta ? $usuarioReporta->empleado : null;
        $empleadoAsignado = $usuarioAsignado ? $usuarioAsignado->empleado : null;

        return [
            'id' => $incidencia->id,
            'id_paquete' => $incidencia->paquete ? $incidencia->paquete->id : null,
            'paquete_descripcion' => $incidencia->paquete ? $incidencia->paquete->descripcion_contenido : null,
            'fecha_hora' => $incidencia->fecha_hora,
            'tipo_incidencia' => $incidencia->tipoIncidencia ? $incidencia->tipoIncidencia->nombre : null,
            'descripcion' => $incidencia->descripcion,
            'estado' => $this->estadosIncidencia[$incidencia->estado] ?? 'Desconocido',
            'fecha_resolucion' => $incidencia->fecha_resolucion,
            'id_usuario_reporta' => $usuarioReporta ? $usuarioReporta->id_empleado : null, // Mostrar id_empleado del usuario que reporta
            'usuario_reporta' => $empleadoReporta ? $empleadoReporta->nombres . ' ' . $empleadoReporta->apellidos : null, // Mostrar nombres y apellidos del empleado que reporta
            'id_usuario_asignado' => $usuarioAsignado ? $usuarioAsignado->id_empleado : null, // Mostrar id_empleado del usuario asignado
            'usuario_asignado' => $empleadoAsignado ? $empleadoAsignado->nombres . ' ' . $empleadoAsignado->apellidos : null, // Mostrar nombres y apellidos del empleado asignado
            'solucion' => $incidencia->solucion,
            'created_at' => $incidencia->created_at,
            'updated_at' => $incidencia->updated_at,
        ];
    }
}

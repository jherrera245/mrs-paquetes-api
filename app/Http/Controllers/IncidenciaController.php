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
use App\Models\AsignacionRutas;

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

        try {
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
        } catch (\Exception $e) {
            // Manejar cualquier excepción y devolver un mensaje de error
            return response()->json(['error' => 'Error al obtener las incidencias', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            // Obtener la incidencia con las relaciones necesarias
            $incidencia = Incidencia::with([
                'tipoIncidencia',
                'paquete',
                'usuarioReporta.empleado',
                'usuarioAsignado.empleado'
            ])->findOrFail($id);

            // Transformar los datos antes de devolver la respuesta
            $incidenciaTransformed = $this->transformIncidencia($incidencia);

            return response()->json($incidenciaTransformed);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si no se encuentra la incidencia, devolver un mensaje de error
            return response()->json(['error' => 'Incidencia no encontrada'], 404);
        } catch (\Exception $e) {
            // Manejar cualquier otro tipo de excepción
            return response()->json(['error' => 'Error al obtener la incidencia', 'message' => $e->getMessage()], 500);
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
            // Obtener el ID del usuario autenticado
            $usuarioReporta = Auth::user(); // Recuperar el usuario autenticado

            if (!$usuarioReporta) {
                throw new \Exception('No se puede determinar el usuario que reporta.');
            }

            // Crear la incidencia con los datos validados
            $incidenciaData = $validator->validated();
            $incidenciaData['id_usuario_reporta'] = $usuarioReporta->id; // Asignar el ID del usuario autenticado

            $incidencia = Incidencia::create($incidenciaData);

            // Obtener el paquete y el detalle de la orden
            $paquete = $incidencia->paquete;
            $detalleOrden = DetalleOrden::where('id_paquete', $incidenciaData['id_paquete'])->firstOrFail();
            $id_orden = $detalleOrden->id_orden;

            // Obtener el numero_seguimiento basado en id_orden
            $numero_seguimiento = Orden::where('id', $id_orden)->value('numero_seguimiento');

            // Verificar si ya existe un movimiento de SALIDA para el paquete, pero solo si la incidencia NO es de tipo "Daño" o "Pérdida"
            $existeKardexSalida = Kardex::where('id_paquete', $incidenciaData['id_paquete'])
                ->where('tipo_movimiento', 'SALIDA')
                ->exists();

            if ($existeKardexSalida && $incidencia->id_tipo_incidencia != 2 && $incidencia->id_tipo_incidencia != 3) {
                // Si no es una incidencia de daño o pérdida, no permitir crear el reporte
                DB::rollBack();
                return response()->json(['message' => 'Ya existe un registro de SALIDA para este paquete.'], 400);
            }

            // Verificar el tipo de incidencia y actualizar el estado del paquete solo si es necesario
            if ($incidencia->id_tipo_incidencia == 2) { // 2 es "Daño"
                $paquete->id_estado_paquete = 11; // 11 es "Dañado"
                //$paquete->id_ubicacion = 100; // Ubicación específica para "Dañado"

                // Registrar los movimientos en Kardex independientemente del estado de la transacción previa
                Kardex::create([
                    'id_paquete' => $incidenciaData['id_paquete'],
                    'id_orden' => $id_orden,
                    'cantidad' => 1,
                    'numero_ingreso' => $numero_seguimiento,
                    'tipo_movimiento' => 'ENTRADA',
                    'tipo_transaccion' => 'PAQUETE_DAÑADO',
                    'fecha' => Carbon::now(),
                ]);
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

            // Obtener el paquete asociado a la incidencia
            $paquete = $incidencia->paquete;

            if (!$paquete) {
                throw new \Exception('El paquete asociado a esta incidencia no existe.');
            }

            // Actualizar la incidencia con los datos validados
            $incidencia->update($validator->validated());

            // Actualizar el estado del paquete según el tipo de incidencia
            if ($request->filled('id_tipo_incidencia')) {
                if ($request->id_tipo_incidencia == 2) { // Daño
                    $paquete->id_estado_paquete = 11; // Dañado
                    $paquete->id_ubicacion = null; // Ubicación específica para "Dañado"

                    // Registrar el movimiento de "PAQUETE_DAÑADO" en Kardex si no se había registrado antes
                    Kardex::create([
                        'id_paquete' => $paquete->id,
                        'id_orden' => $paquete->orden_id,
                        'cantidad' => 1,
                        'numero_ingreso' => $paquete->numero_ingreso,
                        'tipo_movimiento' => 'ENTRADA',
                        'tipo_transaccion' => 'PAQUETE_DAÑADO',
                        'fecha' => Carbon::now(),
                    ]);
                } elseif ($request->id_tipo_incidencia == 3) { // Perdido
                    $paquete->id_estado_paquete = 12; // Perdido
                }

                // Guardar el cambio de estado del paquete solo si se actualizó
                $paquete->save();
            }

            // Cargar las relaciones necesarias después de la actualización
            $incidencia->load(['tipoIncidencia', 'paquete', 'usuarioReporta', 'usuarioAsignado']);

            // Transformar los datos antes de devolver la respuesta
            $data = $this->transformIncidencia($incidencia);

            // Devolver una respuesta JSON con los datos transformados y un mensaje de éxito
            return response()->json(['message' => 'Incidencia actualizada', 'incidencia' => $data]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Incidencia no encontrada'], 404);
        } catch (\Exception $e) {
            // Capturar cualquier otra excepción y devolver un mensaje de error
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

            // Obtener el paquete asociado a la incidencia
            $paquete = Paquete::findOrFail($id_paquete);

            // Determinar el tipo_transaccion basado en el id_paquete
            $kardexRecords = Kardex::where('id_paquete', $id_paquete)
                ->whereIn('tipo_transaccion', ['RECEPCION', 'ALMACENADO', 'PAQUETE_DAÑADO'])
                ->get();

            if ($kardexRecords->isEmpty()) {
                throw new \Exception('No se encontraron registros de Kardex para revertir.');
            }

            // Revertir los registros en Kardex y restaurar el estado del paquete
            foreach ($kardexRecords as $record) {
                switch ($record->tipo_transaccion) {
                    case 'RECEPCION':
                    case 'ALMACENADO':
                    case 'PAQUETE_DAÑADO':
                        // Revertir los registros de Kardex relacionados
                        Kardex::where('id_paquete', $id_paquete)
                            ->where('tipo_transaccion', $record->tipo_transaccion)
                            ->delete();

                        // Restaurar el estado original del paquete (asumiendo que el estado 1 es el inicial)
                        $paquete->id_estado_paquete = 1; // Estado original para paquetes no dañados
                        break;
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
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['error' => 'Incidencia o paquete no encontrado'], 404);
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

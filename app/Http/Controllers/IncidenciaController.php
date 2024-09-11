<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use App\Models\EstadoIncidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            'tipo_incidencia', 'paquete', 'usuario_reporta', 'usuario_asignado', 'estado', 'fecha_hora', 'palabra_clave'
        ]);

        // Filtrar las incidencias utilizando el método search del modelo Incidencia
        $incidencias = Incidencia::search($filters)->paginate($perPage);

        // Verificar si no se encontraron incidencias
        if ($incidencias->isEmpty()) {
            return response()->json(['message' => 'No se encontraron incidencias'], 404);
        }

        // Transformar los datos para incluir nombres en lugar de IDs y mejorar el UUID del paquete
        $incidencias->getCollection()->transform(function ($incidencia) {
            return $this->transformIncidencia($incidencia); // Asegúrate de que $this esté vinculado correctamente aquí
        });

        // Devolver una respuesta JSON con las incidencias transformadas
        return response()->json($incidencias);
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
            'id_usuario_reporta' => 'required|exists:users,id',
            'id_usuario_asignado' => 'nullable|exists:users,id',
            'solucion' => 'nullable|string|max:1000',
            'fecha_resolucion' => 'nullable|date',
        ]);

        // Si la validación falla, devolver errores de validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        try {
            // Crear la incidencia con los datos validados
            $incidencia = Incidencia::create($validator->validated());

            // Cargar las relaciones necesarias
            $incidencia->load(['tipoIncidencia', 'paquete', 'usuarioReporta', 'usuarioAsignado']);

            // Transformar los datos antes de devolver la respuesta
            $data = $this->transformIncidencia($incidencia);

            // Devolver una respuesta JSON con los datos transformados y un código de estado 201 (Created)
            return response()->json(['message' => 'Incidencia creada', 'incidencia' => $data], 201);
        } catch (\Exception $e) {
            // Capturar cualquier excepción y devolver un mensaje de error
            return response()->json(['error' => 'Error al crear la incidencia', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $incidencia = Incidencia::with(['tipoIncidencia', 'paquete', 'usuarioReporta', 'usuarioAsignado'])
                ->findOrFail($id);

            // Transformar los datos antes de devolver la respuesta
            $incidenciaTransformed = $this->transformIncidencia($incidencia);

            return response()->json($incidenciaTransformed);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Incidencia no encontrada', 'message' => $e->getMessage()], 404);
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
            'id_usuario_reporta' => 'sometimes|required|exists:users,id',
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
        try {
            $incidencia = Incidencia::findOrFail($id);
            $incidencia->delete();

            return response()->json(['message' => 'Incidencia eliminada correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar la incidencia', 'message' => $e->getMessage()], 500);
        }
    }

    // Función de transformación de incidencia
    private function transformIncidencia($incidencia)
    {
        return [
            'id' => $incidencia->id,
            'paquete_uuid' => $incidencia->paquete ? $incidencia->paquete->uuid : null,
            'paquete_descripcion' => $incidencia->paquete ? $incidencia->paquete->descripcion_contenido : null,
            'fecha_hora' => $incidencia->fecha_hora,
            'tipo_incidencia' => $incidencia->tipoIncidencia ? $incidencia->tipoIncidencia->nombre : null,
            'descripcion' => $incidencia->descripcion,
            'estado' => $this->estadosIncidencia[$incidencia->estado] ?? 'Desconocido',
            'fecha_resolucion' => $incidencia->fecha_resolucion,
            'usuario_reporta' => $incidencia->usuarioReporta ? $incidencia->usuarioReporta->email : null,
            'usuario_asignado' => $incidencia->usuarioAsignado ? $incidencia->usuarioAsignado->email : null,
            'solucion' => $incidencia->solucion,
            'created_at' => $incidencia->created_at,
            'updated_at' => $incidencia->updated_at,
        ];
    }
}

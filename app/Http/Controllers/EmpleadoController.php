<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EmpleadoController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['nombres', 'apellidos', 'fecha_contratacion_inicio', 'fecha_contratacion_fin', 'id_estado']);

        // Realiza la consulta incluyendo todas las columnas del empleado y los nombres de las relaciones
        $empleados = Empleado::with(['cargo:id,nombre', 'departamento:id,nombre', 'municipio:id,nombre'])->get();

        // Transforma la salida para incluir todos los datos del empleado y los nombres de las relaciones
        $empleados = $empleados->map(function ($empleado) {
            return [
                'id' => $empleado->id,
                'nombres' => $empleado->nombres,
                'apellidos' => $empleado->apellidos,
                'dui' => $empleado->dui,
                'telefono' => $empleado->telefono,
                'fecha_nacimiento' => $empleado->fecha_nacimiento,
                'fecha_contratacion' => $empleado->fecha_contratacion,
                'id_estado' => $empleado->id_estado,
                'id_cargo' => $empleado->id_cargo,
                'id_departamento' => $empleado->id_departamento,
                'id_municipio' => $empleado->id_municipio,
                'direccion' => $empleado->direccion,
                'created_by' => $empleado->created_by,
                'updated_by' => $empleado->updated_by,
                'created_at' => $empleado->created_at,
                'updated_at' => $empleado->updated_at,
                'cargo' => $empleado->cargo->nombre ?? null,
                'departamento' => $empleado->departamento->nombre ?? null,
                'municipio' => $empleado->municipio->nombre ?? null
            ];
        });

        $data = [
            'empleados' => $empleados,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        // Valida los datos de entrada, incluyendo la unicidad del teléfono
        $validator = Validator::make($request->all(), [
            'nombres' => 'nullable|max:255',
            'apellidos' => 'nullable|max:255',
            'dui' => 'nullable|digits:9|unique:empleados',
            'telefono' => 'nullable|digits:8|unique:empleados', // Asegura que el teléfono sea único
            'fecha_nacimiento' => 'nullable|date|before_or_equal:' . Carbon::now()->subYears(18)->format('Y-m-d'), // Valida que la persona tenga al menos 18 años
            'fecha_contratacion' => 'nullable|date|before_or_equal:today', // Valida que la fecha de contratación no sea en el futuro
            'id_estado' => 'nullable|exists:estado_empleados,id',
            'id_cargo' => 'nullable|exists:cargos,id',
            'id_departamento' => 'nullable|exists:departamento,id',
            'id_municipio' => 'nullable|exists:municipios,id',
            'direccion' => 'nullable|max:255',
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        // Crea el empleado si la validación es exitosa
        $empleado = Empleado::create($request->all());

        if (!$empleado) {
            $data = [
                'message' => 'Error al crear el empleado',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        $data = [
            'empleado' => $empleado,
            'status' => 201
        ];

        return response()->json($data, 201);
    }

    public function show($id)
    {
        $empleado = Empleado::with(['cargo:id,nombre', 'departamento:id,nombre', 'municipio:id,nombre'])->find($id);

        if (!$empleado) {
            $data = [
                'message' => 'Empleado no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        // Devolver datos del empleado junto con nombres de relaciones en lugar de IDs
        $data = [
            'empleado' => [
                'id' => $empleado->id,
                'nombres' => $empleado->nombres,
                'apellidos' => $empleado->apellidos,
                'dui' => $empleado->dui,
                'telefono' => $empleado->telefono,
                'fecha_nacimiento' => $empleado->fecha_nacimiento,
                'fecha_contratacion' => $empleado->fecha_contratacion,
                'id_estado' => $empleado->id_estado,
                'cargo' => $empleado->cargo->nombre ?? null,
                'departamento' => $empleado->departamento->nombre ?? null,
                'municipio' => $empleado->municipio->nombre ?? null,
                'direccion' => $empleado->direccion,
                'created_by' => $empleado->created_by,
                'updated_by' => $empleado->updated_by,
                'created_at' => $empleado->created_at,
                'updated_at' => $empleado->updated_at,
            ],
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function destroy($id)
    {
        $empleado = Empleado::find($id);

        if (!$empleado) {
            $data = [
                'message' => 'Empleado no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $empleado->delete();

        $data = [
            'message' => 'Empleado eliminado',
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function relacion()
    {
        // Actualizo la consulta para que refleje la tabla de relaciones correctamente
        $empleados = DB::table('empleados')
            ->join('users', 'empleados.id', '=', 'users.id_empleado')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->join('cargos', 'empleados.id_cargo', '=', 'cargos.id')
            ->join('departamento', 'empleados.id_departamento', '=', 'departamento.id')  // Aquí cambié 'departamentos' por 'departamento'
            ->join('municipios', 'empleados.id_municipio', '=', 'municipios.id')
            ->select(
                'empleados.id',
                'empleados.nombres',
                'empleados.apellidos',
                'empleados.dui',
                'empleados.telefono',
                'empleados.fecha_nacimiento',
                'empleados.fecha_contratacion',
                'empleados.direccion',
                'empleados.created_by',
                'empleados.updated_by',
                'empleados.created_at',
                'empleados.updated_at',
                'users.email as usuario_email',
                'roles.name as rol',
                'cargos.nombre as cargo',
                'departamento.nombre as departamento',  // Aquí cambié 'departamentos.nombre' por 'departamento.nombre'
                'municipios.nombre as municipio'
            )
            ->get();

        return response()->json([
            'empleados' => $empleados,
            'status' => 200
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $empleado = Empleado::find($id);

        if (!$empleado) {
            $data = [
                'message' => 'Empleado no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'nombres' => 'nullable|max:255',
            'apellidos' => 'nullable|max:255',
            'dui' => 'nullable|digits:9|unique:empleados,dui,' . $id,
            'telefono' => 'nullable|digits:8|unique:empleados,telefono,' . $id, // Asegura que el teléfono sea único durante la actualización
            'fecha_nacimiento' => 'nullable|date|before_or_equal:' . Carbon::now()->subYears(18)->format('Y-m-d'),
            'fecha_contratacion' => 'nullable|date|before_or_equal:today',
            'id_estado' => 'nullable|exists:estado_empleados,id',
            'id_cargo' => 'nullable|exists:cargos,id',
            'id_departamento' => 'nullable|exists:departamento,id',
            'id_municipio' => 'nullable|exists:municipios,id',
            'direccion' => 'nullable|max:255'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $empleado->update($request->all());

        $data = [
            'message' => 'Empleado actualizado con éxito.',
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    private function validateEmpleado($request, $id = null)
    {
        // Reglas de validación para los datos del empleado
        $rules = [
            'nombres' => 'nullable|max:255',
            'apellidos' => 'nullable|max:255',
            'dui' => 'nullable|regex:/^\d{8}-?\d{1}$/|unique:empleados,dui,' . $id, // Valida que el DUI sea único, excepto para el empleado que se está actualizando
            'telefono' => 'nullable|regex:/^\d{4}-?\d{4}$/|unique:empleados,telefono,' . $id, // Valida que el teléfono sea único, excepto para el empleado que se está actualizando
            'fecha_nacimiento' => 'nullable|date|before_or_equal:' . Carbon::now()->subYears(18)->format('Y-m-d'), // Valida que la fecha de nacimiento sea de una persona mayor de 18 años
            'fecha_contratacion' => 'nullable|date|before_or_equal:today', // Valida que la fecha de contratación no sea en el futuro
            'id_estado' => 'nullable|exists:estado,id',
            'id_cargo' => 'nullable|exists:cargo,id',
            'id_departamento' => 'nullable|exists:departamento,id',
            'id_municipio' => 'nullable|exists:municipio,id',
            'direccion' => 'nullable|max:255',
        ];

        // Mensajes personalizados para las reglas de validación
        $messages = [
            'dui.unique' => 'El número de DUI ya está registrado.',
            'telefono.unique' => 'El número de teléfono ya está registrado.',
            'fecha_nacimiento.before_or_equal' => 'El empleado debe tener al menos 18 años.',
            'fecha_contratacion.before_or_equal' => 'La fecha de contratación no puede ser una fecha futura.',
        ];

        // Devuelve el validador con las reglas y los mensajes personalizados
        return Validator::make($request->all(), $rules, $messages);
    }
}

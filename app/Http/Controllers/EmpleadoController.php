<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class EmpleadoController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['nombres', 'apellidos', 'fecha_contratacion_inicio', 'fecha_contratacion_fin', 'id_estado']);
        
        // Realiza la consulta incluyendo todas las columnas del empleado y los nombres de las relaciones
        $empleados = Empleado::with(['cargo:id,nombre', 'departamento:id,nombre', 'municipio:id,nombre'])->get();
        
        // Transforma la salida para incluir todos los datos del empleado y los nombres de las relaciones
        $empleados = $empleados->map(function($empleado) {
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
        $validator = Validator::make($request->all(), [
            'nombres' => 'required|max:255',
            'apellidos' => 'required|max:255',
            'dui' => 'required|digits:9|unique:empleados',
            'telefono' => 'required|digits:8',
            'fecha_nacimiento' => 'required|date',
            'fecha_contratacion' => 'required|date',
            'id_estado' => 'required',
            'id_cargo' => 'required',
            'id_departamento' => 'required',
            'direccion' => 'required|max:255',
            'id_municipio' => 'required'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $empleado = Empleado::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'dui' => $request->dui,
            'telefono' => $request->telefono,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'fecha_contratacion' => $request->fecha_contratacion,
            'id_estado' => $request->id_estado,
            'id_cargo' => $request->id_cargo,
            'id_departamento' => $request->id_departamento,
            'id_municipio' => $request->id_municipio,
            'direccion' => $request->direccion
        ]);

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
        $empleados = DB::table('empleados')
            ->join('users', 'empleados.id', '=', 'users.id')
            ->join('roles', 'users.id', '=', 'roles.id')
            ->select('empleados.*', 'users.name as nombre_usuario', 'roles.name as nombre_rol')
            ->get();

        return response()->json([
            'empleados' => $empleados
        ]);
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
            'nombres' => 'required|max:255',
            'apellidos' => 'required|max:255',
            'dui' => 'required|digits:9|unique:empleados,dui,' . $id,
            'telefono' => 'required|digits:8',
            'fecha_nacimiento' => 'required|date',
            'fecha_contratacion' => 'required|date',
            'id_estado' => 'required',
            'id_cargo' => 'required',
            'id_departamento' => 'required',
            'id_municipio' => 'required',
            'direccion' => 'required|max:255'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $empleado->nombres = $request->nombres;
        $empleado->apellidos = $request->apellidos;
        $empleado->dui = $request->dui;
        $empleado->telefono = $request->telefono;
        $empleado->fecha_nacimiento = $request->fecha_nacimiento;
        $empleado->fecha_contratacion = $request->fecha_contratacion;
        $empleado->id_estado = $request->id_estado;
        $empleado->id_cargo = $request->id_cargo;
        $empleado->id_departamento = $request->id_departamento;
        $empleado->id_municipio = $request->id_municipio;
        $empleado->direccion = $request->direccion;

        $empleado->save();

        $data = [
            'message' => 'Empleado actualizado',
            'empleado' => $empleado,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function updatePartial(Request $request, $id)
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
            'nombres' => 'max:255',
            'apellidos' => 'max:255',
            'dui' => 'digits:9|unique:empleados,dui,' . $id,
            'telefono' => 'digits:8',
            'email' => 'email|unique:empleados,email,' . $id,
            'direccion' => 'max:255',
            'language' => 'in:English,Spanish,French'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        if ($request->has('nombres')) {
            $empleado->nombres = $request->nombres;
        }

        if ($request->has('apellidos')) {
            $empleado->apellidos = $request->apellidos;
        }

        if ($request->has('dui')) {
            $empleado->dui = $request->dui;
        }

        if ($request->has('telefono')) {
            $empleado->telefono = $request->telefono;
        }

        if ($request->has('email')) {
            $empleado->email = $request->email;
        }

        if ($request->has('direccion')) {
            $empleado->direccion = $request->direccion;
        }

        if ($request->has('language')) {
            $empleado->language = $request->language;
        }

        $empleado->save();

        $data = [
            'message' => 'Empleado actualizado',
            'empleado' => $empleado,
            'status' => 200
        ];

        return response()->json($data, 200);
    }
}

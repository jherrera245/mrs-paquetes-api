<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;



class EmpleadoController extends Controller
{

    public function index()
    {
        $empleados = Empleado::all();

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
            'id_genero' => 'required',
            'dui' => 'required|digits:9|unique:empleados',
            'telefono' => 'required|digits:8',
            'email' => 'required|email|unique:empleados',
            'fecha_nacimiento' => 'required|date',
            'fecha_contratacion' => 'required|date',
            'salario' => 'required|numeric',
            'id_estado' => 'required',
            'id_cargo' => 'required',
            'id_departamento' => 'required',
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
            'id_genero' => $request->id_genero,
            'dui' => $request->dui,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'fecha_contratacion' => $request->fecha_contratacion,
            'salario' => $request->salario,
            'id_estado' => $request->id_estado,
            'id_cargo' => $request->id_cargo,
            'id_departamento' => $request->id_departamento,
            'id_municipio' => $request->id_municipio
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

         // Validar los datos del request utilizando las reglas del modelo Empleado
         $validator = Empleado::validate($request->all());

         if ($validator->fails()) {
             $errors = implode('<br>', $validator->errors()->all());
             return response()->json(['error' => $errors], 400);
         }

         // Si la validación pasa, procede con la creación del empleado
     }

     // Otros métodos del controlador...





    public function show($id)
    {
        $empleado = Empleado::find($id);

        if (!$empleado) {
            $data = [
                'message' => 'Empleado no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'empleado' => $empleado,
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
        'id_genero' => 'required',
        'dui' => 'required|digits:9|unique:empleados,dui,'.$id,
        'telefono' => 'required|digits:8',
        'email' => 'required|email|unique:empleados,email,'.$id,
        'fecha_nacimiento' => 'required|date',
        'fecha_contratacion' => 'required|date',
        'salario' => 'required|numeric',
        'id_estado' => 'required',
        'id_cargo' => 'required',
        'id_departamento' => 'required',
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

    $empleado->nombres = $request->nombres;
    $empleado->apellidos = $request->apellidos;
    $empleado->id_genero = $request->id_genero;
    $empleado->dui = $request->dui;
    $empleado->telefono = $request->telefono;
    $empleado->email = $request->email;
    $empleado->fecha_nacimiento = $request->fecha_nacimiento;
    $empleado->fecha_contratacion = $request->fecha_contratacion;
    $empleado->salario = $request->salario;
    $empleado->id_estado = $request->id_estado;
    $empleado->id_cargo = $request->id_cargo;
    $empleado->id_departamento = $request->id_departamento;
    $empleado->id_municipio = $request->id_municipio;

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
        'dui' => 'digits:9|unique:empleados,dui,'.$id,
        'telefono' => 'digits:8',
        'email' => 'email|unique:empleados,email,'.$id,
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

<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Rules\validNit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientesController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only([
            'nombre', 'apellido', 'nombre_comercial', 'dui', 'telefono', 
            'id_tipo_persona', 'es_contribuyente', 'id_genero', 'fecha_registro',
            'id_estado', 'id_departamento', 'id_municipio', 'nit', 'nrc', 'giro', 'nombre_empresa', 'direccion'
        ]);

        $perPage = $request->input('per_page', 10);

        $clientes = Clientes::filter($filters)->paginate($perPage);

        return response()->json($clientes);
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'nombre', 'apellido', 'nombre_comercial', 'dui', 'telefono', 
            'id_tipo_persona', 'es_contribuyente', 'id_genero', 'fecha_registro',
            'id_estado', 'id_departamento', 'id_municipio', 'nit', 'nrc', 'giro', 'nombre_empresa', 'direccion'
        ]);

        $validator = Validator::make($data, [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'nombre_comercial' => 'nullable|string|max:255',
            'dui' => [
                'nullable',
                'string',
                'regex:/^\d{8}-\d$/',
                function ($attribute, $value, $fail) {
                    if (!empty($value) && Clientes::where('dui', $value)->exists()) {
                        $fail('El DUI ya está registrado.');
                    }
                },
            ],
            'telefono' => [
                'required',
                'regex:/^\d{4}-?\d{4}$/',
                function ($attribute, $value, $fail) {
                    if (Clientes::where('telefono', $value)->exists()) {
                        $fail('El teléfono ya está registrado.');
                    }
                },
            ],
            'id_tipo_persona' => 'required|exists:tipo_persona,id',
            'es_contribuyente' => 'required|boolean',
            'id_genero' => 'required|exists:genero,id',
            'fecha_registro' => 'required|date',
            'id_estado' => 'required|exists:estado_clientes,id',
            'id_departamento' => 'required|exists:departamento,id',
            'id_municipio' => 'required|exists:municipios,id',
            'nit' => [
                'nullable',
                'string',
                'max:20',
                new validNit,
                function ($attribute, $value, $fail) {
                    if (!empty($value) && Clientes::where('nit', $value)->exists()) {
                        $fail('El NIT ya está registrado.');
                    }
                },
            ],
            'nrc' => 'nullable|string|max:20',
            'giro' => 'nullable|string|max:255',
            'direccion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        $clientes = Clientes::create($data);

        return response()->json($clientes, 201);
    }

    public function update(Request $request, Clientes $clientes)
    {
        $data = $request->only([
            'nombre', 'apellido', 'nombre_comercial', 'dui', 'telefono', 
            'id_tipo_persona', 'es_contribuyente', 'id_genero', 'fecha_registro',
            'id_estado', 'id_departamento', 'id_municipio', 'nit', 'nrc', 'giro', 'nombre_empresa', 'direccion'
        ]);

        $validator = Validator::make($data, [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'nombre_comercial' => 'nullable|string|max:255',
            'dui' => [
                'nullable',
                'string',
                'regex:/^\d{8}-\d$/',
                function ($attribute, $value, $fail) use ($clientes) {
                    if (!empty($value) && Clientes::where('dui', $value)->where('id', '!=', $clientes->id)->exists()) {
                        $fail('El DUI ya está registrado.');
                    }
                },
            ],
            'telefono' => [
                'required',
                'regex:/^\d{4}-?\d{4}$/',
                function ($attribute, $value, $fail) use ($clientes) {
                    if (Clientes::where('telefono', $value)->where('id', '!=', $clientes->id)->exists()) {
                        $fail('El teléfono ya está registrado.');
                    }
                },
            ],
            'id_tipo_persona' => 'required|exists:tipo_persona,id',
            'es_contribuyente' => 'required|boolean',
            'id_genero' => 'required|exists:genero,id',
            'fecha_registro' => 'required|date',
            'id_estado' => 'required|exists:estado_clientes,id',
            'id_departamento' => 'required|exists:departamento,id',
            'id_municipio' => 'required|exists:municipios,id',
            'nit' => [
                'nullable',
                'string',
                'max:20',
                new validNit,
                function ($attribute, $value, $fail) use ($clientes) {
                    if (!empty($value) && Clientes::where('nit', $value)->where('id', '!=', $clientes->id)->exists()) {
                        $fail('El NIT ya está registrado.');
                    }
                },
            ],
            'nrc' => 'nullable|string|max:20',
            'giro' => 'nullable|string|max:255',
            'direccion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        $clientes->update($data);

        return response()->json($clientes, 200);
    }

    public function show($id)
    {
        $clientes = Clientes::find($id);

        if (!$clientes) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        return response()->json(['cliente' => $clientes], 200);
    }

    public function destroy(Clientes $clientes)
    {
        if ($clientes->delete()) {
            return response()->json(['success' => 'Cliente eliminado correctamente'], 200);
        }
        
        return response()->json(['error' => 'No se pudo eliminar el cliente'], 400);
    }
}
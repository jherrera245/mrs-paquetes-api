<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientesController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only([
            'nombre', 'apellido', 'nombre_comercial', 'email', 'dui', 'telefono', 
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
            'nombre', 'apellido', 'nombre_comercial', 'email', 'dui', 'telefono', 
            'id_tipo_persona', 'es_contribuyente', 'id_genero', 'fecha_registro',
            'id_estado', 'id_departamento', 'id_municipio', 'nit', 'nrc', 'giro', 'nombre_empresa', 'direccion'
        ]);

        $validator = Validator::make($data, [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'nombre_comercial' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'dui' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (!empty($value) && !preg_match('/^\d{8}-\d$/', $value)) {
                        $fail('El formato del DUI no es válido. Debe ser en formato 12345678-9.');
                    }
                },
            ],
            'telefono' => 'required|regex:/^\d{4}-?\d{4}$/',
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
                'regex:/^\d{4}-\d{6}-\d{3}-\d$/',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^\d{4}-\d{6}-\d{3}-\d$/', $value)) {
                        $fail('El formato del NIT no es válido. Debe ser en formato 1234-123456-123-0.');
                    }
                },
            ],
            'nrc' => 'nullable|string|max:20',
            'giro' => 'nullable|string|max:255',
            'direccion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $cliente = Clientes::create($data);

        return response()->json($cliente, 200);
    }

    public function update(Request $request, Clientes $cliente)
    {
        $data = $request->only([
            'nombre', 'apellido', 'nombre_comercial', 'email', 'dui', 'telefono', 
            'id_tipo_persona', 'es_contribuyente', 'id_genero', 'fecha_registro',
            'id_estado', 'id_departamento', 'id_municipio', 'nit', 'nrc', 'giro', 'nombre_empresa', 'direccion'
        ]);

        $validator = Validator::make($data, [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'nombre_comercial' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'dui' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (!empty($value) && !preg_match('/^\d{8}-\d$/', $value)) {
                        $fail('El formato del DUI no es válido. Debe ser en formato 12345678-9.');
                    }
                },
            ],
            'telefono' => 'required|regex:/^\d{4}-?\d{4}$/',
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
                'regex:/^\d{4}-\d{6}-\d{3}-\d$/',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^\d{4}-\d{6}-\d{3}-\d$/', $value)) {
                        $fail('El formato del NIT no es válido. Debe ser en formato 1234-123456-123-0.');
                    }
                },
            ],
            'nrc' => 'nullable|string|max:20',
            'giro' => 'nullable|string|max:255',
            'direccion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $cliente->update($data);

        return response()->json($cliente, 200);
    }

    public function show($id)
    {
        $cliente = Clientes::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado', 'status' => 404], 404);
        }

        return response()->json(['cliente' => $cliente, 'status' => 200], 200);
    }

    public function destroy(Clientes $cliente)
    {
        if ($cliente->delete()) {
            return response()->json(['success' => 'Cliente eliminado correctamente'], 200);
        }
        
        return response()->json(['error' => 'No se pudo eliminar el cliente'], 400);
    }
}

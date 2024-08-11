<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Clientes;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;
use App\Notifications\EmailVerificationNotification;

class AuthController extends Controller
{

    public function register(Request $request)
    {

         $r_user = 2;
         //Indicamos que solo queremos recibir name, email y password de la request
        $data = $request->only('email', 'password', 'id_empleado', 'id_cliente',
                            'nombre', 'apellido', 'nombre_comercial', 'dui', 'telefono',
                            'id_tipo_persona', 'es_contribuyente', 'id_genero', 'fecha_registro',
                            'id_estado', 'id_departamento', 'id_municipio', 'nit', 'nrc',
                            'giro', 'nombre_empresa', 'direccion');
        //Realizamos las validaciones
        $validator = Validator::make($data, [

            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50',
            'id_empleado' => 'id_empleado|unique:users',
            'id_cliente' => 'id_cliente|unique:users',
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'nombre_comercial' => 'nullable|string|max:255',
        'dui' => 'required|regex:/^\d{8}-?\d{1}$/|unique:clientes',
        'telefono' => 'required|regex:/^\d{4}-?\d{4}$/',
        'id_tipo_persona' => 'required|exists:tipo_persona,id',
        'es_contribuyente' => 'required|boolean',
        'id_genero' => 'required|exists:genero,id',
        'fecha_registro' => 'required|date',
        'id_estado' => 'required|exists:estado_clientes,id',
        'id_departamento' => 'required|exists:departamento,id',
        'id_municipio' => 'required|exists:municipios,id',
        'nit' => 'required|regex:/^\d{4}-?\d{6}-?\d{3}-?\d{1}$/',
        'nrc' => 'required|regex:/^\d{6}-?\d{1}$/',
        'giro' => 'required|string',
        'nombre_empresa' => 'required|string',
        'direccion' => 'required|string',

        ]);

        //Devolvemos un error si fallan las validaciones
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        $user = new User();
        $cliente = new Clientes();
        $cliente->nombre = $request->nombre;
        $cliente->apellido = $request->apellido;
        $cliente->nombre_comercial = $request->nombre_comercial;
        $cliente->dui = $request->dui;
        $cliente->telefono = $request->telefono;
        $cliente->id_tipo_persona = $request->id_tipo_persona;
        $cliente->es_contribuyente = $request->es_contribuyente;
        $cliente->id_genero = $request->id_genero;
        $cliente->fecha_registro = $request->fecha_registro;
        $cliente->id_estado = $request->id_estado;
        $cliente->id_departamento = $request->id_departamento;
        $cliente->id_municipio = $request->id_municipio;
        $cliente->nit = $request->nit;
        $cliente->nrc = $request->nrc;
        $cliente->giro = $request->giro;
        $cliente->nombre_empresa = $request->nombre_empresa;
        $cliente->direccion = $request->direccion;
        $cliente->created_by = $user->id; // Asumiendo que el usuario que crea el cliente es el creador
        $cliente->updated_by = $user->id;
        $cliente->save();


        $user->name = $request->nombre.' '.$request->apellido;
        $user->email = $request->email;
        $user->password =  bcrypt($request->password);
        $user->type = 1;
        $user->id_cliente= $cliente->id;

        $user->save();

        $role = $r_user;
        $user->roles()->detach();
        $user->assignRole($role);

        $user->notify(new EmailVerificationNotification());

        return response()->json(['message' => 'User created successfully'], Response::HTTP_OK);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['message' => 'Login failed'], Response::HTTP_BAD_REQUEST);
            }
        } catch (JWTException $e) {
            return response()->json(
                [
                    'message' => 'Internal Server Error',
                    'error' => $e->getMessage(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        $user = Auth::user();

        if ($user->status != 1) {
            return response()->json(['message' => 'Account is inactive'], Response::HTTP_FORBIDDEN);
        }

        $roleName = $user->getRoleNames()->first();
        $rolePermissions = $user->getPermissionsViaRoles()->pluck('name')->toArray();
        $permissionsByModule = [];

        foreach ($rolePermissions as $permission) {
            [$module, $actionPermission] = explode('-', $permission, 2);

            if (!isset($permissionsMap[$module])) {
                $permissionsMap[$module] = [
                    'modulo' => $module,
                    'permissions' => []
                ];
            }

            $permissionsMap[$module]['permissions'][$actionPermission] = true;
        }

        $formattedPermissions = array_values($permissionsMap);
        unset($user->roles);

        $payload = [
            'user' => $user,
            'role' => $roleName,
            'permissions' => $formattedPermissions,
            'token' => $token,
        ];

        return response()->json($payload);
    }

    public function logout(Request $request)
    {
        $validator = Validator::make($request->only('token'), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            JWTAuth::invalidate($request->token);
            return response()->json([
                'success' => true,
                'message' => 'User disconnected',
            ]);
        } catch (JWTException $exception) {
            return response()->json(
                [
                    'message' => 'Internal Server Error',
                    'error' => $e->getMessage(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    //Función que utilizaremos para obtener los datos del usuario y validar si el token a expirado.
    public function getUser(Request $request)
    {
        //Validamos que la request tenga el token
        $this->validate($request, [
            'token' => 'required',
        ]);

        $user = JWTAuth::authenticate($request->token);

        if (!$user) {
            return response()->json(['message' => 'Invalid token / token expired'], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json(['user' => $user]);
    }

    public function getUserById($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User Not Found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['user' => $user]);
    }

    // Función para obtener todos los usuarios
    public function getUsers()
    {
        $users = User::all();
        return response()->json(['users' => $users]);
    }

    // Función para asignar roles a un usuario
    public function assignUserRole(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $role = Role::find($request->role_id);

        if (!$role) {
            return response()->json(['error' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }

        if ($user->hasRole($role)) {
            return response()->json(['message' => 'The user is already assigned this role.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->roles()->detach();
        $user->assignRole($role);

        return response()->json(['message' => 'Role assigned to user successfully!'], Response::HTTP_OK);
    }

    public function assignPermissionsToRole(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $role = Role::find($id);

        $permissionIds = $request->permissions;
        $permissionModels = Permission::whereIn('id', $permissionIds)->get();

        DB::transaction(function () use ($role, $permissionModels) {
            $role->syncPermissions($permissionModels);
        });

        return response()->json(['message' => 'Permissions assigned successfully'], Response::HTTP_OK);
    }

    public function getAssignedPermissionsToRole($id)
    {
        // Verificar si el rol existe en la base de datos
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }


        $permissions = Permission::all();
        $result = [];

        foreach($permissions as $permission)
        {
            $assigned = $role->hasPermissionTo($permission);

            $result[] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'assigned' => $assigned,
            ];
        }

        return response()->json(['permissions' => $result], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        //Indicamos que solo queremos recibir name, email y password de la request
        $data = $request->only('name', 'email', 'password', 'type', 'role_id');
        //Realizamos las validaciones
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50',
            'role_id' => 'required|integer',
            'type' => 'required|integer',
            'id_empleado' => 'id_empleado|unique:users',
            'id_cliente' => 'id_cliente|unique:users',
        ]);

        //Devolvemos un error si fallan las validaciones
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password =  bcrypt($request->password);
        $user->type = $request->type;

        if (empty($request->id_empleado) && empty($request->id_cliente)) {
            return response()->json(['error' => 'Faild to create user for employee or customer'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($request->type == 0 && !empty($request->id_empleado)) {
            $user->id_empleado = $request->id_empleado;
        } else if ($request->type == 1 && !empty($request->id_cliente)) {
            $user->id_cliente = $request->id_cliente;
        } else {
            return response()->json(['error' => 'Faild to create user for employee or customer'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->save();

        $role = Role::find($request->role_id);
        $user->roles()->detach();
        $user->assignRole($role);

        return response()->json(['message' => 'User created successfully'], Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        // Validar la solicitud
        $data = $request->only('name', 'email', 'password',  'role_id');
        //Realizamos las validaciones
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->status = $request->status ? 1 : 0;
        $user->type = $request->type;

        if (!empty($request->password)) {
            $user->password =  bcrypt($request->password);
        }

        if (empty($request->id_empleado) && empty($request->id_cliente)) {
            return response()->json(['error' => 'Faild to updated user for employee or customer'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($request->type == 0 && !empty($request->id_empleado)) {
            $user->id_empleado = $request->id_empleado;
            $user->id_cliente = null;
        } else if ($request->type == 1 && !empty($request->id_cliente)) {
            $user->id_cliente = $request->id_cliente;
            $user->id_empleado = null;
        } else {
            return response()->json(['error' => 'Faild to updated user for employee or customer'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->save();

        $role = Role::find($request->role_id);
        $user->roles()->detach();
        $user->assignRole($role);

        if ($user) {
            return response()->json(['message' => 'Users updated successfully'], Response::HTTP_OK);
        } else {
            return response()->json(['error' => 'Error al actualizar el usuario'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        if ($user->id == 1) {
            return response()->json(['error' => 'Failed to delete user'], Response::HTTP_NOT_FOUND);
        }

        if ($user->delete()) {
            return response()->json(['message' => 'User deleted successfully'], Response::HTTP_OK);
        }

        return response()->json(['error' => 'Failed to delete user'], Response::HTTP_UNPROCESSABLE_ENTITY);
    }



}

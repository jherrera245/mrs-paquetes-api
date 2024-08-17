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
    public function adminClienteRegistrar(Request $request)
    {
    $r_user = 2;

    // Indicamos que solo queremos recibir  email y password de la request
    $data = $request->only('email', 'password', 'nombre', 'apellido', 'nombre_comercial', 'dui', 'telefono', 'id_tipo_persona', 'es_contribuyente', 'id_estado', 'id_departamento', 'id_municipio', 'nit', 'nrc', 'giro', 'nombre_empresa', 'direccion');

    // Realizamos las validaciones
    $validator = Validator::make($data, [
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6|max:50',
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'nombre_comercial' => 'nullable|string|max:255',
        'dui' => 'required|regex:/^\d{8}-?\d{1}$/|unique:clientes,dui',
        'telefono' => 'required|regex:/^\d{4}-?\d{4}$/|unique:clientes,telefono',
        'id_tipo_persona' => 'required|exists:tipo_persona,id',
        'es_contribuyente' => 'required|boolean',
        'fecha_registro' => 'nullable|date_format:Y-m-d',
        'id_estado' => 'required|exists:estado_clientes,id',
        'id_departamento' => 'required|exists:departamento,id',
        'id_municipio' => 'required|exists:municipios,id',
        'nit' => 'required|regex:/^\d{4}-?\d{6}-?\d{3}-?\d{1}$/|unique:clientes,nit',
        'nrc' => 'required|regex:/^\d{6}-?\d{1}$/|unique:clientes,nrc',
        'giro' => 'required|string',
        'nombre_empresa' => 'required|string',
        'direccion' => 'required|string'
    ]);

    // Devolvemos un error si fallan las validaciones
    if ($validator->fails()) {
        return response()->json(['error' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    // Crear usuario
    $user = new User();
    $user->email = $request->email;
    $user->password = bcrypt($request->password);
    $user->type = 1;
    $user->save();

    // Asignar rol
    $role = $r_user;
    $user->roles()->detach();
    $user->assignRole($role);

    // Notificar al usuario
    $user->notify(new EmailVerificationNotification());

    // Crear cliente
    $client = new Clientes();
    
    $client->id_user = $user->id;
    $client->nombre = $request->nombre;
    $client->apellido = $request->apellido;
    $client->nombre_comercial = $request->nombre_comercial;
    $client->dui = $request->dui;
    $client->telefono = $request->telefono;
    $client->id_tipo_persona = $request->id_tipo_persona;
    $client->es_contribuyente = $request->es_contribuyente;
    $client->id_estado = $request->id_estado;
    $client->id_departamento = $request->id_departamento;
    $client->id_municipio = $request->id_municipio;
    $client->nit = $request->nit;
    $client->nrc = $request->nrc;
    $client->giro = $request->giro;
    $client->nombre_empresa = $request->nombre_empresa;
    $client->direccion = $request->direccion;
    $client->fecha_registro = now(); // Puedes usar la fecha actual
    $client->created_by = $user->id;
    $client->updated_by = $user->id;
    $client->save();

    return response()->json(['message' => 'usuario creado exitosamente'], Response::HTTP_OK);
}


    public function register(Request $request)
    {
        $r_user = 2;
         //Indicamos que solo queremos recibir email y password de la request
        $data = $request->only('email', 'password');

        //Realizamos las validaciones
        $validator = Validator::make($data, [
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:6|max:50'
        ]);

        //Devolvemos un error si fallan las validaciones
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = new User();
        $user->email = $request->email;
        $user->password =  bcrypt($request->password);
        $user->type = 1;

        $user->save();

        $role = $r_user;
        $user->roles()->detach();
        $user->assignRole($role);

        $user->notify(new EmailVerificationNotification());

        return response()->json(['message' => 'User created successfully'], Response::HTTP_OK);
    }

    public function crearClientePerfil(Request $request)
    {
        $data = $request->only([
            'email', 'password', 'id_user', 'nombre', 'apellido', 'nombre_comercial', 'dui', 
            'telefono', 'id_tipo_persona', 'es_contribuyente', 'fecha_registro', 'id_estado', 
            'id_departamento', 'id_municipio', 'nit', 'nrc', 'giro', 'nombre_empresa', 'direccion'
        ]);
    
        // Realizamos las validaciones
        $validator = Validator::make($data, [ 
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'nombre_comercial' => 'nullable|string|max:255',
        'dui' => 'required|regex:/^\d{8}-?\d{1}$/|unique:clientes,dui',
        'telefono' => 'required|regex:/^\d{4}-?\d{4}$/|unique:clientes,telefono',
        'id_tipo_persona' => 'required|exists:tipo_persona,id',
        'es_contribuyente' => 'required|boolean',
        'fecha_registro' => 'nullable|date_format:Y-m-d',
        'id_estado' => 'required|exists:estado_clientes,id',
        'id_departamento' => 'required|exists:departamento,id',
        'id_municipio' => 'required|exists:municipios,id',
        'nit' => 'required|regex:/^\d{4}-?\d{6}-?\d{3}-?\d{1}$/|unique:clientes,nit',
        'nrc' => 'required|regex:/^\d{6}-?\d{1}$/|unique:clientes,nrc',
        'giro' => 'required|string',
        'nombre_empresa' => 'required|string',
        'direccion' => 'required|string'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
    
        // Obtener el usuario autenticado usando JWT
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $cliente = new Clientes();
    
            $cliente->fill([
                'id_user' => $user->id,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'nombre_comercial' => $request->nombre_comercial,
                'dui' => $request->dui,
                'telefono' => $request->telefono,
                'id_tipo_persona' => $request->id_tipo_persona,
                'es_contribuyente' => $request->es_contribuyente,
                'fecha_registro' => $request->fecha_registro,
                'id_estado' => $request->id_estado,
                'id_departamento' => $request->id_departamento,
                'id_municipio' => $request->id_municipio,
                'nit' => $request->nit,
                'nrc' => $request->nrc,
                'giro' => $request->giro,
                'nombre_empresa' => $request->nombre_empresa,
                'direccion' => $request->direccion
            ]);
    
            $cliente->save();
    
            return response()->json(['message' => 'Perfil creado con éxito'], Response::HTTP_OK);
    
        } catch (\Exception $e) {
            \Log::error('Error creando cliente perfil: '.$e->getMessage());
            return response()->json([
                'error' => 'Hubo un problema al crear el perfil',
                'details' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function actualizarClientePerfil(Request $request, $id)
    {
        // Obtener el usuario autenticado usando JWT
    $user = JWTAuth::parseToken()->authenticate();
    if (!$user) {
        return response()->json(['error' => 'Usuario no autenticado'], Response::HTTP_UNAUTHORIZED);
    }

    // Obtener los datos de la solicitud
    $data = $request->only([
        'email', 'password', 'nombre', 'apellido', 'nombre_comercial', 'dui', 
        'telefono', 'id_tipo_persona', 'es_contribuyente','id_estado', 
        'id_departamento', 'id_municipio', 'nit', 'nrc', 'giro', 'nombre_empresa', 'direccion'
    ]);

    // Realizar las validaciones
    $validator = Validator::make($data, [ 
        'email' => 'nullable|email|unique:users,email,' . $user->id,
        'password' => 'nullable|min:8',
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'nombre_comercial' => 'nullable|string|max:255',
        'dui' => 'required|regex:/^\d{8}-?\d{1}$/|unique:clientes,dui,' . $user->id . ',id_user',
        'telefono' => 'required|regex:/^\d{4}-?\d{4}$/',
        'id_tipo_persona' => 'required|exists:tipo_persona,id',
        'es_contribuyente' => 'required|boolean',
        'id_estado' => 'required|exists:estado_clientes,id',
        'id_departamento' => 'required|exists:departamento,id',
        'id_municipio' => 'required|exists:municipios,id',
        'nit' => 'required|regex:/^\d{4}-?\d{6}-?\d{3}-?\d{1}$/',
        'nrc' => 'required|regex:/^\d{6}-?\d{1}$/',
        'giro' => 'required|string',
        'nombre_empresa' => 'required|string',
        'direccion' => 'required|string'
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    // Actualizar los datos en la tabla `users`
    try {
        if ($request->has('email')) {
            $user->email = $request->input('email');
        }

        if ($request->has('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        $user->save();

        // Actualizar los datos en la tabla `clientes`
        $cliente = Clientes::where('id_user', $user->id)->first();
        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado'], Response::HTTP_NOT_FOUND);
        }

            $cliente->nombre = $request->input('nombre', $cliente->nombre);
            $cliente->apellido = $request->input('apellido', $cliente->apellido);
            $cliente->nombre_comercial = $request->input('nombre_comercial', $cliente->nombre_comercial);
            $cliente->dui = $request->input('dui', $cliente->dui);
            $cliente->telefono = $request->input('telefono', $cliente->telefono);
            $cliente->id_tipo_persona = $request->input('id_tipo_persona', $cliente->id_tipo_persona);
            $cliente->es_contribuyente = $request->input('es_contribuyente', $cliente->es_contribuyente);
            $cliente->id_estado = $request->input('id_estado', $cliente->id_estado);
            $cliente->id_departamento = $request->input('id_departamento', $cliente->id_departamento);
            $cliente->id_municipio = $request->input('id_municipio', $cliente->id_municipio);
            $cliente->nit = $request->input('nit', $cliente->nit);
            $cliente->nrc = $request->input('nrc', $cliente->nrc);
            $cliente->giro = $request->input('giro', $cliente->giro);
            $cliente->nombre_empresa = $request->input('nombre_empresa', $cliente->nombre_empresa);
            $cliente->direccion = $request->input('direccion', $cliente->direccion);

            $cliente->save();

        return response()->json(['message' => 'Perfil actualizado con éxito'], Response::HTTP_OK);

        } catch (\Exception $e) {
            \Log::error('Error actualizando cliente perfil: '.$e->getMessage());
            return response()->json([
                'error' => 'Hubo un problema al actualizar el perfil',
                'details' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    
    public function login_cliente(Request $request)
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

 	if (!$user->email_verified_at) {
        return response()->json(['message' => 'Email no verificado'], Response::HTTP_FORBIDDEN);
    	}

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

    public function getUsers(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
    
        $users = DB::table('users')
            ->select(
                'users.id', 
                'users.name', 
                'users.email', 
                'roles.name as role_name', 
                'users.status', 
                'users.created_at', 
                'users.updated_at'
            )
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', 'App\\Models\\User');
    
        if ($name) {
            $users->where('users.name', 'like', '%' . $name . '%');
        }
    
        if ($email) {
            $users->where('users.email', 'like', '%' . $email . '%');
        }
    
        if ($startDate && $endDate) {
            $users->whereBetween(DB::raw('DATE(users.created_at)'), [$startDate, $endDate]);
        }
    
        $users = $users->get();
    
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

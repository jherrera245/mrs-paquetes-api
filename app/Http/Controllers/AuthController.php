<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
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

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Auth;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    //Función que utilizaremos para registrar al usuario
    public function register(Request $request)
    {
        //Indicamos que solo queremos recibir name, email y password de la request
        $data = $request->only('name', 'email', 'password');
        //Realizamos las validaciones
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50',
        ]);
        //Devolvemos un error si fallan las validaciones
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        //Creamos el nuevo usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        //Nos guardamos el usuario y la contraseña para realizar la petición de token a JWTAuth
        $credentials = $request->only('email', 'password');
        //Devolvemos la respuesta con el token del usuario
        return response()->json([
            'message' => 'User created',
            'token' => JWTAuth::attempt($credentials),
            'user' => $user,
        ], Response::HTTP_OK);
    }

    //Funcion que utilizaremos para hacer login
    public function authenticate(Request $request)
    {
        //Indicamos que solo queremos recibir email y password de la request
        $credentials = $request->only('email', 'password');
        //Validaciones
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50',
        ]);
        //Devolvemos un error de validación en caso de fallo en las verificaciones
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        //Intentamos hacer login
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                //Credenciales incorrectas.
                return response()->json([
                    'message' => 'Login failed',
                ], 401);
            }
        } catch (JWTException $e) {
            //Error chungo
            return response()->json([
                'message' => 'Error',
            ], 500);
        }
        //Devolvemos el token
        return response()->json([
            'token' => $token,
            'user' => Auth::user(),
        ]);
    }
    //Función que utilizaremos para eliminar el token y desconectar al usuario
    public function logout(Request $request)
    {
        //Validamos que se nos envie el token
        $validator = Validator::make($request->only('token'), [
            'token' => 'required',
        ]);
        //Si falla la validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        try {
            //Si el token es valido eliminamos el token desconectando al usuario.
            JWTAuth::invalidate($request->token);
            return response()->json([
                'success' => true,
                'message' => 'User disconnected',
            ]);
        } catch (JWTException $exception) {
            //Error chungo
            return response()->json([
                'success' => false,
                'message' => 'Error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    //Función que utilizaremos para obtener los datos del usuario y validar si el token a expirado.
    public function getUser(Request $request)
    {
        //Validamos que la request tenga el token
        $this->validate($request, [
            'token' => 'required',
        ]);
        //Realizamos la autentificación
        $user = JWTAuth::authenticate($request->token);
        //Si no hay usuario es que el token no es valido o que ha expirado
        if (!$user) {
            return response()->json([
                'message' => 'Invalid token / token expired',
            ], 401);
        }

        //Devolvemos los datos del usuario si todo va bien.
        return response()->json(['user' => $user]);
    }

    // Función para obtener todos los usuarios
    public function getUsers()
    {
        $users = User::where('status', 1)->get();
        return response()->json(['users' => $users]);
    }


    // Función para asignar roles a un usuario
    public function assignUserRole(Request $request, $id)
    {
        // Validar la solicitud
        $validator = Validator::make($request->all(), [
            'role' => 'required|exists:roles,name', // Verificar que el rol exista en la tabla de roles
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        // Obtener el usuario
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        //Variable que recibe el valor del rol
        $roleName = $request->input('role');

        try {
            //Busca el rol que se recibe
            $role = Role::where('name', $roleName)->first();

            // Asignar el rol al usuario
            if ($role) {
                //verificamos si el usuario ya tiene un rol
                if ($user->hasRole($role)) {
                    return response()->json(['message' => 'El usuario ya tiene asignado este rol'], 200);
                }
                $user->syncRoles([$role]);
                return response()->json(['message' => 'Rol asignado!'], 200);
            } else {
                return response()->json(['error' => 'Rol no encontrado'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al asignar el rol'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Validar la solicitud
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        

        if ($user->save()) {
            return response()->json(['message' => 'Usuario actualizado']);
        } else {
            return response()->json(['error' => 'Error al actualizar el usuario'], 500);
        }
    
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->status = 0;
        

        if ($user->save()) {
            return response()->json(['message' => 'Usuario Borrado']);
        } else {
            return response()->json(['error' => 'Error al borrar el usuario'], 500);
        }
    
    }

    public function updateCliente(Request $request, $id)
    {
        // Validar la solicitud
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->id_cliente = $request->id_cliente;

        if ($user->save()) {
            return response()->json(['message' => 'Usuario actualizado']);
        } else {
            return response()->json(['error' => 'Error al actualizar el usuario'], 500);
        }
    
    }

    public function storeCliente(Request $request)
    {
        //Indicamos que solo queremos recibir name, email, id empleado y password de la request
        $data = $request->only('name', 'email','password', 'id_cliente');
        //Realizamos las validaciones
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50',
        ]);
        //Devolvemos un error si fallan las validaciones
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        //Creamos el nuevo usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'id_cliente' => $request->id_cliente,
        ]);
        //Nos guardamos el usuario y la contraseña para realizar la petición de token a JWTAuth
        $credentials = $request->only('email', 'password');
        //Devolvemos la respuesta con el token del usuario
        return response()->json([
            'message' => 'User created',
            'token' => JWTAuth::attempt($credentials),
            'user' => $user,
        ], Response::HTTP_OK);

    }

    public function updateEmpleado(Request $request, $id)
    {
        // Validar la solicitud
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->id_empleado = $request->id_empleado;

        if ($user->save()) {
            return response()->json(['message' => 'Usuario actualizado']);
        } else {
            return response()->json(['error' => 'Error al actualizar el usuario'], 500);
        }
    
    }

    public function storeEmpleado(Request $request)
    {
        //Indicamos que solo queremos recibir name, email y password de la request
        $data = $request->only('name', 'email', 'password', 'id_empleado');
        //Realizamos las validaciones
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50',
        ]);
        //Devolvemos un error si fallan las validaciones
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        //Creamos el nuevo usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'id_empleado' => $request->id_empleado,
        ]);
        //Nos guardamos el usuario y la contraseña para realizar la petición de token a JWTAuth
        $credentials = $request->only('email', 'password');
        //Devolvemos la respuesta con el token del usuario
        return response()->json([
            'message' => 'User created',
            'token' => JWTAuth::attempt($credentials),
            'user' => $user,
        ], Response::HTTP_OK);

    }
}



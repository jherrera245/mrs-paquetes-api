<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->only('name');

        $validator = Validator::make($data, [
            'name' => 'required|unique:roles,name',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        $role = Role::create($request->all());

        return response()->json($role, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $data = $request->only('name');

        $validator = Validator::make($data, [
            'name' => 'required|unique:roles,name,'.$role->id,
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        if ($role->update($request->all())) {
            return response()->json($role, 200);
        }

        response()->json(["error" => "Role is not updated"], 200);
    }

    public function assignPermissionsToRole(Request $request, $roleId)
    {
        // Verificar si el rol existe en la base de datos
        $role = Role::find($roleId);

        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }

        // Obtener los nombres de los permisos desde la solicitud
        $permissionName = $request->input('permission_name');

        
        $permission = Permission::where('name', $permissionName)->first();

        if ($permission) {
            $role->givePermissionTo($permission);
        } else {
            // Manejar el caso en el que el permiso no existe
            return response()->json(['message' => 'Error al agregar el permiso al rol'], 200);
        }

        return response()->json(['message' => 'Permisos agregados con exito'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        if ( $role->delete()) {
            return response()->json(["success" => "Role deleted successfully"], 200);
        }
        return response()->json(["error" => "Role is not deleted"], 400);
    }
}

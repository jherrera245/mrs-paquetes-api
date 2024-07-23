<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Crear permisos
        $permissions = [
            'auth-view_user',
            'auth-get_user_by_id',
            'auth-get_users',
            'auth-assign_user_role',
            'auth-assign_permissions_to_role',
            'auth-update',
            'auth-store',
            'auth-update',
            'auth-destroy',
            'roles-view',
            'roles-create',
            'roles-update',
            'roles-assign_permissions',
            'roles-destroy',
            'permission-view',
            'permission-create',
            'permission-update',
            'permission-destroy',
            'tipoPersona-view',
            'tipoPersona-create',
            'tipoPersona-update',
            'tipoPersona-destroy',
            'clientes-view',
            'clientes-create',
            'clientes-update',
            'clientes-destroy',
            'modeloVehiculo-view',
            'modeloVehiculo-show',
            'modeloVehiculo-create',
            'modeloVehiculo-update',
            'modeloVehiculo-destroy',
            'marcaVehiculo-view',
            'marcaVehiculo-show',
            'marcaVehiculo-create',
            'marcaVehiculo-update',
            'marcaVehiculo-destroy',
            'vehiculo-view',
            'vehiculo-show',
            'vehiculo-create',
            'vehiculo-update',
            'vehiculo-destroy',
            'empleados-view',
            'empleados-show',
            'empleados-create',
            'empleados-update',
            'empleados-destroy',
            'rutas-view',
            'rutas-show',
            'rutas-create',
            'rutas-update',
            'rutas-destroy',
            'direcciones-view',
            'direcciones-show',
            'direcciones-create',
            'direcciones-update',
            'direcciones-destroy',
            'destinos-view',
            'destinos-show',
            'destinos-create',
            'destinos-update',
            'destinos-destroy',
            'bodegas-view',
            'bodegas-show',
            'bodegas-create',
            'bodegas-update',
            'bodegas-destroy',
            'asignacionrutas-view',
            'asignacionrutas-show',
            'asignacionrutas-create',
            'asignacionrutas-update',
            'asignacionrutas-destroy',
            'paquete-view',
            'paquete-show',
            'paquete-create',
            'paquete-update',
            'paquete-destroy',
            'paquete-restore',
            'historialpaquetes-view',
            'historialpaquete-show',
            'incidencias-view',
            'incidencias-create',
            'incidencias-show',
            'incidencias-update',
            'incidencias-destroy',
        ];
        
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear un rol y asignar permisos (ejemplo de rol 'admin')
        $role = Role::find(1); //para admin

        // Asignar todos los permisos al rol 'admin'
        $role->givePermissionTo(Permission::all());
    }
}

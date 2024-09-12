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
            'auth-adminClienteRegistrar',
            'auth-actualizarClientePerfil',
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
            'orden-view',
            'orden-show',
            'orden-create',
            'orden-update',
            'orden-destroy',
            'orden-cliente',
            'mis-ordenes-cliente',
            'rutarecoleccion-view',
            'rutarecoleccion-show',
            'rutarecoleccion-create',
            'rutarecoleccion-update',
            'rutarecoleccion-destroy',
            'ordenrecoleccion-view',
            'ordenrecoleccion-show',
            'ordenrecoleccion-create',
            'ordenrecoleccion-update',
            'ordenrecoleccion-destroy',
             'ubicaciones-view',
             'ubicaciones-show',
             'ubicaciones-create',
             'ubicaciones-update',
             'ubicaciones-destroy',
             'ubicacionespaquetes-view',
             'ubicacionespaquetes-show',
             'ubicacionespaquetes-create',
             'ubicacionespaquetes-update',
             'ubicacionespaquetes-destroy',
             'pasillos-view',
             'pasillos-show',
             'pasillos-create',
             'pasillos-update',
             'pasillos-destroy',
             'traslados-view',
             'traslados-show',
             'traslados-create',
             'traslados-update',
             'traslados-destroy',
             'traslados-pdf',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Obtener los roles existentes
        $adminRole = Role::where('name', 'admin')->first();
        $clienteRole = Role::where('name', 'cliente')->first();
        $conductorRole = Role::where('name', 'conductor')->first();
        $basicoRole = Role::where('name', 'basico')->first();

        // Asignar permisos a los roles existentes
        if ($adminRole) {
            $adminRole->givePermissionTo(Permission::all());
        }

        if ($clienteRole) {
            $clienteRole->givePermissionTo([
                'auth-actualizarClientePerfil',
                'paquete-view',
                'incidencias-create',
                'incidencias-view',
                'direcciones-view',
                'direcciones-show',
                'direcciones-create',
                'direcciones-update',
                'direcciones-destroy',
                'orden-cliente',
                'mis-ordenes-cliente',
            ]);
        }

        if ($conductorRole) {
            $conductorRole->givePermissionTo([
                'rutas-view',
                'rutas-show',
                'rutas-create',
                'rutas-update',
                'rutas-destroy',
                'destinos-view',
                'destinos-show',
                'destinos-create',
                'destinos-update',
                'destinos-destroy',
                'direcciones-view',
                'direcciones-show',
                'direcciones-create',
                'direcciones-update',
                'direcciones-destroy',
                'paquete-view',
                'paquete-show',
                'paquete-create',
                'paquete-update',
                'paquete-destroy',
                'vehiculo-view', 
            ]);
        }

        if ($basicoRole) {
            $basicoRole->givePermissionTo([
                'paquete-view',
                'clientes-view',
            ]);
        }
    }
}

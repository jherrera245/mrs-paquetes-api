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
            'ubicacion-paquetes-danados-index',
            'ubicacion-paquetes-danados-store',
            'ubicacion-paquetes-danados-show',
            'ubicacion-paquetes-danados-update',
            'ubicacion-paquetes-danados-destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Obtener los roles existentes
        $adminRole = Role::where('name', 'admin')->first();
        $clienteRole = Role::where('name', 'cliente')->first();
        $conductorRole = Role::where('name', 'conductor')->first();
        $basicoRole = Role::where('name', 'basico')->first();

        $acompananteRole = Role::firstOrCreate(['name' => 'acompanante']);
        $supervisorDeEntregasRole = Role::firstOrCreate(['name' => 'supervisor_de_entregas']);
        $coordinadorDeRutasRole = Role::firstOrCreate(['name' => 'coordinador_de_rutas']);
        $operadorDeAlmacenRole = Role::firstOrCreate(['name' => 'operador_de_almacen']);
        $atencionAlClienteRole = Role::firstOrCreate(['name' => 'atencion_al_cliente']);
        $analistaDeLogisticaRole = Role::firstOrCreate(['name' => 'analista_de_logistica']);
        $gerenteDeOperacionesRole = Role::firstOrCreate(['name' => 'gerente_de_operaciones']);
        $tecnicoDeMantenimientoDeVehiculosRole = Role::firstOrCreate(['name' => 'tecnico_de_mantenimiento_de_vehiculos']);
        $recursosHumanosRole = Role::firstOrCreate(['name' => 'recursos_humanos']);

        // Asignar permisos a los roles existentes
        if ($adminRole) {
            $adminRole->givePermissionTo(Permission::all());
        }

        if ($clienteRole) {
            $clienteRole->givePermissionTo([
                'auth-actualizarClientePerfil',
                'paquete-view',
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
                'auth-store',
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
                'paquete-view',
                'paquete-show',
                'paquete-create',
                'paquete-update',
                'paquete-destroy',
                'vehiculo-view',
                'incidencias-view',
                'incidencias-create',
                'incidencias-show',
                'incidencias-update',
                'incidencias-destroy',
            ]);
        }

        if ($basicoRole) {
            $basicoRole->givePermissionTo([
                'paquete-view',
                'clientes-view',
            ]);
        }

        if ($acompananteRole) {
            $acompananteRole->givePermissionTo([
                'rutas-view',
                'paquete-view',
                'incidencias-create',
                'incidencias-view'
            ]);
        }

        if ($supervisorDeEntregasRole) {
            $supervisorDeEntregasRole->givePermissionTo([
                'empleados-view',
                'rutas-view',
                'paquete-view',
                'paquete-update',
                'incidencias-view',
                'incidencias-update',
                'orden-view'
            ]);
        }

        if ($coordinadorDeRutasRole) {
            $coordinadorDeRutasRole->givePermissionTo([
                'rutas-create',
                'rutas-update',
                'rutas-destroy',
                'ordenrecoleccion-create'
            ]);
        }

        if ($operadorDeAlmacenRole) {
            $operadorDeAlmacenRole->givePermissionTo([
                'bodegas-view',
                'bodegas-create',
                'bodegas-update',
                'bodegas-destroy',
                'traslados-view',
                'traslados-create'
            ]);
        }

        if ($atencionAlClienteRole) {
            $atencionAlClienteRole->givePermissionTo([
                'clientes-view',
                'orden-view',
                'orden-show',
                'incidencias-view'
            ]);
        }

        if ($analistaDeLogisticaRole) {
            $analistaDeLogisticaRole->givePermissionTo([
                'rutas-view',
                'orden-view',
                'traslados-view',
                'ubicaciones-view'
            ]);
        }

        if ($gerenteDeOperacionesRole) {
            $gerenteDeOperacionesRole->givePermissionTo([
                'roles-view',
                'roles-create',
                'roles-update',
                'empleados-view',
                'empleados-create',
                'empleados-update'
            ]);
        }

        if ($tecnicoDeMantenimientoDeVehiculosRole) {
            $tecnicoDeMantenimientoDeVehiculosRole->givePermissionTo([
                'vehiculo-view',
                'vehiculo-update',
                'vehiculo-destroy'
            ]);
        }

        if ($recursosHumanosRole) {
            $recursosHumanosRole->givePermissionTo([
                'empleados-view',
                'empleados-create',
                'empleados-update',
                'empleados-destroy'
            ]);
        }
    }
}

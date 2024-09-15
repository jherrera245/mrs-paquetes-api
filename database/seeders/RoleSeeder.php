<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Creación de roles si no existen
        $role1 = Role::firstOrCreate(['name' => 'admin']);
        $role2 = Role::firstOrCreate(['name' => 'cliente']);
        $role3 = Role::firstOrCreate(['name' => 'conductor']);
        $role2 = Role::firstOrCreate(['name' => 'acompanante']);
        $role3 = Role::firstOrCreate(['name' => 'supervisor_de_entregas']);
        $role4 = Role::firstOrCreate(['name' => 'coordinador_de_rutas']);
        $role5 = Role::firstOrCreate(['name' => 'operador_de_almacen']);
        $role6 = Role::firstOrCreate(['name' => 'atencion_al_cliente']);
        $role7 = Role::firstOrCreate(['name' => 'analista_de_logistica']);
        $role8 = Role::firstOrCreate(['name' => 'gerente_de_operaciones']);
        $role9 = Role::firstOrCreate(['name' => 'tecnico_de_mantenimiento_de_vehiculos']);
        $role10 = Role::firstOrCreate(['name' => 'recursos_humanos']);
        $role4 = Role::firstOrCreate(['name' => 'basico']);
    }
}

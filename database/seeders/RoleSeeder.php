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
        $role4 = Role::firstOrCreate(['name' => 'basico']);

        // Creación de permisos si no existen
        $permission1 = Permission::firstOrCreate(['name' => 'user.register'])->syncRoles([$role1, $role2]);
        $permission2 = Permission::firstOrCreate(['name' => 'user.update'])->syncRoles([$role1]);
        $permission3 = Permission::firstOrCreate(['name' => 'user.delete'])->syncRoles([$role1]);
        $permission4 = Permission::firstOrCreate(['name' => 'user.create'])->syncRoles([$role1]);
    }
}

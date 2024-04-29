<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Creacion de los roles a utilizar
        $role1 = Role::create(['name' => 'admin']);
        $role2 = Role::create(['name' => 'cliente']);
        $role3 = Role::create(['name' => 'conductor']);
        $role4 =Role::create(['name' => 'basico']);


        //Creacion de los permisos
        $permission1 = Permission::create(['name' => 'user.register'])->syncRoles([$role1, $role2]);
        $permission2 = Permission::create(['name' => 'user.update'])->syncRoles([$role1]);
        $permission3 = Permission::create(['name' => 'user.delete'])->syncRoles([$role1]);
        $permission4 = Permission::create(['name' => 'user.create'])->syncRoles([$role1]);
    }
}

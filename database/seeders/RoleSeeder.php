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
        $role1 = Role::firstOrCreate(['name' => 'Admin']);
        $role2 = Role::firstOrCreate(['name' => 'Cliente']);
        $role3 = Role::firstOrCreate(['name' => 'Conductor']);
        $role4 = Role::firstOrCreate(['name' => 'Basico']);
    }
}

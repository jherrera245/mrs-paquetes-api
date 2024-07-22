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
    }
}

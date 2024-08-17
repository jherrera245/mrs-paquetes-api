<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $id = DB::table('users')->insertGetId([
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin1234'),
        ]);

        $user = User::find($id);
        $role = Role::find(1);
        $user->roles()->detach();
        $user->assignRole($role);
    }
}

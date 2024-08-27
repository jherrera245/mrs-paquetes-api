<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        
        $users = [
            ['email' => 'ana.fernandez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'manuel.garcia@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'beatriz.lopez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'fernando.ruiz@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'claudia.martinez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'jose.hernandez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'marta.romero@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'pablo.gonzalez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'sandra.rodriguez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'miguel.diaz@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'laura.sanchez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'rafael.molina@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'susana.moreno@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'andres.castillo@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'veronica.blanco@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'luis.navarro@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'elena.gutierrez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'oscar.ramos@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'angela.fernandez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'francisco.martin@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'patricia.ortega@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'julio.alvarez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'carmen.santos@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'victor.fernandez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'lucia.perez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'ricardo.flores@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'alejandro.sanchez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'sonia.romero@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'raul.garcia@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ];
     

    
        // Inserta todos los usuarios en la tabla
        DB::table('users')->insert($users);

    }
}

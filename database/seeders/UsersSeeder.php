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
            ['email' => 'maria.gomez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'juan.perez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 2, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'pedro.lopez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 3, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'sofia.martinez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 4, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'ana.ramirez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 5, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'lucia.hernandez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 6, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'carlos.diaz@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 7, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'jorge.gonzalez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 8, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'marta.sanchez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 9, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'gabriel.ortiz@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 10, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'daniel.cruz@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 11, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'elena.reyes@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 12, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'alvaro.mendoza@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 13, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'rosa.flores@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 14, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'fernando.morales@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 15, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'sara.vega@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 16, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'raul.paz@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 17, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'laura.torres@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 18, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'mario.ramos@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 19, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'nuria.guzman@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 20, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'clara.vargas@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 21, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'pablo.castro@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 22, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'david.silva@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 23, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'irene.mendez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 24, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'javier.carrillo@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 25, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'cristina.lozano@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 26, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'esteban.cortes@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 27, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'antonio.campos@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 28, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'isabel.ibanez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 29, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'soledad.jimenez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => 30, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
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

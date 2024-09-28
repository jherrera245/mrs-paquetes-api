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
        
        // $users = [
        //     ['email' => 'ana.fernandez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'manuel.garcia@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'beatriz.lopez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'fernando.ruiz@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'claudia.martinez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'jose.hernandez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'marta.romero@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'pablo.gonzalez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'sandra.rodriguez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'miguel.diaz@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'laura.sanchez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'rafael.molina@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'susana.moreno@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'andres.castillo@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'veronica.blanco@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'luis.navarro@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'elena.gutierrez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'oscar.ramos@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'angela.fernandez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'francisco.martin@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'patricia.ortega@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'julio.alvarez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'carmen.santos@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'victor.fernandez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'lucia.perez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'ricardo.flores@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'alejandro.sanchez@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'sonia.romero@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'raul.garcia@gmail.com', 'password' => Hash::make('Mimesot@2024'), 'id_empleado' => null, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'mariagomez@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'juanperez@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 2, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'luismartinez@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 3, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'carlaramirez@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 4, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'pedrogonzalez@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 5, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'analopez@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 6, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'robertogarcia@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 7, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'angelaruiz@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 8, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'franciscorojas@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 9, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'marianamoreno@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 10, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'albertopena@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 11, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'luisafernandez@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 12, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'feliperamirez@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 13, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'silviarivera@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 14, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'jorgerodriguez@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 15, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'danielamartinez@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 16, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'ricardoromero@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 17, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'paulagarcia@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 18, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'fernandovazquez@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 19, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'gabrielatorres@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 20, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'vicentemartinez@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 21, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'lorenagomez@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 22, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'rafaelguerrero@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 23, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'elenasanchez@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 24, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'carlosmartin@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 25, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'inescampos@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 26, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'raulhernandez@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 27, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'martacastro@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 28, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'antonioruiz@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 29, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        //     ['email' => 'noeliaflores@gmail.com', 'password' => Hash::make('Mimesot@1'), 'id_empleado' => 30, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        // ];
     

    
        // // Inserta todos los usuarios en la tabla
        // DB::table('users')->insert($users);

    }
}

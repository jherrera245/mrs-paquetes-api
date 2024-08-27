<?php

namespace Database\Seeders;

use App\Models\TipoEntrega;
use App\Models\TipoPago;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{    /**
    * Seed the application's database.
    *
    * @return void
    */

    public function run()
    {
        DB::statement('ALTER TABLE pasillos AUTO_INCREMENT = 1;');
        
        // Lista de seeders a ejecutar
        $this->call([
            CargosTableSeeder::class,
            DepartamentoSeeder::class,
            EmpaquetadoSeeder::class,
            EstadoClientesSeeder::class,
            EstadoEmpleadosSeeder::class,
            EstadoIncidenciasSeeder::class,
            EstadoPaquetesSeeder::class,
            EstadoRutasSeeder::class,
            EstadoVehiculosSeeder::class,
            MunicipioSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            TipoIncidenciaSeeder::class,
            TipoPaqueteSeeder::class,
            TipoPersonaSeeder::class,
            EmpleadoSeeder::class,
            UsersSeeder::class,
            ClienteSeeder::class,
            DestinoSeeder::class,
            PaqueteSeeder::class,
            TipoPagoSeeder::class,
            TipoEntregaSeeder::class,
            VehicleSeeder::class,
            VehiculosSeeder::class,
            BodegaSeeder::class,
            PasilloSeeder::class
        ]);
    }
}

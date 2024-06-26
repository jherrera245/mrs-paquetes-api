<?php

namespace Database\Seeders;

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
        // Lista de seeders a ejecutar
        $this->call([
            RoleSeeder::class,
            DepartamentoSeeder::class,
            MunicipioSeeder::class,
            EstadoRutasSeeder::class,
            EstadoVehiculosSeeder::class,
            EstadoClientesSeeder::class,
            EstadoEmpleadosSeeder::class,
            EstadoIncidenciasSeeder::class,
            EstadoPaquetesSeeder::class,
            GeneroSeeder::class,
            TipoPersonaSeeder::class,
            TipoIncidenciaSeeder::class,
            TipoPaqueteSeeder::class,
            EmpaquetadoSeeder::class
        ]);
    }
}

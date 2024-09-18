<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarcaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('marcas')->insert([
            [
                'nombre' => 'Isuzu',
                'descripcion' => 'Famosa por sus camiones ligeros y pesados, reconocida por su durabilidad y eficiencia.',
            ],
            [
                'nombre' => 'Hyundai',
                'descripcion' => 'Líder en la fabricación de camiones y autobuses, conocida por su innovación y calidad.',
            ],
            [
                'nombre' => 'Honda',
                'descripcion' => 'Reconocida mundialmente por sus motocicletas de alto rendimiento y confiabilidad.',
            ],
        ]); 
    }
}

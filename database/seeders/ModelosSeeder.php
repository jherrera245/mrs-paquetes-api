<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modelos')->insert([
            [
                'nombre' => 'Isuzu N-Series',
                'descripcion' => 'Camión ligero ideal para la distribución urbana.',
                'id_marca' => 1, // Isuzu
            ],
            [
                'nombre' => 'HD45',
                'descripcion' => 'Camión robusto para transporte de carga pesada.',
                'id_marca' => 2, // hyundai
            ],
            [
                'nombre' => 'Honda Navy',
                'descripcion' => 'Motocicleta versátil y ágil, perfecta para la ciudad.',
                'id_marca' => 3, // Honda
            ],
        ]);
    }
}

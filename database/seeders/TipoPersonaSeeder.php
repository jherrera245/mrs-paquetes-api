<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoPersonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_persona')->insert([
            ['nombre' => 'Natural', 'descripcion' => 'titular personal'],
            ['nombre' => 'Juridica', 'descripcion' => 'titular sociedad'],
        ]);
    }
}

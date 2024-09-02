<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class TarifasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ["nombre"=>'Tarifa Rural'],
            ["nombre"=>'Tarifa Urbana'],
            ["nombre"=>'Tarifa Urbana Express'],
            ];
    
            DB::table('tarifas')->insert($data);
    }
}

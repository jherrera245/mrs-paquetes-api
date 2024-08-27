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
            ["nombre"=>'tarifa rural'],
            ["nombre"=>'tarifa urbana'],
            ["nombre"=>'tarifa urbana express'],
            ];
    
            DB::table('tarifas')->insert($data);
    }
}

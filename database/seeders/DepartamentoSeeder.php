<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $data = [
        ["nombre"=>'AHUACHAPAN'],
        ["nombre"=>'SANTA ANA'],
        ["nombre"=>'SONSONATE'],
        ["nombre"=>'CHALATENANGO'],
        ["nombre"=>'LA LIBERTAD'],
        ["nombre"=>'SAN SALVADOR'],
        ["nombre"=>'CUSCATLAN'],
        ["nombre"=>'LA PAZ'],
        ["nombre"=>'CABAÑAS'],
        ["nombre"=> 'SAN VICENTE'],
        ["nombre"=> 'USULUTAN'],
        ["nombre"=> 'SAN MIGUEL'],
        ["nombre"=> 'MORAZAN'],
        ["nombre"=> 'LA UNION']
        ];

        DB::table('departamento')->insert($data);
    }
}

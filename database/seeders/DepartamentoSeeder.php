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
        // Utiliza la función global `capitalizarPalabra` desde los helpers
        $data = [
            ["nombre" => capitalizarPalabra('AHUACHAPAN')],
            ["nombre" => capitalizarPalabra('SANTA ANA')],
            ["nombre" => capitalizarPalabra('SONSONATE')],
            ["nombre" => capitalizarPalabra('CHALATENANGO')],
            ["nombre" => capitalizarPalabra('LA LIBERTAD')],
            ["nombre" => capitalizarPalabra('SAN SALVADOR')],
            ["nombre" => capitalizarPalabra('CUSCATLAN')],
            ["nombre" => capitalizarPalabra('LA PAZ')],
            ["nombre" => capitalizarPalabra('CABAÑAS')],
            ["nombre" => capitalizarPalabra('SAN VICENTE')],
            ["nombre" => capitalizarPalabra('USULUTAN')],
            ["nombre" => capitalizarPalabra('SAN MIGUEL')],
            ["nombre" => capitalizarPalabra('MORAZAN')],
            ["nombre" => capitalizarPalabra('LA UNION')],
        ];

        DB::table('departamento')->insert($data);
    }
}


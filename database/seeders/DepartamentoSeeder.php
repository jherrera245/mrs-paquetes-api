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
        if (!function_exists('capitalizarPalabra')) {
            /**
             * Función personalizada para capitalizar correctamente palabras con tildes.
             *
             * @param string $string
             * @return string
             */
            function capitalizarPalabra($string)
            {
                return mb_convert_case(mb_strtolower($string), MB_CASE_TITLE, "UTF-8");
            }
        }

        // Utiliza la función global `capitalizarPalabra` desde los helpers
        $data = [
            ["nombre" => capitalizarPalabra('AHUACHAPÁN')],
            ["nombre" => capitalizarPalabra('SANTA ANA')],
            ["nombre" => capitalizarPalabra('SONSONATE')],
            ["nombre" => capitalizarPalabra('CHALATENANGO')],
            ["nombre" => capitalizarPalabra('LA LIBERTAD')],
            ["nombre" => capitalizarPalabra('SAN SALVADOR')],
            ["nombre" => capitalizarPalabra('CUSCATLÁN')],
            ["nombre" => capitalizarPalabra('LA PAZ')],
            ["nombre" => capitalizarPalabra('CABAÑAS')],
            ["nombre" => capitalizarPalabra('SAN VICENTE')],
            ["nombre" => capitalizarPalabra('USULUTÁN')],
            ["nombre" => capitalizarPalabra('SAN MIGUEL')],
            ["nombre" => capitalizarPalabra('MORAZÁN')],
            ["nombre" => capitalizarPalabra('LA UNIÓN')],
        ];

        DB::table('departamento')->insert($data);
    }
}

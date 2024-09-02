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
        // Función personalizada para capitalizar correctamente palabras con tildes
        function capitalizarPalabraConTildes($string)
        {
            return mb_convert_case(mb_strtolower($string), MB_CASE_TITLE, "UTF-8");
        }

        // Datos de prueba con formato adecuado de capitalización
        $data = [
            ["nombre" => capitalizarPalabraConTildes('AHUACHAPAN')],
            ["nombre" => capitalizarPalabraConTildes('SANTA ANA')],
            ["nombre" => capitalizarPalabraConTildes('SONSONATE')],
            ["nombre" => capitalizarPalabraConTildes('CHALATENANGO')],
            ["nombre" => capitalizarPalabraConTildes('LA LIBERTAD')],
            ["nombre" => capitalizarPalabraConTildes('SAN SALVADOR')],
            ["nombre" => capitalizarPalabraConTildes('CUSCATLAN')],
            ["nombre" => capitalizarPalabraConTildes('LA PAZ')],
            ["nombre" => capitalizarPalabraConTildes('CABAÑAS')],
            ["nombre" => capitalizarPalabraConTildes('SAN VICENTE')],
            ["nombre" => capitalizarPalabraConTildes('USULUTAN')],
            ["nombre" => capitalizarPalabraConTildes('SAN MIGUEL')],
            ["nombre" => capitalizarPalabraConTildes('MORAZAN')],
            ["nombre" => capitalizarPalabraConTildes('LA UNION')],
        ];

        DB::table('departamento')->insert($data);
    }
}

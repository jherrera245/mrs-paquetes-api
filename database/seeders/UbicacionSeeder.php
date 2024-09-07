<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UbicacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
     
        $ubicaciones = [
            ['nomenclatura' => 'B1P1AN1', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P2AN2', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P3AN3', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P1AN4', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P2AN5', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B2P4AN6', 'id_bodega' => 2, 'id_pasillo' => 4],
            ['nomenclatura' => 'B2P5AN7', 'id_bodega' => 2, 'id_pasillo' => 5],
            ['nomenclatura' => 'B2P6AN8', 'id_bodega' => 2, 'id_pasillo' => 6],
            ['nomenclatura' => 'B2P4AN9', 'id_bodega' => 2, 'id_pasillo' => 4],
            ['nomenclatura' => 'B2P5AN10', 'id_bodega' => 2, 'id_pasillo' => 5],
            ['nomenclatura' => 'B3P7AN11', 'id_bodega' => 3, 'id_pasillo' => 7],
            ['nomenclatura' => 'B3P8AN12', 'id_bodega' => 3, 'id_pasillo' => 8],
            ['nomenclatura' => 'B3P9AN13', 'id_bodega' => 3, 'id_pasillo' => 9],
            ['nomenclatura' => 'B3P7AN14', 'id_bodega' => 3, 'id_pasillo' => 7],
            ['nomenclatura' => 'B3P8AN15', 'id_bodega' => 3, 'id_pasillo' => 8],
        ];

        DB::table('ubicaciones')->insert($ubicaciones);
    }
}

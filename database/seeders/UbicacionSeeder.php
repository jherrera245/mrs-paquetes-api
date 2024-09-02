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
            ['nomenclatura' => 'UB001', 'id_bodega' => 1],
            ['nomenclatura' => 'UB002', 'id_bodega' => 1],
            ['nomenclatura' => 'UB003', 'id_bodega' => 1],
            ['nomenclatura' => 'UB004', 'id_bodega' => 1],
            ['nomenclatura' => 'UB005', 'id_bodega' => 1],
            ['nomenclatura' => 'UB006', 'id_bodega' => 2],
            ['nomenclatura' => 'UB007', 'id_bodega' => 2],
            ['nomenclatura' => 'UB008', 'id_bodega' => 2],
            ['nomenclatura' => 'UB009', 'id_bodega' => 2],
            ['nomenclatura' => 'UB010', 'id_bodega' => 2],
            ['nomenclatura' => 'UB011', 'id_bodega' => 3],
            ['nomenclatura' => 'UB012', 'id_bodega' => 3],
            ['nomenclatura' => 'UB013', 'id_bodega' => 3],
            ['nomenclatura' => 'UB014', 'id_bodega' => 3],
            ['nomenclatura' => 'UB015', 'id_bodega' => 3],
        ];

        DB::table('ubicaciones')->insert($ubicaciones);
    }
}

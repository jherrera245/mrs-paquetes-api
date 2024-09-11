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
            ['nomenclatura' => 'B1P1E1AN1', 'id_bodega' => 1, 'id_pasillo' => 1, 'ocupado' => 1],
            ['nomenclatura' => 'B1P1E1AN2', 'id_bodega' => 1, 'id_pasillo' => 1, 'ocupado' => 1],
            ['nomenclatura' => 'B1P1E1AN3', 'id_bodega' => 1, 'id_pasillo' => 1, 'ocupado' => 1],
            ['nomenclatura' => 'B1P1E1AN4', 'id_bodega' => 1, 'id_pasillo' => 1, 'ocupado' => 1],
            ['nomenclatura' => 'B1P1E1AN5', 'id_bodega' => 1, 'id_pasillo' => 1, 'ocupado' => 1],
            ['nomenclatura' => 'B1P1E1AN6', 'id_bodega' => 1, 'id_pasillo' => 1, 'ocupado' => 1],
            ['nomenclatura' => 'B1P1E1AN7', 'id_bodega' => 1, 'id_pasillo' => 1, 'ocupado' => 1],
            ['nomenclatura' => 'B1P1E1AN8', 'id_bodega' => 1, 'id_pasillo' => 1, 'ocupado' => 1],
            ['nomenclatura' => 'B1P1E1AN9', 'id_bodega' => 1, 'id_pasillo' => 1, 'ocupado' => 1],
            ['nomenclatura' => 'B1P1E1AN10', 'id_bodega' => 1, 'id_pasillo' => 1, 'ocupado' => 1],
            ['nomenclatura' => 'B1P1E1AN11', 'id_bodega' => 1, 'id_pasillo' => 1, 'ocupado' => 1],
            ['nomenclatura' => 'B1P1E1AN12', 'id_bodega' => 1, 'id_pasillo' => 1, 'ocupado' => 1],
            ['nomenclatura' => 'B1P1E1AN13', 'id_bodega' => 1, 'id_pasillo' => 1, 'ocupado' => 1],
            ['nomenclatura' => 'B1P1E1AN14', 'id_bodega' => 1, 'id_pasillo' => 1, 'ocupado' => 1],
            ['nomenclatura' => 'B1P1E1AN15', 'id_bodega' => 1, 'id_pasillo' => 1, 'ocupado' => 1],
            ['nomenclatura' => 'B1P1E1AN16', 'id_bodega' => 1, 'id_pasillo' => 1, 'ocupado' => 1],
            ['nomenclatura' => 'B1P1E1AN17', 'id_bodega' => 1, 'id_pasillo' => 1, 'ocupado' => 1],
            ['nomenclatura' => 'B1P1E1AN18', 'id_bodega' => 1, 'id_pasillo' => 1, 'ocupado' => 1],
            ['nomenclatura' => 'B1P1E1AN19', 'id_bodega' => 1, 'id_pasillo' => 1, 'ocupado' => 1],
            ['nomenclatura' => 'B1P1E1AN20', 'id_bodega' => 1, 'id_pasillo' => 1, 'ocupado' => 1],
        ];

        DB::table('ubicaciones')->insert($ubicaciones);
    }
}

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
            ['nomenclatura' => 'B1P1E1AN1SR', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN2SR', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN3SR', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN4SR', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN5SR', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN6SR', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN7SR', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN8SR', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN9SR', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN10SR', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN11SR', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN12SR', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN13SR', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN14SR', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN15SR', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN16SR', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN17SR', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN18SR', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN19SR', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN20SR', 'id_bodega' => 1, 'id_pasillo' => 1],

            ['nomenclatura' => 'B1P1E2AN1LU', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E2AN2LU', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E2AN3LU', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E2AN4LU', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E2AN5LU', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E2AN6LU', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E2AN7LU', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E2AN8LU', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E2AN9LU', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E2AN10LU', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E2AN11LU', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E2AN12LU', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E2AN13LU', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E2AN14LU', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E2AN15LU', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E2AN16LU', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E2AN17LU', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E2AN18LU', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E2AN19LU', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E2AN20LU', 'id_bodega' => 1, 'id_pasillo' => 1],

            ['nomenclatura' => 'B1P2E1AN1SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN2SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN3SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN4SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN5SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN6SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN7SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN8SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN9SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN10SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN11SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN12SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN13SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN14SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN15SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN16SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN17SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN18SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN19SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN20SM', 'id_bodega' => 1, 'id_pasillo' => 2],

            ['nomenclatura' => 'B1P2E2AN1SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E2AN2SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E2AN3SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E2AN4SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E2AN5SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E2AN6SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E2AN7SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E2AN8SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E2AN9SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E2AN10SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E2AN11SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E2AN12SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E2AN13SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E2AN14SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E2AN15SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E2AN16SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E2AN17SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E2AN18SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E2AN19SM', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E2AN20SM', 'id_bodega' => 1, 'id_pasillo' => 2],

            ['nomenclatura' => 'B1P3E1AN1MO', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN2MO', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN3MO', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN4MO', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN5MO', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN6MO', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN7MO', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN8MO', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN9MO', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN10MO', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN11MO', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN12MO', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN13MO', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN14MO', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN15MO', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN16MO', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN17MO', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN18MO', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN19MO', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN20MO', 'id_bodega' => 1, 'id_pasillo' => 3],

            ['nomenclatura' => 'B1P3E2AN1US', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E2AN2US', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E2AN3US', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E2AN4US', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E2AN5US', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E2AN6US', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E2AN7US', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E2AN8US', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E2AN9US', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E2AN10US', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E2AN11US', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E2AN12US', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E2AN13US', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E2AN14US', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E2AN15US', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E2AN16US', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E2AN17US', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E2AN18US', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E2AN19US', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E2AN20US', 'id_bodega' => 1, 'id_pasillo' => 3],


        ];

        DB::table('ubicaciones')->insert($ubicaciones);
    }
}

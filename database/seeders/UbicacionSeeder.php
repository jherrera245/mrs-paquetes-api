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
            // Ubicaciones para Pasillo P1__SANMIGUEL
            ['nomenclatura' => 'B1P1E1AN1_SANMIGUEL', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN2_SANMIGUEL', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN3_SANMIGUEL', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN4_SANMIGUEL', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN5_SANMIGUEL', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN6_SANMIGUEL', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN7_SANMIGUEL', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN8_SANMIGUEL', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN9_SANMIGUEL', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN10_SANMIGUEL', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN11_SANMIGUEL', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN12_SANMIGUEL', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN13_SANMIGUEL', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN14_SANMIGUEL', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN15_SANMIGUEL', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN16_SANMIGUEL', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN17_SANMIGUEL', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN18_SANMIGUEL', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN19_SANMIGUEL', 'id_bodega' => 1, 'id_pasillo' => 1],
            ['nomenclatura' => 'B1P1E1AN20_SANMIGUEL', 'id_bodega' => 1, 'id_pasillo' => 1],

            // Ubicaciones para Pasillo P2__SANTAROSA
            ['nomenclatura' => 'B1P2E1AN1_SANTAROSA', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN2_SANTAROSA', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN3_SANTAROSA', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN4_SANTAROSA', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN5_SANTAROSA', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN6_SANTAROSA', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN7_SANTAROSA', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN8_SANTAROSA', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN9_SANTAROSA', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN10_SANTAROSA', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN11_SANTAROSA', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN12_SANTAROSA', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN13_SANTAROSA', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN14_SANTAROSA', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN15_SANTAROSA', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN16_SANTAROSA', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN17_SANTAROSA', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN18_SANTAROSA', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN19_SANTAROSA', 'id_bodega' => 1, 'id_pasillo' => 2],
            ['nomenclatura' => 'B1P2E1AN20_SANTAROSA', 'id_bodega' => 1, 'id_pasillo' => 2],

            // Ubicaciones para Pasillo P3__MORAZAN
            ['nomenclatura' => 'B1P3E1AN1_MORAZAN', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN2_MORAZAN', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN3_MORAZAN', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN4_MORAZAN', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN5_MORAZAN', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN6_MORAZAN', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN7_MORAZAN', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN8_MORAZAN', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN9_MORAZAN', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN10_MORAZAN', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN11_MORAZAN', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN12_MORAZAN', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN13_MORAZAN', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN14_MORAZAN', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN15_MORAZAN', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN16_MORAZAN', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN17_MORAZAN', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN18_MORAZAN', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN19_MORAZAN', 'id_bodega' => 1, 'id_pasillo' => 3],
            ['nomenclatura' => 'B1P3E1AN20_MORAZAN', 'id_bodega' => 1, 'id_pasillo' => 3],

            // Ubicaciones para Pasillo P4__USULUTLAN
            ['nomenclatura' => 'B1P4E1AN1_USULUTLAN', 'id_bodega' => 1, 'id_pasillo' => 4],
            ['nomenclatura' => 'B1P4E1AN2_USULUTLAN', 'id_bodega' => 1, 'id_pasillo' => 4],
            ['nomenclatura' => 'B1P4E1AN3_USULUTLAN', 'id_bodega' => 1, 'id_pasillo' => 4],
            ['nomenclatura' => 'B1P4E1AN4_USULUTLAN', 'id_bodega' => 1, 'id_pasillo' => 4],
            ['nomenclatura' => 'B1P4E1AN5_USULUTLAN', 'id_bodega' => 1, 'id_pasillo' => 4],
            ['nomenclatura' => 'B1P4E1AN6_USULUTLAN', 'id_bodega' => 1, 'id_pasillo' => 4],
            ['nomenclatura' => 'B1P4E1AN7_USULUTLAN', 'id_bodega' => 1, 'id_pasillo' => 4],
            ['nomenclatura' => 'B1P4E1AN8_USULUTLAN', 'id_bodega' => 1, 'id_pasillo' => 4],
            ['nomenclatura' => 'B1P4E1AN9_USULUTLAN', 'id_bodega' => 1, 'id_pasillo' => 4],
            ['nomenclatura' => 'B1P4E1AN10_USULUTLAN', 'id_bodega' => 1, 'id_pasillo' => 4],
            ['nomenclatura' => 'B1P4E1AN11_USULUTLAN', 'id_bodega' => 1, 'id_pasillo' => 4],
            ['nomenclatura' => 'B1P4E1AN12_USULUTLAN', 'id_bodega' => 1, 'id_pasillo' => 4],
            ['nomenclatura' => 'B1P4E1AN13_USULUTLAN', 'id_bodega' => 1, 'id_pasillo' => 4],
            ['nomenclatura' => 'B1P4E1AN14_USULUTLAN', 'id_bodega' => 1, 'id_pasillo' => 4],
            ['nomenclatura' => 'B1P4E1AN15_USULUTLAN', 'id_bodega' => 1, 'id_pasillo' => 4],
            ['nomenclatura' => 'B1P4E1AN16_USULUTLAN', 'id_bodega' => 1, 'id_pasillo' => 4],
            ['nomenclatura' => 'B1P4E1AN17_USULUTLAN', 'id_bodega' => 1, 'id_pasillo' => 4],
            ['nomenclatura' => 'B1P4E1AN18_USULUTLAN', 'id_bodega' => 1, 'id_pasillo' => 4],
            ['nomenclatura' => 'B1P4E1AN19_USULUTLAN', 'id_bodega' => 1, 'id_pasillo' => 4],
            ['nomenclatura' => 'B1P4E1AN20_USULUTLAN', 'id_bodega' => 1, 'id_pasillo' => 4],

            // Ubicaciones para Pasillo P5__LAUNION
            ['nomenclatura' => 'B1P5E1AN1_LAUNION', 'id_bodega' => 1, 'id_pasillo' => 5],
            ['nomenclatura' => 'B1P5E1AN2_LAUNION', 'id_bodega' => 1, 'id_pasillo' => 5],
            ['nomenclatura' => 'B1P5E1AN3_LAUNION', 'id_bodega' => 1, 'id_pasillo' => 5],
            ['nomenclatura' => 'B1P5E1AN4_LAUNION', 'id_bodega' => 1, 'id_pasillo' => 5],
            ['nomenclatura' => 'B1P5E1AN5_LAUNION', 'id_bodega' => 1, 'id_pasillo' => 5],
            ['nomenclatura' => 'B1P5E1AN6_LAUNION', 'id_bodega' => 1, 'id_pasillo' => 5],
            ['nomenclatura' => 'B1P5E1AN7_LAUNION', 'id_bodega' => 1, 'id_pasillo' => 5],
            ['nomenclatura' => 'B1P5E1AN8_LAUNION', 'id_bodega' => 1, 'id_pasillo' => 5],
            ['nomenclatura' => 'B1P5E1AN9_LAUNION', 'id_bodega' => 1, 'id_pasillo' => 5],
            ['nomenclatura' => 'B1P5E1AN10_LAUNION', 'id_bodega' => 1, 'id_pasillo' => 5],
            ['nomenclatura' => 'B1P5E1AN11_LAUNION', 'id_bodega' => 1, 'id_pasillo' => 5],
            ['nomenclatura' => 'B1P5E1AN12_LAUNION', 'id_bodega' => 1, 'id_pasillo' => 5],
            ['nomenclatura' => 'B1P5E1AN13_LAUNION', 'id_bodega' => 1, 'id_pasillo' => 5],
            ['nomenclatura' => 'B1P5E1AN14_LAUNION', 'id_bodega' => 1, 'id_pasillo' => 5],
            ['nomenclatura' => 'B1P5E1AN15_LAUNION', 'id_bodega' => 1, 'id_pasillo' => 5],
            ['nomenclatura' => 'B1P5E1AN16_LAUNION', 'id_bodega' => 1, 'id_pasillo' => 5],
            ['nomenclatura' => 'B1P5E1AN17_LAUNION', 'id_bodega' => 1, 'id_pasillo' => 5],
            ['nomenclatura' => 'B1P5E1AN18_LAUNION', 'id_bodega' => 1, 'id_pasillo' => 5],
            ['nomenclatura' => 'B1P5E1AN19_LAUNION', 'id_bodega' => 1, 'id_pasillo' => 5],
            ['nomenclatura' => 'B1P5E1AN20_LAUNION', 'id_bodega' => 1, 'id_pasillo' => 5],

             // Ubicaciones para Pasillo P6 (Nueva Ubicación de DAÑADO)
             ['nomenclatura' => 'B1P6E1AN1DA', 'id_bodega' => 1, 'id_pasillo' => 6],
             ['nomenclatura' => 'B1P6E1AN2DA', 'id_bodega' => 1, 'id_pasillo' => 6],
             ['nomenclatura' => 'B1P6E1AN3DA', 'id_bodega' => 1, 'id_pasillo' => 6],
             ['nomenclatura' => 'B1P6E1AN4DA', 'id_bodega' => 1, 'id_pasillo' => 6],
             ['nomenclatura' => 'B1P6E1AN5DA', 'id_bodega' => 1, 'id_pasillo' => 6],
             ['nomenclatura' => 'B1P6E1AN6DA', 'id_bodega' => 1, 'id_pasillo' => 6],
             ['nomenclatura' => 'B1P6E1AN7DA', 'id_bodega' => 1, 'id_pasillo' => 6],
             ['nomenclatura' => 'B1P6E1AN8DA', 'id_bodega' => 1, 'id_pasillo' => 6],
             ['nomenclatura' => 'B1P6E1AN9DA', 'id_bodega' => 1, 'id_pasillo' => 6],
             ['nomenclatura' => 'B1P6E1AN10DA', 'id_bodega' => 1, 'id_pasillo' => 6],
             ['nomenclatura' => 'B1P6E1AN11DA', 'id_bodega' => 1, 'id_pasillo' => 6],
             ['nomenclatura' => 'B1P6E1AN12DA', 'id_bodega' => 1, 'id_pasillo' => 6],
             ['nomenclatura' => 'B1P6E1AN13DA', 'id_bodega' => 1, 'id_pasillo' => 6],
             ['nomenclatura' => 'B1P6E1AN14DA', 'id_bodega' => 1, 'id_pasillo' => 6],
             ['nomenclatura' => 'B1P6E1AN15DA', 'id_bodega' => 1, 'id_pasillo' => 6],
             ['nomenclatura' => 'B1P6E1AN16DA', 'id_bodega' => 1, 'id_pasillo' => 6],
             ['nomenclatura' => 'B1P6E1AN17DA', 'id_bodega' => 1, 'id_pasillo' => 6],
             ['nomenclatura' => 'B1P6E1AN18DA', 'id_bodega' => 1, 'id_pasillo' => 6],
             ['nomenclatura' => 'B1P6E1AN19DA', 'id_bodega' => 1, 'id_pasillo' => 6],
             ['nomenclatura' => 'B1P6E1AN20DA', 'id_bodega' => 1, 'id_pasillo' => 6],
        ];

        // Insertar las ubicaciones en la tabla 'ubicaciones'
        DB::table('ubicaciones')->insert($ubicaciones);
    }
}

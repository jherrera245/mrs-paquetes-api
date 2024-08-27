<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TarifaDestinoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tarifas = [
            1 => 'Tarifa Urbana',
            2 => 'Tarifa Rural',
            3 => 'Tarifa Express-Zona Urbana'
        ];

        $tamanoPaquete = [
            1 => 'Paquete pequeño',
            2 => 'Paquete mediano',
            3 => 'Paquete grande'
        ];

        // Usulután (solo municipio 198 con tarifa Urbana)
        $this->insertDataForDepartamento(11, range(176, 198), $tarifas, $tamanoPaquete, 'usulutan', false, null, 198);

        // San Miguel (solo municipio 215 con tarifa Urbana y Express)
        $this->insertDataForDepartamento(12, range(199, 218), $tarifas, $tamanoPaquete, 'san_miguel', true, 215);

        // Morazán (solo municipio 237 con tarifa Urbana)
        $this->insertDataForDepartamento(13, range(219, 244), $tarifas, $tamanoPaquete, 'morazan', false, null, 237);

        // La Unión (solo municipio 252 con tarifa Urbana)
        $this->insertDataForDepartamento(14, range(245, 262), $tarifas, $tamanoPaquete, 'la_union', false, null, 252);
    }

    private function insertDataForDepartamento($departamentoId, $municipios, $tarifas, $tamanoPaquete, $departamento, $hasExpress, $expressMunicipioId = null, $urbanaMunicipioId = null)
    {
        foreach ($municipios as $municipioId) {
            if ($departamentoId == 11 && $municipioId == $urbanaMunicipioId) {
                // Tarifa Urbana para el municipio específico en Usulután
                foreach ($tamanoPaquete as $tamanoId => $descripcion) {
                    DB::table('tarifas_destinos')->insert([
                        'id_tarifa' => 2, // Tarifa Urbana
                        'id_tamano_paquete' => $tamanoId,
                        'id_departamento' => $departamentoId,
                        'id_municipio' => $municipioId,
                        'monto' => $this->getMonto($departamento, 'urbana', $tamanoId),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } elseif ($departamentoId == 12) {
                // Tarifa Urbana y Express-Zona Urbana para el municipio específico en San Miguel
                foreach ($tamanoPaquete as $tamanoId => $descripcion) {
                    if ($municipioId == $expressMunicipioId) {
                        DB::table('tarifas_destinos')->insert([
                            'id_tarifa' => 2, // Tarifa Urbana
                            'id_tamano_paquete' => $tamanoId,
                            'id_departamento' => $departamentoId,
                            'id_municipio' => $municipioId,
                            'monto' => $this->getMonto($departamento, 'urbana', $tamanoId),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        if ($hasExpress && ($tamanoId == 1 || $tamanoId == 2)) { // Solo paquetes pequeños y medianos para Express
                            DB::table('tarifas_destinos')->insert([
                                'id_tarifa' => 3, // Tarifa Express-Zona Urbana
                                'id_tamano_paquete' => $tamanoId,
                                'id_departamento' => $departamentoId,
                                'id_municipio' => $municipioId,
                                'monto' => $this->getMontoExpress($tamanoId),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    } else {
                        // Tarifa Rural para otros municipios en San Miguel
                        DB::table('tarifas_destinos')->insert([
                            'id_tarifa' => 1, // Tarifa Rural
                            'id_tamano_paquete' => $tamanoId,
                            'id_departamento' => $departamentoId,
                            'id_municipio' => $municipioId,
                            'monto' => $this->getMonto($departamento, 'rural', $tamanoId),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            } elseif ($departamentoId == 13 && $municipioId == $urbanaMunicipioId) {
                // Tarifa Urbana para el municipio específico en Morazán
                foreach ($tamanoPaquete as $tamanoId => $descripcion) {
                    DB::table('tarifas_destinos')->insert([
                        'id_tarifa' => 2, // Tarifa Urbana
                        'id_tamano_paquete' => $tamanoId,
                        'id_departamento' => $departamentoId,
                        'id_municipio' => $municipioId,
                        'monto' => $this->getMonto($departamento, 'urbana', $tamanoId),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } elseif ($departamentoId == 14 && $municipioId == $urbanaMunicipioId) {
                // Tarifa Urbana para el municipio específico en La Unión
                foreach ($tamanoPaquete as $tamanoId => $descripcion) {
                    DB::table('tarifas_destinos')->insert([
                        'id_tarifa' => 2, // Tarifa Urbana
                        'id_tamano_paquete' => $tamanoId,
                        'id_departamento' => $departamentoId,
                        'id_municipio' => $municipioId,
                        'monto' => $this->getMonto($departamento, 'urbana', $tamanoId),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                // Tarifa Rural para todos los demás municipios
                foreach ($tamanoPaquete as $tamanoId => $descripcion) {
                    DB::table('tarifas_destinos')->insert([
                        'id_tarifa' => 1, // Tarifa Rural
                        'id_tamano_paquete' => $tamanoId,
                        'id_departamento' => $departamentoId,
                        'id_municipio' => $municipioId,
                        'monto' => $this->getMonto($departamento, 'rural', $tamanoId),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Tarifa Express-Zona Urbana para el municipio específico en San Miguel
                if ($hasExpress && $municipioId == $expressMunicipioId) {
                    foreach ($tamanoPaquete as $tamanoId => $descripcion) {
                        if ($tamanoId == 1 || $tamanoId == 2) { // Solo paquetes pequeños y medianos para Express
                            DB::table('tarifas_destinos')->insert([
                                'id_tarifa' => 3, // Tarifa Express-Zona Urbana
                                'id_tamano_paquete' => $tamanoId,
                                'id_departamento' => $departamentoId,
                                'id_municipio' => $municipioId,
                                'monto' => $this->getMontoExpress($tamanoId),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }
        }
    }

    private function getMonto($departamento, $tipo, $tamanoId)
    {
        $montos = [
            'usulutan' => [
                'urbana' => [1 => 6.00, 2 => 7.00, 3 => 8.50],
                'rural' => [1 => 8.00, 2 => 9.00, 3 => 10.50],
            ],
            'san_miguel' => [
                'urbana' => [1 => 5.00, 2 => 6.00, 3 => 7.50],
                'rural' => [1 => 7.00, 2 => 8.00, 3 => 9.50],
            ], 
            'morazan' => [
                'urbana' => [1 => 7.00, 2 => 8.00, 3 => 9.50],
                'rural' => [1 => 9.00, 2 => 10.00, 3 => 11.50],
            ],
            'la_union' => [
                'urbana' => [1 => 6.00, 2 => 7.00, 3 => 8.50],
                'rural' => [1 => 8.00, 2 => 9.00, 3 => 10.50],
            ],
        ];

        return $montos[$departamento][$tipo][$tamanoId] ?? 0;
    }

    private function getMontoExpress($tamanoId)
    {
        $montos = [
            1 => 7.00, // Paquete pequeño
            2 => 8.00, // Paquete mediano
        ];
        return $montos[$tamanoId] ?? 0;
    }
}

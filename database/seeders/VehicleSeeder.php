<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Marcas y modelos de camiones
        $truckBrands = [
            ['Kia', ['K2700', 'K3000S', 'K4000G']],
            ['JMC', ['Carrying Plus', 'Conquer', 'Vigus']],
            ['Isuzu', ['NPR', 'FRR', 'D-MAX']],
        ];

        // Marcas y modelos de motos
        $motorcycleBrands = [
            ['Yamaha', ['YZF-R3', 'MT-07', 'FZ-09']],
            ['Honda', ['CBR500R', 'CB650R', 'Rebel 500']],
            ['Kawasaki', ['Ninja 400', 'Z650', 'Vulcan S']],
        ];

        // Insertar marcas y modelos de camiones
        foreach ($truckBrands as [$nombreMarca, $modelos]) {
            $marcaId = DB::table('marcas')->insertGetId([
                'nombre' => $nombreMarca,
                'descripcion' => $this->getDescriptionByBrand($nombreMarca)
            ]);

            foreach ($modelos as $nombreModelo) {
                DB::table('modelos')->insert([
                    'nombre' => $nombreModelo,
                    'descripcion' => $this->getDescriptionByModel($nombreMarca, $nombreModelo),
                    'id_marca' => $marcaId
                ]);
            }
        }

        // Insertar marcas y modelos de motos
        foreach ($motorcycleBrands as [$nombreMarca, $modelos]) {
            $marcaId = DB::table('marcas')->insertGetId([
                'nombre' => $nombreMarca,
                'descripcion' => $this->getDescriptionByBrand($nombreMarca)
            ]);

            foreach ($modelos as $nombreModelo) {
                DB::table('modelos')->insert([
                    'nombre' => $nombreModelo,
                    'descripcion' => $this->getDescriptionByModel($nombreMarca, $nombreModelo),
                    'id_marca' => $marcaId
                ]);
            }
        }
    }

    private function getDescriptionByBrand($brandName)
    {
        $descriptions = [
            'Kia' => "Camiones compactos y eficientes con buena capacidad de carga.",
            'JMC' => "Camiones económicos y duraderos para carga ligera.",
            'Isuzu' => "Camiones robustos con alta capacidad de carga.",
            'Yamaha' => "Motocicletas confiables con gran rendimiento.",
            'Honda' => "Motos versátiles con diseño enfocado en el conductor.",
            'Kawasaki' => "Motos de alto rendimiento con estilo agresivo."
        ];

        return $descriptions[$brandName] ?? "Descripción no disponible";
    }

    private function getDescriptionByModel($brandName, $modelName)
    {
        $descriptions = [
            'Kia' => [
                'K2700' => "Camión ligero, eficiente para la ciudad, buena carga.",
                'K3000S' => "Mayor capacidad de carga y manejo ágil.",
                'K4000G' => "Ideal para cargas pesadas con chasis fuerte."
            ],
            'JMC' => [
                'Carrying Plus' => "Económico y versátil, carga moderada.",
                'Conquer' => "Robusto, diseñado para condiciones difíciles.",
                'Vigus' => "Compacto, moderno y ágil para la ciudad."
            ],
            'Isuzu' => [
                'NPR' => "Camión mediano, confiable para distribución urbana.",
                'FRR' => "Mayor carga y motor potente para largos viajes.",
                'D-MAX' => "Camioneta versátil, mezcla de confort y carga."
            ],
            'Yamaha' => [
                'YZF-R3' => "Moto deportiva ligera, fácil de manejar.",
                'MT-07' => "Versátil, potente, ideal para cualquier camino.",
                'FZ-09' => "Diseño agresivo con rendimiento sobresaliente."
            ],
            'Honda' => [
                'CBR500R' => "Deportiva balanceada, buena potencia y control.",
                'CB650R' => "Moto naked ágil con rendimiento sólido.",
                'Rebel 500' => "Cruiser clásica con tecnología moderna."
            ],
            'Kawasaki' => [
                'Ninja 400' => "Deportiva accesible con buen manejo.",
                'Z650' => "Naked ágil con rendimiento dinámico.",
                'Vulcan S' => "Cruiser personalizable, confort y estilo."
            ],
        ];

        return $descriptions[$brandName][$modelName] ?? "Descripción del modelo no disponible";
    }
}

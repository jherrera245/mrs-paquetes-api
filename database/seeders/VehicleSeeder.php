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
        $brands = [
            ['Toyota', ['Corolla', 'Camry', 'RAV4', 'Prius', 'Hilux']],
            ['Ford', ['F-150', 'Mustang', 'Explorer', 'Escape', 'Focus']],
            ['Chevrolet', ['Silverado', 'Malibu', 'Camaro', 'Equinox', 'Tahoe']],
            ['Honda', ['Civic', 'Accord', 'CR-V', 'Fit', 'Pilot']],
            ['Nissan', ['Frontier', 'Sentra', 'Versa', 'Rogue', 'Murano']],
            ['Volkswagen', ['Golf', 'Passat', 'Tiguan', 'Jetta', 'Beetle']],
            ['Hyundai', ['Elantra', 'Sonata', 'Santa Fe', 'Tucson', 'Accent']],
            ['BMW', ['Serie 3', 'Serie 5', 'X3', 'X5', 'Serie 1']],
            ['Mercedes-Benz', ['Clase C', 'Clase E', 'Clase S', 'GLC', 'GLE']],
            ['Audi', ['A3', 'A4', 'A6', 'Q5', 'Q7']]
        ];

        foreach ($brands as [$nombreMarca, $modelos]) {
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
            'Toyota' => "Toyota es conocida por su durabilidad y alta calidad en condiciones de manejo diversas.",
            'Ford' => "Ford se destaca en la innovación con vehículos que combinan rendimiento y eficiencia energética.",
            'Chevrolet' => "Chevrolet ofrece una amplia variedad de vehículos desde coches deportivos hasta camiones robustos.",
            'Honda' => "Honda tiene una reputación de confiabilidad y diseño orientado al conductor.",
            'Nissan' => "Nissan es apreciada por su diseño moderno y tecnología accesible en automoción.",
            'Volkswagen' => "Volkswagen es sinónimo de ingeniería alemana y eficiencia, con un toque de lujo accesible.",
            'Hyundai' => "Hyundai se ha hecho un nombre por su excepcional garantía y características innovadoras a precios competitivos.",
            'BMW' => "BMW ofrece una experiencia de lujo con vehículos que destacan por su desempeño y tecnología avanzada.",
            'Mercedes-Benz' => "Mercedes-Benz es el epitome del lujo automotriz, ofreciendo alto rendimiento y comodidad inigualable.",
            'Audi' => "Audi es conocida por sus interiores de alta calidad y sistemas de conducción quattro all-wheel."
        ];

        return $descriptions[$brandName] ?? "Descripción no disponible";
    }

    private function getDescriptionByModel($brandName, $modelName)
    {
        $descriptions = [
            'Volkswagen' => [
                'Golf' => "Volkswagen Golf es reconocido por su versatilidad y rendimiento sólido, ideal para el entusiasta del automovilismo.",
                'Passat' => "El Volkswagen Passat combina comodidad y tecnología en un paquete elegante de sedán.",
                'Tiguan' => "Volkswagen Tiguan ofrece un diseño espacioso y características inteligentes, perfecto para la familia moderna.",
                'Jetta' => "El Volkswagen Jetta es apreciado por su manejo refinado y su diseño elegante, un sedán accesible con carácter de lujo.",
                'Beetle' => "El icónico Volkswagen Beetle ofrece un estilo único con una herencia rica y emocionante."
            ],
            'Hyundai' => [
                'Elantra' => "Hyundai Elantra destaca por su diseño moderno y eficiencia en consumo de combustible, ideal para la conducción diaria.",
                'Sonata' => "El Hyundai Sonata es un sedán que combina diseño avanzado, seguridad y confort a un precio competitivo.",
                'Santa Fe' => "Hyundai Santa Fe es un SUV versátil con tecnología de punta y espacio suficiente para toda la familia.",
                'Tucson' => "El Hyundai Tucson ofrece una combinación de diseño, eficiencia y tecnología, ideal para la aventura urbana.",
                'Accent' => "Hyundai Accent es un compacto económico que no escatima en estilo ni en características prácticas."
            ],
            'BMW' => [
                'Serie 3' => "BMW Serie 3 es un sedán deportivo que ofrece dinámica de manejo líder en su clase y tecnología innovadora.",
                'Serie 5' => "El BMW Serie 5 es un sedán de lujo que combina confort y tecnología con un rendimiento potente.",
                'X3' => "BMW X3 es un SUV de lujo compacto que ofrece versatilidad y rendimiento sin sacrificar el confort.",
                'X5' => "El BMW X5 es un SUV de lujo que ofrece espacio, potencia y tecnología avanzada para una experiencia de conducción superior.",
                'Serie 1' => "BMW Serie 1 es un hatchback deportivo con la esencia de lujo y rendimiento que caracteriza a BMW."
            ],
            'Mercedes-Benz' => [
                'Clase C' => "Mercedes-Benz Clase C es un sedán de lujo que ofrece estilo, sofisticación y tecnología avanzada.",
                'Clase E' => "El Mercedes-Benz Clase E es sinónimo de lujo y rendimiento con un diseño elegante y tecnología de punta.",
                'Clase S' => "Mercedes-Benz Clase S define el lujo en automoción, ofreciendo una experiencia inigualable en términos de confort, estilo y rendimiento.",
                'GLC' => "El Mercedes-Benz GLC es un SUV de lujo compacto que ofrece estilo y versatilidad con tecnología avanzada.",
                'GLE' => "Mercedes-Benz GLE es un SUV de lujo que combina espacio, confort y tecnología para la conducción diaria o las aventuras más ambiciosas."
            ],
            'Audi' => [
                'A3' => "Audi A3 es un compacto premium que ofrece diseño y tecnología en un paquete accesible.",
                'A4' => "El Audi A4 es un sedán de lujo que combina rendimiento y eficiencia con un diseño elegante.",
                'A6' => "Audi A6 es un sedán de lujo que ofrece un equilibrio perfecto entre rendimiento, tecnología y diseño sofisticado.",
                'Q5' => "El Audi Q5 es un SUV compacto que ofrece la combinación perfecta de estilo, confort y versatilidad.",
                'Q7' => "Audi Q7 es el SUV de lujo grande que ofrece espacio, potencia y tecnología avanzada para toda la familia."
            ],
            'Toyota' => [
                'Corolla' => "El Toyota Corolla es un sedán compacto líder en ventas, conocido por su fiabilidad y eficiencia.",
                'Camry' => "Toyota Camry es un sedán mediano que ofrece un cómodo interior y un rendimiento eficiente.",
                'RAV4' => "El Toyota RAV4 es un SUV versátil que combina robustez y tecnología, ideal para cualquier aventura.",
                'Prius' => "Toyota Prius es un pionero en vehículos híbridos, ofreciendo economía de combustible y diseño eco-amigable.",
                'Hilux' => "La Toyota Hilux es una camioneta robusta conocida por su durabilidad y capacidad off-road."
            ],
            'Ford' => [
                'F-150' => "Ford F-150 es una camioneta full-size que destaca por su capacidad de carga y robustez.",
                'Mustang' => "El Ford Mustang es un icono americano con un rendimiento impresionante y estilo deportivo.",
                'Explorer' => "Ford Explorer es un SUV familiar que ofrece amplio espacio y tecnología avanzada para viajes seguros.",
                'Escape' => "El Ford Escape es un SUV compacto con tecnología moderna y eficiencia de combustible.",
                'Focus' => "Ford Focus es un vehículo compacto que ofrece maniobrabilidad y economía en un diseño práctico."
            ],
            'Chevrolet' => [
                'Silverado' => "Chevrolet Silverado es una de las camionetas más potentes y confiables del mercado.",
                'Malibu' => "El Chevrolet Malibu es un sedán eficiente y elegante, perfecto para la conducción diaria.",
                'Camaro' => "Chevrolet Camaro es un coche deportivo con potencia y diseño agresivo, un verdadero ícono de la carretera.",
                'Equinox' => "Chevrolet Equinox es un SUV mediano que ofrece seguridad y confort con un diseño versátil.",
                'Tahoe' => "Chevrolet Tahoe es un SUV grande que proporciona espacio amplio y tecnología para viajes en familia."
            ],
            'Honda' => [
                'Civic' => "Honda Civic es un compacto que combina estilo, eficiencia y tecnología de punta.",
                'Accord' => "Honda Accord es un sedán confiable y espacioso, ideal para familias y profesionales.",
                'CR-V' => "Honda CR-V es un SUV de tamaño mediano, perfecto para quienes buscan seguridad y comodidad.",
                'Fit' => "Honda Fit es un hatchback pequeño y versátil, conocido por su espacio interior inteligente y eficiencia.",
                'Pilot' => "Honda Pilot es un SUV grande con tres filas de asientos, ideal para familias grandes y viajes largos."
            ],
            'Nissan' => [
                'Frontier' => "Nissan Frontier es una camioneta que combina durabilidad y rendimiento en un paquete económico.",
                'Sentra' => "Nissan Sentra es un sedán compacto que destaca por su comodidad y eficiencia en el consumo de combustible.",
                'Versa' => "El Nissan Versa es conocido por su valor excepcional, combinando economía y espacio en un diseño compacto.",
                'Rogue' => "Nissan Rogue es un SUV crossover versátil con tecnología avanzada y un diseño atractivo.",
                'Murano' => "Nissan Murano es un SUV que ofrece lujo y rendimiento con un diseño sofisticado y confort premium."
            ]
        ];

        return $descriptions[$brandName][$modelName] ?? "Descripción del modelo no disponible";
    }
}

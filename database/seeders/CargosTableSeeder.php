<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CargosTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('cargos')->insert([
            ['nombre' => 'Motorista', 'descripcion' => 'Encargado de conducir y entregar los paquetes de...'],
            ['nombre' => 'Acompañante', 'descripcion' => 'Brinda apoyo al motorista en la entrega de los paq...'],
            ['nombre' => 'Supervisor de Entregas', 'descripcion' => 'Responsable de supervisar las operaciones de entre...'],
            ['nombre' => 'Coordinador de Rutas', 'descripcion' => 'Encargado de planificar las rutas de entrega y coo...'],
            ['nombre' => 'Operador de Almacén', 'descripcion' => 'Responsable de recibir, almacenar y preparar los p...'],
            ['nombre' => 'Atención al Cliente', 'descripcion' => 'Encargado de brindar atención y soporte a los clie...'],
            ['nombre' => 'Analista de Logística', 'descripcion' => 'Encargado de analizar y optimizar los procesos log...'],
            ['nombre' => 'Gerente de Operaciones', 'descripcion' => 'Responsable de la gestión y dirección de todas las...'],
            ['nombre' => 'Técnico de Mantenimiento de Vehículos', 'descripcion' => 'Encargado del mantenimiento y reparación de los ve...'],
            ['nombre' => 'Recursos Humanos', 'descripcion' => 'Encargado de la selección, contratación y gestión ...']
        ]);
    }
}

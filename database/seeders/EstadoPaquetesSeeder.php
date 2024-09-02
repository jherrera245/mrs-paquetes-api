<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoPaquetesSeeder extends Seeder
{
    public function run()
    {
        DB::table('estado_paquetes')->insert([
            ['nombre' => 'En Recepción', 'descripcion' => 'El paquete está en Recepción'],
            ['nombre' => 'En Bodega', 'descripcion' => 'El paquete se encuentra en la bodega'],
            ['nombre' => 'En Espera de Recolección', 'descripcion' => 'El paquete está listo para ser recolectado'],
            ['nombre' => 'En Tránsito', 'descripcion' => 'El paquete está en tránsito'],
            ['nombre' => 'En Ruta de Entrega', 'descripcion' => 'El paquete está en camino para su entrega'],
            ['nombre' => 'Reprogramado', 'descripcion' => 'La entrega del paquete ha sido reprogramada'],
            ['nombre' => 'Recibido en Destino', 'descripcion' => 'El paquete ha llegado a su destino final'],
            ['nombre' => 'Entregado', 'descripcion' => 'El paquete ha sido entregado'],
            ['nombre' => 'En Proceso de Retorno', 'descripcion' => 'El paquete está siendo devuelto al remitente'],
            ['nombre' => 'Devuelto', 'descripcion' => 'El paquete ha sido devuelto'],
            ['nombre' => 'Dañado', 'descripcion' => 'El paquete ha sido reportado como dañado'],
            ['nombre' => 'Perdido', 'descripcion' => 'El paquete está perdido'],
            ['nombre' => 'Cancelado', 'descripcion' => 'El envío del paquete ha sido cancelado'],
        ]);
    }
}


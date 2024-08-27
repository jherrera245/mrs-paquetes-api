<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DireccionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('direcciones')->insert([
            [
                'id_cliente' => 1,
                'nombre_contacto' => 'Jose',
                'telefono' => '78302817',
                'direccion' => 'Av. Los Naranjos caso 20',
                'referencia' => 'Enfrente del GYN los portales',
                'id_departamento' => 12, 
                'id_municipio' => 200, 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_cliente' => 1,
                'nombre_contacto' => 'Maria',
                'telefono' => '78302845',
                'direccion' => 'Av. Los Naranjos caso 20',
                'referencia' => 'Enfrente del GYN los portales',
                'id_departamento' => 12,
                'id_municipio' => 200, 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_cliente' => 2,
                'nombre_contacto' => 'Santos',
                'telefono' => '78302812',
                'direccion' => 'Av. los heroes casa #40',
                'referencia' => 'Enfrente a la iglesia los portales',
                'id_departamento' => 12, 
                'id_municipio' => 200, 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_cliente' => 2,
                'nombre_contacto' => 'Carmen',
                'telefono' => '7830278',
                'direccion' => 'Av. los heroes casa #41',
                'referencia' => 'Enfrente a la iglesia los portales',
                'id_departamento' => 12, 
                'id_municipio' => 200,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],[
                'id_cliente' => 3,
                'nombre_contacto' => 'Luis',
                'telefono' => '78305017',
                'direccion' => 'Col. San Mateo, Casa #15',
                'referencia' => 'Cerca del supermercado La Unión',
                'id_departamento' => 12, 
                'id_municipio' => 201, 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_cliente' => 3,
                'nombre_contacto' => 'Carla',
                'telefono' => '78301523',
                'direccion' => 'Res. Los Pinos, Bl. 5, Apto. 3C',
                'referencia' => 'Frente al parque central',
                'id_departamento' => 12,
                'id_municipio' => 202, 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_cliente' => 4,
                'nombre_contacto' => 'Julia',
                'telefono' => '78302564',
                'direccion' => 'Av. Litoral, Edificio Alfa, Of. 204',
                'referencia' => 'Al lado de la clinica dental Sonrisas',
                'id_departamento' => 13, 
                'id_municipio' => 203, 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_cliente' => 4,
                'nombre_contacto' => 'Marcos',
                'telefono' => '78303244',
                'direccion' => 'Urb. El Bosque, Sendero 12, Casa #8',
                'referencia' => 'Detrás del colegio Internacional',
                'id_departamento' => 13, 
                'id_municipio' => 204,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_cliente' => 5,
                'nombre_contacto' => 'Natalia',
                'telefono' => '78304455',
                'direccion' => 'Boulevard del Rio, Quinta 10',
                'referencia' => 'Cerca del puente viejo',
                'id_departamento' => 13, 
                'id_municipio' => 205,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);
    }
}

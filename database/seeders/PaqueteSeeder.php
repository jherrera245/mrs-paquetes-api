<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PaqueteSeeder extends Seeder
{
    public function run()
    {
        DB::table('paquetes')->insert([
            [
                'id_tipo_paquete' => 1,
                'id_empaque' => 1,
                'peso' => 5.00,
                'uuid' => '676d8e07-61f9-40b0-b3c0-a277afcb414c',
                'tag' => 'https://mrs-paquetes-bucket.s3.us-west-1.amazonaws.com/qr_codes/676d8e07-61f9-40b0-b3c0-a277afcb414c.png',
                'id_estado_paquete' => 4,
                'fecha_envio' => '2024-08-26 00:00:00',
                'fecha_entrega_estimada' => '2024-08-29 00:00:00',
                'descripcion_contenido' => 'Documento de titulo importante',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'eliminado_at' => null
            ],
            [
                'id_tipo_paquete' => 2,
                'id_empaque' => 1,
                'peso' => 15.00,
                'uuid' => '3ecc6713-30ae-4880-9987-4186e7a429c4',
                'tag' => 'https://mrs-paquetes-bucket.s3.us-west-1.amazonaws.com/qr_codes/3ecc6713-30ae-4880-9987-4186e7a429c4.png',
                'id_estado_paquete' => 4,
                'fecha_envio' => '2024-08-26 00:00:00',
                'fecha_entrega_estimada' => '2024-08-29 00:00:00',
                'descripcion_contenido' => 'Computadora',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'eliminado_at' => null
            ],
            [
                'id_tipo_paquete' => 3,
                'id_empaque' => 1,
                'peso' => 45.00,
                'uuid' => 'a066fea1-dfd5-4fb0-8811-6ace4234e31e',
                'tag' => 'https://mrs-paquetes-bucket.s3.us-west-1.amazonaws.com/qr_codes/a066fea1-dfd5-4fb0-8811-6ace4234e31e.png',
                'id_estado_paquete' => 4,
                'fecha_envio' => '2024-08-26 00:00:00',
                'fecha_entrega_estimada' => '2024-08-29 00:00:00',
                'descripcion_contenido' => 'Ropa de señoria',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'eliminado_at' => null
            ],
            [
                'id_tipo_paquete' => 1,
                'id_empaque' => 1,
                'peso' => 5.00,
                'uuid' => '1249231c-3422-467c-9861-08a8e878dd09',
                'tag' => 'https://mrs-paquetes-bucket.s3.us-west-1.amazonaws.com/qr_codes/1249231c-3422-467c-9861-08a8e878dd09.png',
                'id_estado_paquete' => 4,
                'fecha_envio' => '2024-08-26 00:00:00',
                'fecha_entrega_estimada' => '2024-08-29 00:00:00',
                'descripcion_contenido' => 'Partidas de Nacimiento',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'eliminado_at' => null
            ],
            [
                'id_tipo_paquete' => 2,
                'id_empaque' => 1,
                'peso' => 85.00,
                'uuid' => 'f53cd268-e654-48d5-bd10-3159cb065711',
                'tag' => 'https://mrs-paquetes-bucket.s3.us-west-1.amazonaws.com/qr_codes/f53cd268-e654-48d5-bd10-3159cb065711.png',
                'id_estado_paquete' => 4,
                'fecha_envio' => '2024-08-26 00:00:00',
                'fecha_entrega_estimada' => '2024-08-29 00:00:00',
                'descripcion_contenido' => 'Silla gamer',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'eliminado_at' => null
            ],
            [
                'id_tipo_paquete' => 4,
                'id_empaque' => 1,
                'peso' => 25.00,
                'uuid' => '006adfc3-ba13-4849-b72f-dc80bed8ad74',
                'tag' => 'https://mrs-paquetes-bucket.s3.us-west-1.amazonaws.com/qr_codes/006adfc3-ba13-4849-b72f-dc80bed8ad74.png',
                'id_estado_paquete' => 4,
                'fecha_envio' => '2024-08-26 00:00:00',
                'fecha_entrega_estimada' => '2024-08-29 00:00:00',
                'descripcion_contenido' => 'Arroz',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'eliminado_at' => null
            ],
            [
                'id_tipo_paquete' => 2,
                'id_empaque' => 2,
                'peso' => 20.00,
                'uuid' => '75f0c911-4d7a-4011-98ad-08d4aecad4c5',
                'tag' => 'https://mrs-paquetes-bucket.s3.us-west-1.amazonaws.com/qr_codes/75f0c911-4d7a-4011-98ad-08d4aecad4c5.png',
                'id_estado_paquete' => 4,
                'fecha_envio' => '2024-08-27 00:00:00',
                'fecha_entrega_estimada' => '2024-08-30 00:00:00',
                'descripcion_contenido' => 'TV, Aparatos',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'eliminado_at' => null
            ],
            [
                'id_tipo_paquete' => 3,
                'id_empaque' => 1,
                'peso' => 20.00,
                'uuid' => 'cb24a177-3c04-4ed7-bb85-caa91977f347',
                'tag' => 'https://mrs-paquetes-bucket.s3.us-west-1.amazonaws.com/qr_codes/cb24a177-3c04-4ed7-bb85-caa91977f347.png',
                'id_estado_paquete' => 4,
                'fecha_envio' => '2024-08-26 00:00:00',
                'fecha_entrega_estimada' => '2024-08-26 00:00:00',
                'descripcion_contenido' => 'Ropa de caballero',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'eliminado_at' => null
            ],
            [
                'id_tipo_paquete' => 1,
                'id_empaque' => 1,
                'peso' => 5.00,
                'uuid' => '676d8e07-61f9-40b0-b3c0-a277afcb414c',
                'tag' => 'https://mrs-paquetes-bucket.s3.us-west-1.amazonaws.com/qr_codes/676d8e07-61f9-40b0-b3c0-a277afcb414c.png',
                'id_estado_paquete' => 4,
                'fecha_envio' => '2024-08-26 00:00:00',
                'fecha_entrega_estimada' => '2024-08-29 00:00:00',
                'descripcion_contenido' => 'Documento de titulo importante',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'eliminado_at' => null
            ],
            [
                'id_tipo_paquete' => 2,
                'id_empaque' => 1,
                'peso' => 15.00,
                'uuid' => '3ecc6713-30ae-4880-9987-4186e7a429c4',
                'tag' => 'https://mrs-paquetes-bucket.s3.us-west-1.amazonaws.com/qr_codes/3ecc6713-30ae-4880-9987-4186e7a429c4.png',
                'id_estado_paquete' => 4,
                'fecha_envio' => '2024-08-26 00:00:00',
                'fecha_entrega_estimada' => '2024-08-29 00:00:00',
                'descripcion_contenido' => 'Computadora',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'eliminado_at' => null
            ],
            [
                'id_tipo_paquete' => 3,
                'id_empaque' => 1,
                'peso' => 45.00,
                'uuid' => 'a066fea1-dfd5-4fb0-8811-6ace4234e31e',
                'tag' => 'https://mrs-paquetes-bucket.s3.us-west-1.amazonaws.com/qr_codes/a066fea1-dfd5-4fb0-8811-6ace4234e31e.png',
                'id_estado_paquete' => 4,
                'fecha_envio' => '2024-08-26 00:00:00',
                'fecha_entrega_estimada' => '2024-08-29 00:00:00',
                'descripcion_contenido' => 'Ropa de señoria',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'eliminado_at' => null
            ],
            [
                'id_tipo_paquete' => 1,
                'id_empaque' => 1,
                'peso' => 5.00,
                'uuid' => '1249231c-3422-467c-9861-08a8e878dd09',
                'tag' => 'https://mrs-paquetes-bucket.s3.us-west-1.amazonaws.com/qr_codes/1249231c-3422-467c-9861-08a8e878dd09.png',
                'id_estado_paquete' => 4,
                'fecha_envio' => '2024-08-26 00:00:00',
                'fecha_entrega_estimada' => '2024-08-29 00:00:00',
                'descripcion_contenido' => 'Partidas de Nacimiento',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'eliminado_at' => null
            ],
            [
                'id_tipo_paquete' => 2,
                'id_empaque' => 1,
                'peso' => 85.00,
                'uuid' => 'f53cd268-e654-48d5-bd10-3159cb065711',
                'tag' => 'https://mrs-paquetes-bucket.s3.us-west-1.amazonaws.com/qr_codes/f53cd268-e654-48d5-bd10-3159cb065711.png',
                'id_estado_paquete' => 4,
                'fecha_envio' => '2024-08-26 00:00:00',
                'fecha_entrega_estimada' => '2024-08-29 00:00:00',
                'descripcion_contenido' => 'Silla gamer',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'eliminado_at' => null
            ],
            [
                'id_tipo_paquete' => 4,
                'id_empaque' => 1,
                'peso' => 25.00,
                'uuid' => '006adfc3-ba13-4849-b72f-dc80bed8ad74',
                'tag' => 'https://mrs-paquetes-bucket.s3.us-west-1.amazonaws.com/qr_codes/006adfc3-ba13-4849-b72f-dc80bed8ad74.png',
                'id_estado_paquete' => 4,
                'fecha_envio' => '2024-08-26 00:00:00',
                'fecha_entrega_estimada' => '2024-08-29 00:00:00',
                'descripcion_contenido' => 'Arroz',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'eliminado_at' => null
            ],
            [
                'id_tipo_paquete' => 2,
                'id_empaque' => 2,
                'peso' => 20.00,
                'uuid' => '75f0c911-4d7a-4011-98ad-08d4aecad4c5',
                'tag' => 'https://mrs-paquetes-bucket.s3.us-west-1.amazonaws.com/qr_codes/75f0c911-4d7a-4011-98ad-08d4aecad4c5.png',
                'id_estado_paquete' => 4,
                'fecha_envio' => '2024-08-27 00:00:00',
                'fecha_entrega_estimada' => '2024-08-30 00:00:00',
                'descripcion_contenido' => 'TV, Aparatos',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'eliminado_at' => null
            ],
            [
                'id_tipo_paquete' => 3,
                'id_empaque' => 1,
                'peso' => 20.00,
                'uuid' => '49a46a53-faec-4bcb-bb38-33d1709fe598',
                'tag' => 'https://mrs-paquetes-bucket.s3.us-west-1.amazonaws.com/qr_codes/49a46a53-faec-4bcb-bb38-33d1709fe598.png',
                'id_estado_paquete' => 4,
                'fecha_envio' => '2024-08-26 00:00:00',
                'fecha_entrega_estimada' => '2024-08-26 00:00:00',
                'descripcion_contenido' => 'Ropa de caballero',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'eliminado_at' => null
            ],
            [
                'id_tipo_paquete' => 3,
                'id_empaque' => 1,
                'peso' => 20.00,
                'uuid' => 'a29ca87d-cb9d-40fc-af44-18605b10f329',
                'tag' => 'https://mrs-paquetes-bucket.s3.us-west-1.amazonaws.com/qr_codes/a29ca87d-cb9d-40fc-af44-18605b10f329.png',
                'id_estado_paquete' => 4,
                'fecha_envio' => '2024-08-26 00:00:00',
                'fecha_entrega_estimada' => '2024-08-26 00:00:00',
                'descripcion_contenido' => 'Ropa de Niños',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'eliminado_at' => null
            ],
        ]);
    }
}

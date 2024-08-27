<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DestinoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $destinos = [
            // 30 destinos con id_departamento 12 y id_municipio entre 200 y 217
            ['nombre' => 'Parque Los Encinos', 'descripcion' => 'Ubicado frente al Centro Cultural.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 200],
            ['nombre' => 'Centro Comercial Valle Verde', 'descripcion' => 'Al lado de la Universidad Estatal.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 201],
            ['nombre' => 'Mercado Central San Pedro', 'descripcion' => 'Esquina con la Avenida Bolívar.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 202],
            ['nombre' => 'Jardín Botánico El Edén', 'descripcion' => 'Cerca del Museo de Historia Natural.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 203],
            ['nombre' => 'Estadio Municipal Rivera', 'descripcion' => 'Frente al Hospital General.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 204],
            ['nombre' => 'Biblioteca Pública San Carlos', 'descripcion' => 'Detrás del Palacio de Justicia.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 205],
            ['nombre' => 'Teatro Nacional García Lorca', 'descripcion' => 'A un lado del Parque Central.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 206],
            ['nombre' => 'Terminal de Autobuses El Sol', 'descripcion' => 'Después del Centro de Convenciones.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 207],
            ['nombre' => 'Universidad Politécnica San Mateo', 'descripcion' => 'Esquina opuesta a la Plaza de Armas.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 208],
            ['nombre' => 'Residencial Los Almendros', 'descripcion' => 'Detrás del Instituto Técnico.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 209],
            ['nombre' => 'Centro de Salud La Esperanza', 'descripcion' => 'Frente al Parque de la Paz.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 210],
            ['nombre' => 'Monumento a los Héroes', 'descripcion' => 'Al lado de la Catedral Metropolitana.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 211],
            ['nombre' => 'Centro Deportivo El Prado', 'descripcion' => 'Cerca del Centro Comercial Las Américas.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 212],
            ['nombre' => 'Plaza Mayor San Felipe', 'descripcion' => 'A la derecha de la Casa de la Cultura.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 213],
            ['nombre' => 'Hospital Materno Infantil', 'descripcion' => 'Esquina con el Boulevard de los Héroes.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 214],
            ['nombre' => 'Auditorio Nacional Miguel Hidalgo', 'descripcion' => 'Frente al Estadio Olímpico.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 215],
            ['nombre' => 'Centro de Convenciones Las Flores', 'descripcion' => 'Cerca del Mercado de Artesanías.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 216],
            ['nombre' => 'Aeropuerto Internacional El Carmen', 'descripcion' => 'A la izquierda del Centro de Negocios.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 217],
            ['nombre' => 'Plaza Comercial El Faro', 'descripcion' => 'Detrás del Hospital General.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 218],
            ['nombre' => 'Residencial Los Cipreses', 'descripcion' => 'Frente a la Escuela de Música.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 219],
            ['nombre' => 'Centro de Capacitación Técnica', 'descripcion' => 'A un lado del Parque Industrial.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 220],
            ['nombre' => 'Teatro de la Ciudad', 'descripcion' => 'Cerca del Centro Histórico.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 221],
            ['nombre' => 'Parque Metropolitano La Libertad', 'descripcion' => 'Detrás de la Estación de Trenes.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 222],
            ['nombre' => 'Hospital Regional San Antonio', 'descripcion' => 'Frente a la Terminal de Buses.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 223],
            ['nombre' => 'Biblioteca Central Francisco Gavidia', 'descripcion' => 'Cerca del Museo de Arte Contemporáneo.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 224],
            ['nombre' => 'Centro Comercial El Portal', 'descripcion' => 'A la derecha del Jardín Botánico.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 225],
            ['nombre' => 'Residencial Los Laureles', 'descripcion' => 'Esquina con la Avenida de los Próceres.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 226],
            ['nombre' => 'Estadio Municipal El Salvador', 'descripcion' => 'Frente a la Universidad Nacional.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 227],
            ['nombre' => 'Zoológico La Sabana', 'descripcion' => 'Cerca del Centro de Convenciones.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 228],
            ['nombre' => 'Plaza del Sol', 'descripcion' => 'Detrás del Monumento a la Paz.', 'estado' => 1, 'id_departamento' => 12, 'id_municipio' => 229],

            // 5 destinos con id_departamento 13 y id_municipio entre 219 y 240
            ['nombre' => 'Parque Los Laureles', 'descripcion' => 'Vivienda en el número 219 de la colonia Los Laureles.', 'estado' => 1, 'id_departamento' => 13, 'id_municipio' => 219],
            ['nombre' => 'Centro Comercial La Cima', 'descripcion' => 'Frente a la Plaza del Encuentro.', 'estado' => 1, 'id_departamento' => 13, 'id_municipio' => 220],
            ['nombre' => 'Hospital La Esperanza', 'descripcion' => 'Al lado de la Iglesia San José.', 'estado' => 1, 'id_departamento' => 13, 'id_municipio' => 221],
            ['nombre' => 'Residencial Las Colinas', 'descripcion' => 'Detrás de la Escuela Santa María.', 'estado' => 1, 'id_departamento' => 13, 'id_municipio' => 222],
            ['nombre' => 'Parque Industrial Las Mercedes', 'descripcion' => 'Cerca de la Universidad Americana.', 'estado' => 1, 'id_departamento' => 13, 'id_municipio' => 240],

            // 5 destinos con id_departamento 14 y id_municipio entre 246 y 262
            ['nombre' => 'Centro Recreativo Las Brisas', 'descripcion' => 'Vivienda en el número 246 de la colonia Las Brisas.', 'estado' => 1, 'id_departamento' => 14, 'id_municipio' => 246],
            ['nombre' => 'Centro Educativo Las Rosas', 'descripcion' => 'Frente a la Plaza Central.', 'estado' => 1, 'id_departamento' => 14, 'id_municipio' => 250],
            ['nombre' => 'Centro de Salud San Miguel', 'descripcion' => 'Cerca del Mercado Municipal.', 'estado' => 1, 'id_departamento' => 14, 'id_municipio' => 255],
            ['nombre' => 'Residencial Los Pinos', 'descripcion' => 'Detrás de la Parroquia San Martín.', 'estado' => 1, 'id_departamento' => 14, 'id_municipio' => 260],
            ['nombre' => 'Plaza Comercial Los Olivos', 'descripcion' => 'Esquina con la Avenida Principal.', 'estado' => 1, 'id_departamento' => 14, 'id_municipio' => 262],

            // 5 destinos con id_departamento 11 y id_municipio entre 176 y 198
            ['nombre' => 'Residencial Los Girasoles', 'descripcion' => 'Cerca del Colegio El Roble.', 'estado' => 1, 'id_departamento' => 11, 'id_municipio' => 176],
            ['nombre' => 'Plaza Comercial Los Cedros', 'descripcion' => 'Frente al Parque Municipal.', 'estado' => 1, 'id_departamento' => 11, 'id_municipio' => 180],
            ['nombre' => 'Centro Cultural Los Álamos', 'descripcion' => 'Detrás del Instituto Nacional.', 'estado' => 1, 'id_departamento' => 11, 'id_municipio' => 185],
            ['nombre' => 'Parque Residencial El Sauce', 'descripcion' => 'Al lado de la Clínica Santa Ana.', 'estado' => 1, 'id_departamento' => 11, 'id_municipio' => 190],
            ['nombre' => 'Centro Deportivo El Pino', 'descripcion' => 'Esquina con el Boulevard de la Paz.', 'estado' => 1, 'id_departamento' => 11, 'id_municipio' => 198],
        ];

        // Inserta todos los destinos en la tabla
        DB::table('destinos')->insert($destinos);
    }
}

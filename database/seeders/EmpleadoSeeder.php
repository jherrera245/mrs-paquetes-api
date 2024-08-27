<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpleadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Datos de prueba para 30 empleados con id_municipio dentro del rango 199-218
        $empleados = [
            ['nombres' => 'Maria', 'apellidos' => 'Gomez', 'dui' => '4718066', 'telefono' => '70177762', 'fecha_nacimiento' => '1990-12-27', 'fecha_contratacion' => '2020-11-27', 'id_estado' => 1, 'id_cargo' => 1, 'id_departamento' => 12, 'id_municipio' => 200, 'direccion' => '1281 Heath Avenue'],
            ['nombres' => 'Juan', 'apellidos' => 'Perez', 'dui' => '6734076', 'telefono' => '77368748', 'fecha_nacimiento' => '1985-12-27', 'fecha_contratacion' => '2015-11-25', 'id_estado' => 1, 'id_cargo' => 2, 'id_departamento' => 12, 'id_municipio' => 205, 'direccion' => '2 Atwood Court'],
            ['nombres' => 'Pedro', 'apellidos' => 'Lopez', 'dui' => '10517519', 'telefono' => '61054300', 'fecha_nacimiento' => '1978-11-07', 'fecha_contratacion' => '2019-09-13', 'id_estado' => 1, 'id_cargo' => 1, 'id_departamento' => 12, 'id_municipio' => 210, 'direccion' => '11 Longview Hill'],
            ['nombres' => 'Sofia', 'apellidos' => 'Martinez', 'dui' => '7657967', 'telefono' => '66713828', 'fecha_nacimiento' => '1992-09-10', 'fecha_contratacion' => '2020-01-30', 'id_estado' => 1, 'id_cargo' => 2, 'id_departamento' => 12, 'id_municipio' => 215, 'direccion' => '6226 Upham Trail'],
            ['nombres' => 'Ana', 'apellidos' => 'Ramirez', 'dui' => '153349', 'telefono' => '78775799', 'fecha_nacimiento' => '1961-02-03', 'fecha_contratacion' => '2006-03-14', 'id_estado' => 1, 'id_cargo' => 3, 'id_departamento' => 12, 'id_municipio' => 218, 'direccion' => '79282 Loeprich Trail'],
            ['nombres' => 'Lucia', 'apellidos' => 'Hernandez', 'dui' => '8840442', 'telefono' => '65254898', 'fecha_nacimiento' => '1990-11-05', 'fecha_contratacion' => '2021-05-20', 'id_estado' => 1, 'id_cargo' => 4, 'id_departamento' => 12, 'id_municipio' => 199, 'direccion' => '0 Village Green Parkway'],
            ['nombres' => 'Carlos', 'apellidos' => 'Diaz', 'dui' => '2484158', 'telefono' => '66434154', 'fecha_nacimiento' => '1980-07-28', 'fecha_contratacion' => '2007-04-26', 'id_estado' => 1, 'id_cargo' => 5, 'id_departamento' => 12, 'id_municipio' => 203, 'direccion' => '4 Hintze Trail'],
            ['nombres' => 'Jorge', 'apellidos' => 'Gonzalez', 'dui' => '2268090', 'telefono' => '61405859', 'fecha_nacimiento' => '1916-04-26', 'fecha_contratacion' => '2022-12-16', 'id_estado' => 1, 'id_cargo' => 6, 'id_departamento' => 12, 'id_municipio' => 208, 'direccion' => '930 Transport Lane'],
            ['nombres' => 'Marta', 'apellidos' => 'Sanchez', 'dui' => '255834', 'telefono' => '71807561', 'fecha_nacimiento' => '1951-03-24', 'fecha_contratacion' => '2013-04-05', 'id_estado' => 1, 'id_cargo' => 7, 'id_departamento' => 12, 'id_municipio' => 211, 'direccion' => '83 Lakewood Plaza'],
            ['nombres' => 'Gabriel', 'apellidos' => 'Ortiz', 'dui' => '5739317', 'telefono' => '66760079', 'fecha_nacimiento' => '1975-06-17', 'fecha_contratacion' => '2014-02-12', 'id_estado' => 1, 'id_cargo' => 8, 'id_departamento' => 12, 'id_municipio' => 217, 'direccion' => '3261 Eggendart Parkway'],
            ['nombres' => 'Daniel', 'apellidos' => 'Cruz', 'dui' => '7947613', 'telefono' => '63033237', 'fecha_nacimiento' => '1981-05-28', 'fecha_contratacion' => '2016-12-09', 'id_estado' => 1, 'id_cargo' => 9, 'id_departamento' => 12, 'id_municipio' => 202, 'direccion' => '6969 Straubel Court'],
            ['nombres' => 'Elena', 'apellidos' => 'Reyes', 'dui' => '9925895', 'telefono' => '79629131', 'fecha_nacimiento' => '1931-10-31', 'fecha_contratacion' => '2008-04-23', 'id_estado' => 1, 'id_cargo' => 10, 'id_departamento' => 12, 'id_municipio' => 207, 'direccion' => '2 Jay Trail'],
            ['nombres' => 'Alvaro', 'apellidos' => 'Mendoza', 'dui' => '4862773', 'telefono' => '79408120', 'fecha_nacimiento' => '1937-08-17', 'fecha_contratacion' => '2009-07-13', 'id_estado' => 1, 'id_cargo' => 1, 'id_departamento' => 12, 'id_municipio' => 216, 'direccion' => '857 West Junction'],
            ['nombres' => 'Rosa', 'apellidos' => 'Flores', 'dui' => '1221996', 'telefono' => '61707974', 'fecha_nacimiento' => '1928-02-20', 'fecha_contratacion' => '2019-06-02', 'id_estado' => 1, 'id_cargo' => 2, 'id_departamento' => 12, 'id_municipio' => 200, 'direccion' => '1391 Fordem Alley'],
            ['nombres' => 'Fernando', 'apellidos' => 'Morales', 'dui' => '4689947', 'telefono' => '77410757', 'fecha_nacimiento' => '1967-01-05', 'fecha_contratacion' => '2019-08-20', 'id_estado' => 1, 'id_cargo' => 3, 'id_departamento' => 12, 'id_municipio' => 213, 'direccion' => '47 Warbler Junction'],
            ['nombres' => 'Sara', 'apellidos' => 'Vega', 'dui' => '9253488', 'telefono' => '69133730', 'fecha_nacimiento' => '1934-12-14', 'fecha_contratacion' => '2013-04-02', 'id_estado' => 1, 'id_cargo' => 4, 'id_departamento' => 12, 'id_municipio' => 201, 'direccion' => '5 New Castle Street'],
            ['nombres' => 'Raul', 'apellidos' => 'Paz', 'dui' => '4578307', 'telefono' => '67763516', 'fecha_nacimiento' => '1972-11-15', 'fecha_contratacion' => '2011-11-04', 'id_estado' => 1, 'id_cargo' => 5, 'id_departamento' => 12, 'id_municipio' => 209, 'direccion' => '48358 Milwaukee Road'],
            ['nombres' => 'Laura', 'apellidos' => 'Torres', 'dui' => '3318252', 'telefono' => '77513116', 'fecha_nacimiento' => '1967-07-28', 'fecha_contratacion' => '2010-06-27', 'id_estado' => 1, 'id_cargo' => 6, 'id_departamento' => 12, 'id_municipio' => 212, 'direccion' => '3 Mifflin Hill'],
            ['nombres' => 'Mario', 'apellidos' => 'Ramos', 'dui' => '7846897', 'telefono' => '68834941', 'fecha_nacimiento' => '1999-01-22', 'fecha_contratacion' => '2016-10-27', 'id_estado' => 1, 'id_cargo' => 7, 'id_departamento' => 12, 'id_municipio' => 204, 'direccion' => '00 Anzinger Alley'],
            ['nombres' => 'Nuria', 'apellidos' => 'Guzman', 'dui' => '6807363', 'telefono' => '65025487', 'fecha_nacimiento' => '1902-02-07', 'fecha_contratacion' => '2018-05-01', 'id_estado' => 1, 'id_cargo' => 8, 'id_departamento' => 12, 'id_municipio' => 206, 'direccion' => '9140 Monica Avenue'],
            ['nombres' => 'Clara', 'apellidos' => 'Vargas', 'dui' => '1198319', 'telefono' => '72751092', 'fecha_nacimiento' => '1968-08-11', 'fecha_contratacion' => '2006-02-21', 'id_estado' => 1, 'id_cargo' => 9, 'id_departamento' => 12, 'id_municipio' => 214, 'direccion' => '03 Cardinal Lane'],
            ['nombres' => 'Pablo', 'apellidos' => 'Castro', 'dui' => '6992018', 'telefono' => '75293451', 'fecha_nacimiento' => '1963-12-27', 'fecha_contratacion' => '2015-06-18', 'id_estado' => 1, 'id_cargo' => 10, 'id_departamento' => 12, 'id_municipio' => 199, 'direccion' => '3520 Namekagon Park'],
            ['nombres' => 'David', 'apellidos' => 'Silva', 'dui' => '8388813', 'telefono' => '68125860', 'fecha_nacimiento' => '1989-12-24', 'fecha_contratacion' => '2012-09-17', 'id_estado' => 1, 'id_cargo' => 1, 'id_departamento' => 12, 'id_municipio' => 218, 'direccion' => '60206 Declaration Plaza'],
            ['nombres' => 'Irene', 'apellidos' => 'Mendez', 'dui' => '1842048', 'telefono' => '61342977', 'fecha_nacimiento' => '1926-09-07', 'fecha_contratacion' => '2009-10-25', 'id_estado' => 1, 'id_cargo' => 2, 'id_departamento' => 12, 'id_municipio' => 203, 'direccion' => '049 John Wall Pass'],
            ['nombres' => 'Javier', 'apellidos' => 'Carrillo', 'dui' => '7654054', 'telefono' => '65822351', 'fecha_nacimiento' => '1968-03-09', 'fecha_contratacion' => '2018-06-13', 'id_estado' => 1, 'id_cargo' => 3, 'id_departamento' => 12, 'id_municipio' => 208, 'direccion' => '61 Aberg Parkway'],
            ['nombres' => 'Cristina', 'apellidos' => 'Lozano', 'dui' => '5729609', 'telefono' => '76432159', 'fecha_nacimiento' => '1968-08-12', 'fecha_contratacion' => '2015-02-07', 'id_estado' => 1, 'id_cargo' => 4, 'id_departamento' => 12, 'id_municipio' => 205, 'direccion' => '103 Red Cloud Junction'],
            ['nombres' => 'Esteban', 'apellidos' => 'Cortes', 'dui' => '9820958', 'telefono' => '60014897', 'fecha_nacimiento' => '1937-03-10', 'fecha_contratacion' => '2022-12-26', 'id_estado' => 1, 'id_cargo' => 5, 'id_departamento' => 12, 'id_municipio' => 210, 'direccion' => '619 Hoffman Terrace'],
            ['nombres' => 'Antonio', 'apellidos' => 'Campos', 'dui' => '8430479', 'telefono' => '69581418', 'fecha_nacimiento' => '1949-12-25', 'fecha_contratacion' => '2021-10-27', 'id_estado' => 1, 'id_cargo' => 6, 'id_departamento' => 12, 'id_municipio' => 202, 'direccion' => '019 Knutson Place'],
            ['nombres' => 'Isabel', 'apellidos' => 'Ibanez', 'dui' => '8983161', 'telefono' => '77899837', 'fecha_nacimiento' => '1931-03-01', 'fecha_contratacion' => '2019-11-26', 'id_estado' => 1, 'id_cargo' => 7, 'id_departamento' => 12, 'id_municipio' => 215, 'direccion' => '38 Pepper Wood Parkway'],
            ['nombres' => 'Soledad', 'apellidos' => 'Jimenez', 'dui' => '10303420', 'telefono' => '60464992', 'fecha_nacimiento' => '2003-04-11', 'fecha_contratacion' => '2019-02-18', 'id_estado' => 1, 'id_cargo' => 8, 'id_departamento' => 12, 'id_municipio' => 201, 'direccion' => '386 Sugar Trail'],
        ];

        // Inserta todos los empleados en la tabla
        DB::table('empleados')->insert($empleados);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MunicipioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ["nombre"=>'AHUACHAPAN', "id_departamento"=>  1],
            ["nombre"=>'APANECA', "id_departamento"=>  1],
            ["nombre"=>'ATIQUIZAYA', "id_departamento"=>  1],
            ["nombre"=>'CONCEPCIÓN DE ATACO', "id_departamento"=>  1],
            ["nombre"=>'EL REFUGIO', "id_departamento"=>  1],
            ["nombre"=>'GUAYMANGO', "id_departamento"=>  1],
            ["nombre"=>'JUJUTLA', "id_departamento"=>  1],
            ["nombre"=>'SAN FRANCISCO MENENDEZ', "id_departamento"=>  1],
            ["nombre"=>'SAN LORENZO', "id_departamento"=>  1],
            ["nombre"=>'SAN PEDRO PUXTLA', "id_departamento"=>  1],
            ["nombre"=>'TACUBA', "id_departamento"=>  1],
            ["nombre"=>'TURIN', "id_departamento"=>  1],
            ["nombre"=>'CANDELARIA DE LA FRONTERA', "id_departamento"=>  2],
            ["nombre"=>'COATEPEQUE', "id_departamento"=>  2],
            ["nombre"=>'CHALCHUAPA', "id_departamento"=>  2],
            ["nombre"=>'EL CONGO', "id_departamento"=>  2],
            ["nombre"=>'EL PORVENIR', "id_departamento"=>  2],
            ["nombre"=>'MASAHUAT', "id_departamento"=>  2],
            ["nombre"=>'METAPAN', "id_departamento"=>  2],
            ["nombre"=>'SAN ANTONIO PAJONAL', "id_departamento"=>  2],
            ["nombre"=>'SAN SEBASTIAN SALITRILLO', "id_departamento"=>  2],
            ["nombre"=>'SANTA ANA', "id_departamento"=>  2],
            ["nombre"=>'SANTA ROSA GUACHIPILIN', "id_departamento"=>  2],
            ["nombre"=>'SANTIAGO DE LA FRONTERA', "id_departamento"=>  2],
            ["nombre"=>'TEXISTEPEQUE', "id_departamento"=>  2],
            ["nombre"=>'ACAJUTLA', "id_departamento"=>  3],
            ["nombre"=>'ARMENIA', "id_departamento"=>  3],
            ["nombre"=>'CALUCO', "id_departamento"=>  3],
            ["nombre"=>'CUISNAHUAT', "id_departamento"=>  3],
            ["nombre"=>'SANTA ISABEL ISHUATÁN', "id_departamento"=>  3],
            ["nombre"=>'IZALCO', "id_departamento"=>  3],
            ["nombre"=>'JUAYUA', "id_departamento"=>  3],
            ["nombre"=>'NAHUIZALCO', "id_departamento"=>  3],
            ["nombre"=>'NAHULINGO', "id_departamento"=>  3],
            ["nombre"=>'SALCOATITAN', "id_departamento"=>  3],
            ["nombre"=>'SAN ANTONIO DEL MONTE', "id_departamento"=>  3],
            ["nombre"=>'SAN JULIAN', "id_departamento"=>  3],
            ["nombre"=>'SANTA CATARINA MASAHUAT', "id_departamento"=>  3],
            ["nombre"=>'SANTO DOMINGO DE GUZMAN', "id_departamento"=>  3],
            ["nombre"=>'SONSONATE', "id_departamento"=>  3],
            ["nombre"=>'SONZACATE', "id_departamento"=>  3],
            ["nombre"=>'AGUA CALIENTE', "id_departamento"=>  4],
            ["nombre"=>'ARCATAO', "id_departamento"=>  4],
            ["nombre"=>'AZACUALPA', "id_departamento"=>  4],
            ["nombre"=>'CITALA', "id_departamento"=>  4],
            ["nombre"=>'COMALAPA', "id_departamento"=>  4],
            ["nombre"=>'CONCEPCION QUEZALTEPEQUE', "id_departamento"=>  4],
            ["nombre"=>'CHALATENANGO', "id_departamento"=>  4],
            ["nombre"=>'DULCE NOMBRE DE MARÍA', "id_departamento"=>  4],
            ["nombre"=>'EL CARRIZAL', "id_departamento"=>  4],
            ["nombre"=>'EL PARAISO', "id_departamento"=>  4],
            ["nombre"=>'LA LAGUNA', "id_departamento"=>  4],
            ["nombre"=>'LA PALMA', "id_departamento"=>  4],
            ["nombre"=>'LA REINA', "id_departamento"=>  4],
            ["nombre"=>'LAS VUELTAS', "id_departamento"=>  4],
            ["nombre"=>'NOMBRE DE JESÚS', "id_departamento"=>  4],
            ["nombre"=>'NUEVA CONCEPCION', "id_departamento"=>  4],
            ["nombre"=>'NUEVA TRINIDAD', "id_departamento"=>  4],
            ["nombre"=>'OJOS DE AGUA', "id_departamento"=>  4],
            ["nombre"=>'POTONICO', "id_departamento"=>  4],
            ["nombre"=>'SAN ANTONIO DE LA CRUZ', "id_departamento"=>  4],
            ["nombre"=>'SAN ANTONIO LOS RANCHOS', "id_departamento"=>  4],
            ["nombre"=>'SAN FERNANDO', "id_departamento"=>  4],
            ["nombre"=>'SAN FRANCISCO LEMPA', "id_departamento"=>  4],
            ["nombre"=>'SAN FRANCISCO MORAZÁN', "id_departamento"=>  4],
            ["nombre"=>'SAN IGNACIO', "id_departamento"=>  4],
            ["nombre"=>'SAN ISIDRO LABRADOR', "id_departamento"=>  4],
            ["nombre"=>'CANCASQUE', "id_departamento"=>  4],
            ["nombre"=>'LAS FLORES', "id_departamento"=>  4],
            ["nombre"=>'SAN LUIS DEL CARMEN', "id_departamento"=>  4],
            ["nombre"=>'SAN MIGUEL DE MERCEDES', "id_departamento"=>  4],
            ["nombre"=>'SAN RAFAEL', "id_departamento"=>  4],
            ["nombre"=>'SANTA RITA', "id_departamento"=>  4],
            ["nombre"=>'TEJUTLA', "id_departamento"=>  4],
            ["nombre"=>'ANTIGUO CUSCATLAN', "id_departamento"=>  5],
            ["nombre"=>'CIUDAD ARCE', "id_departamento"=>  5],
            ["nombre"=>'COLON', "id_departamento"=>  5],
            ["nombre"=>'COMASAGUA', "id_departamento"=>  5],
            ["nombre"=>'CHILTIUPAN', "id_departamento"=>  5],
            ["nombre"=>'HUIZUCAR', "id_departamento"=>  5],
            ["nombre"=>'JAYAQUE', "id_departamento"=>  5],
            ["nombre"=>'JICALAPA', "id_departamento"=>  5],
            ["nombre"=>'LA LIBERTAD', "id_departamento"=>  5],
            ["nombre"=>'NUEVO CUSCATLAN', "id_departamento"=>  5],
            ["nombre"=>'SANTA TECLA', "id_departamento"=>  5],
            ["nombre"=>'QUEZALTEPEQUE', "id_departamento"=>  5],
            ["nombre"=>'SACACOYO', "id_departamento"=>  5],
            ["nombre"=>'SAN JOSE VILLANUEVA', "id_departamento"=>  5],
            ["nombre"=>'SAN JUAN OPICO', "id_departamento"=>  5],
            ["nombre"=>'SAN MATÍAS', "id_departamento"=>  5],
            ["nombre"=>'SAN PABLO TACACHICO', "id_departamento"=>  5],
            ["nombre"=>'TAMANIQUE', "id_departamento"=>  5],
            ["nombre"=>'TALNIQUE', "id_departamento"=>  5],
            ["nombre"=>'TEOTEPEQUE', "id_departamento"=>  5],
            ["nombre"=>'TEPECOYO', "id_departamento"=>  5],
            ["nombre"=>'ZARAGOZA', "id_departamento"=>  5],
            ["nombre"=>'AGUILARES', "id_departamento"=>  6],
            ["nombre"=>'APOPA', "id_departamento"=>  6],
            ["nombre"=>'AYUTUXTEPEQUE', "id_departamento"=>  6],
            ["nombre"=> 'CUSCATANCINGO', "id_departamento"=>  6],
            ["nombre"=> 'EL PAISNAL', "id_departamento"=>  6],
            ["nombre"=> 'GUAZAPA', "id_departamento"=>  6],
            ["nombre"=> 'ILOPANGO', "id_departamento"=>  6],
            ["nombre"=> 'MEJICANOS', "id_departamento"=>  6],
            ["nombre"=> 'NEJAPA', "id_departamento"=>  6],
            ["nombre"=> 'PANCHIMALCO', "id_departamento"=>  6],
            ["nombre"=> 'ROSARIO DE MORA', "id_departamento"=>  6],
            ["nombre"=> 'SAN MARCOS', "id_departamento"=>  6],
            ["nombre"=> 'SAN MARTIN', "id_departamento"=>  6],
            ["nombre"=> 'SAN SALVADOR', "id_departamento"=>  6],
            ["nombre"=> 'SANTIAGO TEXACUANGOS', "id_departamento"=>  6],
            ["nombre"=> 'SANTO TOMAS', "id_departamento"=>  6],
            ["nombre"=> 'SOYAPANGO', "id_departamento"=>  6],
            ["nombre"=> 'TONACATEPEQUE', "id_departamento"=>  6],
            ["nombre"=> 'CIUDAD DELGADO', "id_departamento"=>  6],
            ["nombre"=> 'CANDELARIA', "id_departamento"=>  7],
            ["nombre"=> 'COJUTEPEQUE', "id_departamento"=>  7],
            ["nombre"=> 'EL CARMEN', "id_departamento"=>  7],
            ["nombre"=> 'EL ROSARIO', "id_departamento"=>  7],
            ["nombre"=> 'MONTE SAN JUAN', "id_departamento"=>  7],
            ["nombre"=> 'ORATORIO DE CONCEPCIÓN', "id_departamento"=>  7],
            ["nombre"=> 'SAN BARTOLOME PERULAPIA', "id_departamento"=>  7],
            ["nombre"=> 'SAN CRISTÓBAL', "id_departamento"=>  7],
            ["nombre"=> 'SAN JOSE GUAYABAL', "id_departamento"=>  7],
            ["nombre"=> 'SAN PEDRO PERULAPAN', "id_departamento"=>  7],
            ["nombre"=> 'SAN RAFAEL CEDROS', "id_departamento"=>  7],
            ["nombre"=> 'SAN RAMÓN', "id_departamento"=>  7],
            ["nombre"=> 'SANTA CRUZ ANALQUITO', "id_departamento"=>  7],
            ["nombre"=> 'SANTA CRUZ MICHAPA', "id_departamento"=>  7],
            ["nombre"=> 'SUCHITOTO', "id_departamento"=>  7],
            ["nombre"=> 'TENANCINGO', "id_departamento"=>  7],
            ["nombre"=> 'CUYULTITAN', "id_departamento"=>  8],
            ["nombre"=> 'EL ROSARIO', "id_departamento"=>  8],
            ["nombre"=> 'JERUSALÉN', "id_departamento"=>  8],
            ["nombre"=> 'MERCEDES LA CEIBA', "id_departamento"=>  8],
            ["nombre"=> 'OLOCUILTA', "id_departamento"=>  8],
            ["nombre"=> 'PARAÍSO DE OSORIO', "id_departamento"=>  8],
            ["nombre"=> 'SAN ANTONIO MASAHUAT', "id_departamento"=>  8],
            ["nombre"=> 'SAN EMIGDIO', "id_departamento"=>  8],
            ["nombre"=> 'SAN FRANCISCO CHINAMECA', "id_departamento"=>  8],
            ["nombre"=> 'SAN JUAN NONUALCO', "id_departamento"=>  8],
            ["nombre"=> 'SAN JUAN TALPA', "id_departamento"=>  8],
            ["nombre"=> 'SAN JUAN TEPEZONTES', "id_departamento"=>  8],
            ["nombre"=> 'SAN LUIS TALPA', "id_departamento"=>  8],
            ["nombre"=> 'SAN MIGUEL TEPEZONTES', "id_departamento"=>  8],
            ["nombre"=> 'SAN PEDRO MASAHUAT', "id_departamento"=>  8],
            ["nombre"=> 'SAN PEDRO NONUALCO', "id_departamento"=>  8],
            ["nombre"=> 'SAN RAFAEL OBRAJUELO', "id_departamento"=>  8],
            ["nombre"=> 'SANTA MARÍA OSTUMA', "id_departamento"=>  8],
            ["nombre"=> 'SANTIAGO NONUALCO', "id_departamento"=>  8],
            ["nombre"=> 'TAPALHUACA', "id_departamento"=>  8],
            ["nombre"=> 'ZACATECOLUCA', "id_departamento"=>  8],
            ["nombre"=> 'SAN LUIS LA HERRADURA', "id_departamento"=>  8],
            ["nombre"=> 'CINQUERA', "id_departamento"=>  9],
            ["nombre"=> 'GUACOTECTI', "id_departamento"=>  9],
            ["nombre"=> 'ILOBASCO', "id_departamento"=>  9],
            ["nombre"=> 'JUTIAPA', "id_departamento"=>  9],
            ["nombre"=> 'SAN ISIDRO', "id_departamento"=>  9],
            ["nombre"=> 'SENSUNTEPEQUE', "id_departamento"=>  9],
            ["nombre"=> 'TEJUTEPEQUE', "id_departamento"=>  9],
            ["nombre"=> 'VICTORIA', "id_departamento"=>  9],
            ["nombre"=> 'DOLORES', "id_departamento"=>  9],
            ["nombre"=> 'APASTEPEQUE',  "id_departamento"=> 10],
            ["nombre"=> 'GUADALUPE',  "id_departamento"=> 10],
            ["nombre"=> 'SAN CAYETANO ISTEPEQUE',  "id_departamento"=> 10],
            ["nombre"=> 'SANTA CLARA',  "id_departamento"=> 10],
            ["nombre"=> 'SANTO DOMINGO',  "id_departamento"=> 10],
            ["nombre"=> 'SAN ESTEBAN CATARINA',  "id_departamento"=> 10],
            ["nombre"=> 'SAN ILDEFONSO',  "id_departamento"=> 10],
            ["nombre"=> 'SAN LORENZO',  "id_departamento"=> 10],
            ["nombre"=> 'SAN SEBASTIAN',  "id_departamento"=> 10],
            ["nombre"=> 'SAN VICENTE',  "id_departamento"=> 10],
            ["nombre"=> 'TECOLUCA',  "id_departamento"=> 10],
            ["nombre"=> 'TEPETITÁN',  "id_departamento"=> 10],
            ["nombre"=> 'VERAPAZ',  "id_departamento"=> 10],
            ["nombre"=> 'ALEGRIA',  "id_departamento"=> 11],
            ["nombre"=> 'BERLIN',  "id_departamento"=> 11],
            ["nombre"=> 'CALIFORNIA',  "id_departamento"=> 11],
            ["nombre"=> 'CONCEPCION BATRES',  "id_departamento"=> 11],
            ["nombre"=> 'EL TRIUNFO',  "id_departamento"=> 11],
            ["nombre"=> 'EREGUAYQUIN',  "id_departamento"=> 11],
            ["nombre"=> 'ESTANZUELAS',  "id_departamento"=> 11],
            ["nombre"=> 'JIQUILISCO',  "id_departamento"=> 11],
            ["nombre"=> 'JUCUAPA',  "id_departamento"=> 11],
            ["nombre"=> 'JUCUARÁN',  "id_departamento"=> 11],
            ["nombre"=> 'MERCEDES UMAÑA',  "id_departamento"=> 11],
            ["nombre"=> 'NUEVA GRANADA',  "id_departamento"=> 11],
            ["nombre"=> 'OZATLAN',  "id_departamento"=> 11],
            ["nombre"=> 'PUERTO EL TRIUNFO',  "id_departamento"=> 11],
            ["nombre"=> 'SAN AGUSTIN',  "id_departamento"=> 11],
            ["nombre"=> 'SAN BUENA VENTURA',  "id_departamento"=> 11],
            ["nombre"=> 'SAN DIONISIO',  "id_departamento"=> 11],
            ["nombre"=> 'SANTA ELENA',  "id_departamento"=> 11],
            ["nombre"=> 'SAN FRANCISCO JAVIER',  "id_departamento"=> 11],
            ["nombre"=> 'SANTA MARIA',  "id_departamento"=> 11],
            ["nombre"=> 'SANTIAGO DE MARIA',  "id_departamento"=> 11],
            ["nombre"=> 'TECAPAN',  "id_departamento"=> 11],
            ["nombre"=> 'USULUTAN',  "id_departamento"=> 11],
            ["nombre"=> 'CAROLINA',  "id_departamento"=> 12],
            ["nombre"=> 'CIUDAD BARRIOS',  "id_departamento"=> 12],
            ["nombre"=> 'COMACARAN',  "id_departamento"=> 12],
            ["nombre"=> 'CHAPELTIQUE',  "id_departamento"=> 12],
            ["nombre"=> 'CHINAMECA',  "id_departamento"=> 12],
            ["nombre"=> 'CHIRILAGUA',  "id_departamento"=> 12],
            ["nombre"=> 'EL TRANSITO',  "id_departamento"=> 12],
            ["nombre"=> 'LOLOTIQUE',  "id_departamento"=> 12],
            ["nombre"=> 'MONCAGUA',  "id_departamento"=> 12],
            ["nombre"=> 'NUEVA GUADALUPE',  "id_departamento"=> 12],
            ["nombre"=> 'NUEVO EDEN DE SAN JUAN',  "id_departamento"=> 12],
            ["nombre"=> 'QUELEPA',  "id_departamento"=> 12],
            ["nombre"=> 'SAN ANTONIO',  "id_departamento"=> 12],
            ["nombre"=> 'SAN GERARDO',  "id_departamento"=> 12],
            ["nombre"=> 'SAN JORGE',  "id_departamento"=> 12],
            ["nombre"=> 'SAN LUIS DE LA REINA',  "id_departamento"=> 12],
            ["nombre"=> 'SAN MIGUEL',  "id_departamento"=> 12],
            ["nombre"=> 'SAN RAFAEL ORIENTE',  "id_departamento"=> 12],
            ["nombre"=> 'SESORI',  "id_departamento"=> 12],
            ["nombre"=> 'ULUAZAPA',  "id_departamento"=> 12],
            ["nombre"=> 'ARAMBALA',  "id_departamento"=> 13],
            ["nombre"=> 'CACAOPERA',  "id_departamento"=> 13],
            ["nombre"=> 'CORINTO',  "id_departamento"=> 13],
            ["nombre"=> 'CHILANGA',  "id_departamento"=> 13],
            ["nombre"=> 'DELICIAS DE CONCEPCIÓN',  "id_departamento"=> 13],
            ["nombre"=> 'EL DIVISADERO',  "id_departamento"=> 13],
            ["nombre"=> 'EL ROSARIO',  "id_departamento"=> 13],
            ["nombre"=> 'GUALOCOCTI',  "id_departamento"=> 13],
            ["nombre"=> 'GUATAJIAGUA',  "id_departamento"=> 13],
            ["nombre"=> 'JOATECA',  "id_departamento"=> 13],
            ["nombre"=> 'JOCOAITIQUE',  "id_departamento"=> 13],
            ["nombre"=> 'JOCORO',  "id_departamento"=> 13],
            ["nombre"=> 'LOLOTIQUILLO',  "id_departamento"=> 13],
            ["nombre"=> 'MEANGUERA',  "id_departamento"=> 13],
            ["nombre"=> 'OSCICALA',  "id_departamento"=> 13],
            ["nombre"=> 'PERQUIN',  "id_departamento"=> 13],
            ["nombre"=> 'SAN CARLOS',  "id_departamento"=> 13],
            ["nombre"=> 'SAN FERNANDO',  "id_departamento"=> 13],
            ["nombre"=> 'SAN FRANCISCO GOTERA',  "id_departamento"=> 13],
            ["nombre"=> 'SAN ISIDRO',  "id_departamento"=> 13],
            ["nombre"=> 'SAN SIMON',  "id_departamento"=> 13],
            ["nombre"=> 'SENSEMBRA',  "id_departamento"=> 13],
            ["nombre"=> 'SOCIEDAD',  "id_departamento"=> 13],
            ["nombre"=> 'TOROLA',  "id_departamento"=> 13],
            ["nombre"=> 'YAMABAL',  "id_departamento"=> 13],
            ["nombre"=> 'YOLOAIQUIN',  "id_departamento"=> 13],
            ["nombre"=> 'ANAMOROS',  "id_departamento"=> 14],
            ["nombre"=> 'BOLÍVAR',  "id_departamento"=> 14],
            ["nombre"=> 'CONCEPCION DE ORIENTE',  "id_departamento"=> 14],
            ["nombre"=> 'CONCHAGUA',  "id_departamento"=> 14],
            ["nombre"=> 'EL CARMEN',  "id_departamento"=> 14],
            ["nombre"=> 'EL SAUCE',  "id_departamento"=> 14],
            ["nombre"=> 'INTIPUCÁ',  "id_departamento"=> 14],
            ["nombre"=> 'LA UNION',  "id_departamento"=> 14],
            ["nombre"=> 'LISLIQUE',  "id_departamento"=> 14],
            ["nombre"=> 'MEANGUERA DEL GOLFO',  "id_departamento"=> 14],
            ["nombre"=> 'NUEVA ESPARTA',  "id_departamento"=> 14],
            ["nombre"=> 'PASAQUINA',  "id_departamento"=> 14],
            ["nombre"=> 'POLOROS',  "id_departamento"=> 14],
            ["nombre"=> 'SAN ALEJO',  "id_departamento"=> 14],
            ["nombre"=> 'SAN JOSÉ',  "id_departamento"=> 14],
            ["nombre"=> 'SANTA ROSA DE LIMA',  "id_departamento"=> 14],
            ["nombre"=> 'YAYANTIQUE',  "id_departamento"=> 14],
            ["nombre"=> 'YUCUAIQUÍN',  "id_departamento"=> 14]
        ];

        DB::table('municipios')->insert($data);
    }
}

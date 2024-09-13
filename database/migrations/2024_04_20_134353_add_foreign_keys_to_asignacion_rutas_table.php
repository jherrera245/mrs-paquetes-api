<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asignacion_rutas', function (Blueprint $table) {
            $table->foreign(['id_vehiculo'], 'asignacion_rutas_fk_id_vehculo')->references(['id'])->on('vehiculos');
            $table->foreign(['id_estado'], 'asignacion_rutas_fk_id_estado_ruta')->references(['id'])->on('estado_rutas');
            $table->foreign(['id_ruta'], 'asignacion_rutas_fk_id_ruta')->references(['id'])->on('rutas');
            $table->foreign(['id_paquete'], 'asignacion_rutas_id_paquete')->references(['id'])->on('paquetes');
            $table->foreign(['id_departamento'], 'asignacion_rutas_id_deparatamento')->references(['id'])->on('departamento');
            $table->foreign(['id_municipio'], 'asignacion_rutas_id_municipio')->references(['id'])->on('municipios');
            $table->foreign(['id_direccion'], 'asignacion_rutas_id_direccion')->references(['id'])->on('direcciones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asignacion_rutas', function (Blueprint $table) {
            $table->dropForeign('asignacion_rutas_fk_id_vehculo');
            $table->dropForeign('asignacion_rutas_fk_id_estado_ruta');
            $table->dropForeign('asignacion_rutas_fk_id_ruta');
            $table->dropForeign('asignacion_rutas_id_paquete');
            $table->dropForeign('asignacion_rutas_id_deparatamento');
            $table->dropForeign('asignacion_rutas_id_municipio');
            $table->dropForeign('asignacion_rutas_id_direccion');
        });
    }
};

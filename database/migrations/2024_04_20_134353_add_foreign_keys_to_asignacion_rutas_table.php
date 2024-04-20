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
            $table->foreign(['id_vehiculo'], 'asignacion_rutas_ibfk_2')->references(['id'])->on('vehiculos')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_estado'], 'asignacion_rutas_ibfk_4')->references(['id'])->on('estado_rutas')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_ruta'], 'asignacion_rutas_ibfk_1')->references(['id'])->on('rutas')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_paquete'], 'asignacion_rutas_ibfk_3')->references(['id'])->on('paquetes')->onUpdate('CASCADE')->onDelete('CASCADE');
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
            $table->dropForeign('asignacion_rutas_ibfk_2');
            $table->dropForeign('asignacion_rutas_ibfk_4');
            $table->dropForeign('asignacion_rutas_ibfk_1');
            $table->dropForeign('asignacion_rutas_ibfk_3');
        });
    }
};

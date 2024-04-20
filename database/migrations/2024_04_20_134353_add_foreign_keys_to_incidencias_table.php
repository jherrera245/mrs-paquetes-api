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
        Schema::table('incidencias', function (Blueprint $table) {
            $table->foreign(['estado'], 'incidencias_ibfk_5')->references(['id'])->on('estado_incidencias')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_paquete'], 'incidencias_ibfk_2')->references(['id'])->on('paquetes')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_usuario_asignado'], 'incidencias_ibfk_4')->references(['id'])->on('usuarios')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_tipo_incidencia'], 'incidencias_ibfk_1')->references(['id'])->on('tipo_incidencia')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_usuario_reporta'], 'incidencias_ibfk_3')->references(['id'])->on('usuarios')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->dropForeign('incidencias_ibfk_5');
            $table->dropForeign('incidencias_ibfk_2');
            $table->dropForeign('incidencias_ibfk_4');
            $table->dropForeign('incidencias_ibfk_1');
            $table->dropForeign('incidencias_ibfk_3');
        });
    }
};

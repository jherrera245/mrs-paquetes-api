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
            $table->foreign(['estado'], 'incidencias_fk_id_estado')->references(['id'])->on('estado_incidencias');
            $table->foreign(['id_paquete'], 'incidencias_fk_id_paquete')->references(['id'])->on('paquetes');
            $table->foreign(['id_usuario_asignado'], 'incidencias_fk_id_user_asignado')->references(['id'])->on('users');
            $table->foreign(['id_tipo_incidencia'], 'incidencias_fk_id_tipo_incidencia')->references(['id'])->on('tipo_incidencia');
            $table->foreign(['id_usuario_reporta'], 'incidencias_fk_id_user_recibe')->references(['id'])->on('users');
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
            $table->dropForeign('incidencias_fk_id_estado');
            $table->dropForeign('incidencias_fk_id_paquete');
            $table->dropForeign('incidencias_fk_id_user_asignado');
            $table->dropForeign('incidencias_fk_id_tipo_incidencia');
            $table->dropForeign('incidencias_fk_id_user_recibe');
        });
    }
};

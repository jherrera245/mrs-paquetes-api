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
        Schema::create('incidencias', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_paquete')->index('id_paquete');
            $table->dateTime('fecha_hora');
            $table->integer('id_tipo_incidencia')->index('id_tipo_incidencia');
            $table->integer('descripcion');
            $table->integer('estado')->index('estado');
            $table->dateTime('fecha_resolucion');
            $table->integer('id_usuario_reporta')->index('id_usuario_reporta');
            $table->integer('id_usuario_asignado')->index('id_usuario_asignado');
            $table->text('solucion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incidencias');
    }
};

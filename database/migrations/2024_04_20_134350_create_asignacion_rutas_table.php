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
        Schema::create('asignacion_rutas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('codigo_unico_asignacion');
            $table->integer('id_ruta')->index('id_ruta');
            $table->integer('id_vehiculo')->index('id_vehiculo');
            $table->integer('id_paquete')->index('id_paquete');
            $table->dateTime('fecha');
            $table->integer('id_estado')->index('id_estado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asignacion_rutas');
    }
};

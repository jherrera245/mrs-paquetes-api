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
            $table->id();
            $table->foreignId('id_paquete');
            $table->dateTime('fecha_hora');
            $table->foreignId('id_tipo_incidencia');
            $table->foreignId('descripcion');
            $table->foreignId('estado');
            $table->dateTime('fecha_resolucion');
            $table->foreignId('id_usuario_reporta');
            $table->foreignId('id_usuario_asignado');
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

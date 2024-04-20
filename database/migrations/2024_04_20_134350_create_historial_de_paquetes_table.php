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
        Schema::create('historial_de_paquetes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_paquete')->index('id_paquete');
            $table->dateTime('fecha_hora');
            $table->integer('id_usuario')->index('id_usuario');
            $table->string('accion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historial_de_paquetes');
    }
};

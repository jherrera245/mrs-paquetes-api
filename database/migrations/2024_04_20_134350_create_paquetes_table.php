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
        Schema::create('paquetes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_tipo_paquete')->index('id_tipo_paquete');
            $table->integer('id_empaque')->index('id_empaque');
            $table->decimal('peso', 10);
            $table->string('uuid');
            $table->string('tag');
            $table->integer('id_estado_paquete')->index('id_estado_paquete');
            $table->dateTime('fecha_envio');
            $table->dateTime('fecha_entrega_estimada');
            $table->text('descripcion_contenido');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paquetes');
    }
};

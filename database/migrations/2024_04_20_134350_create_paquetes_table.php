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
            $table->id();
            $table->foreignId('id_ubicacion');
            $table->foreignId('id_tipo_paquete');
            $table->foreignId('id_tamano_paquete');
            $table->foreignId('id_empaque');
            $table->decimal('peso', 10);
            $table->string('uuid');
            $table->string('tag');
            $table->foreignId('id_estado_paquete');
            $table->datetime('fecha_envio');
            $table->datetime('fecha_entrega_estimada');
            $table->text('descripcion_contenido');
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
        Schema::dropIfExists('paquetes');
    }
};

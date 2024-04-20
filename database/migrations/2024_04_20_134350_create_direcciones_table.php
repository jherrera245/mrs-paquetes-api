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
        Schema::create('direcciones', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_cliente')->index('id_cliente');
            $table->string('nombre_contacto');
            $table->string('telefono', 9);
            $table->integer('id_departamento')->index('id_departamento');
            $table->integer('id_municipio')->index('id_municipio');
            $table->string('direccion');
            $table->string('referencia');
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
        Schema::dropIfExists('direcciones');
    }
};

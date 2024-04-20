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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nombre', 100);
            $table->integer('id_departamento')->index('id_departamento');
            $table->integer('id_municipio')->index('id_municipio');
            $table->string('direccion');
            $table->string('telefono', 9);
            $table->string('email');
            $table->string('web_site');
            $table->integer('id_estado')->index('id_estado');
            $table->dateTime('fecha_creacion');
            $table->dateTime('fecha_modificacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proveedores');
    }
};

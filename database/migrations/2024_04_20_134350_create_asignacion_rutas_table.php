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
            $table->id();
            $table->string('codigo_unico_asignacion');
            $table->foreignId('id_ruta');
            $table->foreignId('id_vehiculo');
            $table->foreignId('id_paquete');
            $table->integer('prioridad');
            $table->foreignId('id_deparatamento');
            $table->foreignId('id_municipio');
            $table->foreignId('id_direccion');
            $table->string('destino');
            $table->dateTime('fecha');
            $table->foreignId('id_estado');
            $table->boolean('status')->nullable()->default(1);
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
        Schema::dropIfExists('asignacion_rutas');
    }
};

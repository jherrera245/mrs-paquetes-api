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
        Schema::create('rutas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_destino')->index('id_destino');
            $table->string('nombre');
            $table->integer('id_bodega')->index('id_bodega');
            $table->integer('id_estado')->index('id_estado');
            $table->decimal('distancia_km', 10);
            $table->decimal('duracion_aproximada', 5);
            $table->date('fecha_programada');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rutas');
    }
};

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
            $table->id();
            $table->foreignId('id_destino');
            $table->string('nombre');
            $table->foreignId('id_bodega');
            $table->foreignId('id_estado');
            $table->decimal('distancia_km', 10);
            $table->decimal('duracion_aproximada', 5);
            $table->date('fecha_programada');
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
        Schema::dropIfExists('rutas');
    }
};

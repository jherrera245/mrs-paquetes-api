<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleTrasladoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_traslado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_traslado')->constrained('traslados')->onDelete('cascade');
            $table->foreignId('id_paquete')->constrained('paquetes')->onDelete('cascade'); 
            $table->boolean('estado')->default(1); // Campo para el estado (1: activo, 0: inactivo o eliminado)
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
        Schema::dropIfExists('detalle_traslado');
    }
}

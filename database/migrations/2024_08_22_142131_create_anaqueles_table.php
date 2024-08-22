<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnaquelesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anaqueles', function (Blueprint $table) {
            $table->id();
            // relacion con pasillo.
            $table->foreignId('id_pasillo')->constrained('pasillos');
            $table->string('nombre');
            $table->integer('capacidad')->nullable();
            $table->integer('paquetes_actuales')->nullable();
            // estado -> 1: activo, 0: inactivo.
            $table->integer('estado');
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
        Schema::dropIfExists('anaqueles');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUbicacionesPaquetesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ubicaciones_paquetes', function (Blueprint $table) {
            $table->id();
            // relacion con paquetes.
            $table->foreignId('id_paquete')->constrained('paquetes');
            $table->foreignId('id_ubicacion')->constrained('ubicaciones');
            $table->boolean('estado')->default(1);
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
        Schema::dropIfExists('ubicaciones_paquetes');
    }
}

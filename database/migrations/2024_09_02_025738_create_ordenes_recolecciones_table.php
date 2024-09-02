<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenesRecoleccionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_recolecciones', function (Blueprint $table) {
            $table->id();
            // relacion con rutas recolecciones
            $table->foreignId('id_ruta_recoleccion')->constrained('rutas_recolecciones');
            // relacion con ordenes.
            $table->foreignId('id_orden')->constrained('ordenes');
            // estado (0,1)
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
        Schema::dropIfExists('ordenes_recolecciones');
    }
}

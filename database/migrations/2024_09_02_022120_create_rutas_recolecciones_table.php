<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRutasRecoleccionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rutas_recolecciones', function (Blueprint $table) {
            $table->id();
            // relacion con rutas
            $table->foreignId('id_ruta')->constrained('rutas');
            // relacion con vehiculo.
            $table->foreignId('id_vehiculo')->constrained('vehiculos');
            // fecha de asignacion de la ruta.
            $table->date('fecha_asignacion');
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
        Schema::dropIfExists('rutas_recolecciones');
    }
}

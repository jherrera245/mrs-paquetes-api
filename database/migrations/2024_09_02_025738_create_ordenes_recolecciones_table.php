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
            $table->string('codigo_unico_recoleccion');
            // relacion con ordenes.
            $table->foreignId('id_orden')->constrained('ordenes');
            $table->integer('prioridad');
            $table->foreignId('id_departamento')->constrained('departamento');
            $table->foreignId('id_municipio')->constrained('municipios');
            $table->foreignId('id_direccion')->constrained('direcciones');
            $table->string('destino');
            // estado (0,1)
            $table->boolean('estado')->default(1);
            $table->boolean('recoleccion_iniciada')->nullable();
            $table->boolean('recoleccion_finalizada')->nullable();
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

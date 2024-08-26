<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTarifasDestinosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tarifas_destinos', function (Blueprint $table) {
            $table->id();
            // relacion con tarifas.
            $table->foreignId('id_tarifa')->constrained('tarifas');
            // relacion con departamentos.
            $table->foreignId('id_departamento')->constrained('departamento');
            // relacion con municipios.
            $table->foreignId('id_municipio')->constrained('municipios');
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
        Schema::dropIfExists('tarifas_destinos');
    }
}

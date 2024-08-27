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
            $table->foreignId('id_tarifa')->constrained('tarifas');
            $table->foreignId('id_tamano_paquete')->constrained('tamano_paquete');
            $table->foreignId('id_departamento')->constrained('departamento');
            $table->foreignId('id_municipio')->constrained('municipios');
            $table->decimal('monto', 8,2);
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

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
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_empleado_conductor')->index('id_empleado_conductor');
            $table->integer('id_empleado_apoyo')->index('id_empleado_apoyo');
            $table->string('placa', 10)->unique('placa');
            $table->decimal('capacidad_carga', 10);
            $table->integer('id_estado')->index('id_estado');
            $table->integer('id_marca')->index('id_marca');
            $table->integer('id_modelo')->index('id_modelo');
            $table->string('modelo');
            $table->integer('year_fabricacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehiculos');
    }
};

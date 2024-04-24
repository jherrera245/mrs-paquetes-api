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
            $table->id();
            $table->foreignId('id_empleado_conductor');
            $table->foreignId('id_empleado_apoyo');
            $table->string('placa', 10)->unique('placa');
            $table->decimal('capacidad_carga', 10);
            $table->foreignId('id_estado');
            $table->foreignId('id_marca');
            $table->foreignId('id_modelo');
            $table->foreignId('year_fabricacion');
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
        Schema::dropIfExists('vehiculos');
    }
};

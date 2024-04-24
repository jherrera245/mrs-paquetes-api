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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('nombre_comercial', 45);
            $table->string('email');
            $table->string('dui', 10)->unique('dui');
            $table->string('telefono', 9);
            $table->foreignId('id_tipo_persona');
            $table->boolean('es_contribuyente')->nullable()->default(0);
            $table->foreignId('id_genero');
            $table->datetime('fecha_registro');
            $table->foreignId('id_estado');
            $table->foreignId('id_departamento');
            $table->foreignId('id_municipio');
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
        Schema::dropIfExists('clientes');
    }
};

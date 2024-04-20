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
            $table->integer('id', true);
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('nombre_comercial', 45);
            $table->string('email');
            $table->string('dui', 10)->unique('dui');
            $table->string('telefono', 9);
            $table->integer('id_tipo_persona')->index('id_tipo_persona');
            $table->integer('es_contribuyente')->index('es_contribuyente');
            $table->integer('id_genero')->index('id_genero');
            $table->date('fecha_registro');
            $table->integer('id_estado')->index('id_estado');
            $table->integer('id_departamento')->index('id_departamento');
            $table->integer('id_municipio')->index('id_municipio');
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

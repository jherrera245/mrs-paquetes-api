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
        Schema::create('empleados', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->integer('id_genero')->index('id_genero');
            $table->string('dui', 10)->unique('dui');
            $table->string('telefono', 9);
            $table->string('email');
            $table->date('fecha_nacimiento');
            $table->date('fecha_contratacion');
            $table->decimal('salario', 10);
            $table->integer('id_estado')->index('id_estado');
            $table->integer('id_cargo')->index('id_cargo');
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
        Schema::dropIfExists('empleados');
    }
};

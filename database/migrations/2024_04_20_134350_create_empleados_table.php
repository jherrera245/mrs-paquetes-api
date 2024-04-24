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
            $table->id();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->foreignId('id_genero');
            $table->string('dui', 10)->unique('dui');
            $table->string('telefono', 9);
            $table->string('email');
            $table->date('fecha_nacimiento');
            $table->date('fecha_contratacion');
            $table->decimal('salario', 10);
            $table->foreignId('id_estado');
            $table->foreignId('id_cargo');
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
        Schema::dropIfExists('empleados');
    }
};

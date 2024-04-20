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
        Schema::table('empleados', function (Blueprint $table) {
            $table->foreign(['id_estado'], 'empleados_ibfk_5')->references(['id'])->on('estado_empleados')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_departamento'], 'empleados_ibfk_2')->references(['id'])->on('departamento')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_cargo'], 'empleados_ibfk_4')->references(['id'])->on('cargos')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_genero'], 'empleados_ibfk_1')->references(['id'])->on('genero')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_municipio'], 'empleados_ibfk_3')->references(['id'])->on('municipios')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropForeign('empleados_ibfk_5');
            $table->dropForeign('empleados_ibfk_2');
            $table->dropForeign('empleados_ibfk_4');
            $table->dropForeign('empleados_ibfk_1');
            $table->dropForeign('empleados_ibfk_3');
        });
    }
};

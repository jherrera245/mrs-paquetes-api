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
            $table->foreign(['id_estado'], 'empleados_fk_id_estado')->references(['id'])->on('estado_empleados');
            $table->foreign(['id_departamento'], 'empleados_fk_id_departamento')->references(['id'])->on('departamento');
            $table->foreign(['id_cargo'], 'empleados_fk_id_cargo')->references(['id'])->on('cargos');
            $table->foreign(['id_genero'], 'empleados_fk_id_genero')->references(['id'])->on('genero');
            $table->foreign(['id_municipio'], 'empleados_fk_id_municipio')->references(['id'])->on('municipios');
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
            $table->dropForeign('empleados_fk_id_estado');
            $table->dropForeign('empleados_fk_id_departamento');
            $table->dropForeign('empleados_fk_id_cargo');
            $table->dropForeign('empleados_fk_id_genero');
            $table->dropForeign('empleados_fk_id_municipio');
        });
    }
};

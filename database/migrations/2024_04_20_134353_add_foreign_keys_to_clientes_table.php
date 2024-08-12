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
        Schema::table('clientes', function (Blueprint $table) {
            $table->foreign(['id_user'], 'clientes_fk_id_user')->references(['id'])->on('users');
            $table->foreign(['id_estado'], 'clientes_fk_id_estado')->references(['id'])->on('estado_clientes');
            $table->foreign(['id_genero'], 'clientes_fk_id_genero')->references(['id'])->on('genero');
            $table->foreign(['id_municipio'], 'clientes_fk_id_municipio')->references(['id'])->on('municipios');
            $table->foreign(['id_tipo_persona'], 'clientes_fk_id_tipo_persona')->references(['id'])->on('tipo_persona');
            $table->foreign(['id_departamento'], 'clientes_fk_id_departamento')->references(['id'])->on('departamento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign('clientes_fk_id_user');
            $table->dropForeign('clientes_fk_id_estado');
            $table->dropForeign('clientes_fk_id_genero');
            $table->dropForeign('clientes_fk_id_municipio');
            $table->dropForeign('clientes_fk_id_tipo_persona');
            $table->dropForeign('clientes_fk_id_departamento');
        });
    }
};

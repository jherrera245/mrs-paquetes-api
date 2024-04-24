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
        Schema::table('direcciones', function (Blueprint $table) {
            $table->foreign(['id_departamento'], 'direcciones_fk_id_departamento')->references(['id'])->on('departamento');
            $table->foreign(['id_municipio'], 'direcciones_fk_id_municipio')->references(['id'])->on('municipios');
            $table->foreign(['id_cliente'], 'direcciones_fk_id_cliente')->references(['id'])->on('clientes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('direcciones', function (Blueprint $table) {
            $table->dropForeign('direcciones_fk_id_departamento');
            $table->dropForeign('direcciones_fk_id_municipio');
            $table->dropForeign('direcciones_fk_id_cliente');
        });
    }
};

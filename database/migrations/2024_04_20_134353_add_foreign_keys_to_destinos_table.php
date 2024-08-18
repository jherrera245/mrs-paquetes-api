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
        Schema::table('destinos', function (Blueprint $table) {
            $table->foreign(['id_municipio'], 'destinos_fk_id_municipio')->references(['id'])->on('municipios');
            $table->foreign(['id_departamento'], 'destinos_fk_id_departamento')->references(['id'])->on('departamento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('destinos', function (Blueprint $table) {
            $table->dropForeign('destinos_fk_id_municipio');
            $table->dropForeign('destinos_fk_id_departamento');
        });
    }
};

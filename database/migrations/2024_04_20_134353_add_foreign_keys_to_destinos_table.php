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
            $table->foreign(['id_municipio'], 'destinos_ibfk_2')->references(['id'])->on('municipios')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_departamento'], 'destinos_ibfk_1')->references(['id'])->on('departamento')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_estado'], 'destinos_ibfk_3')->references(['id'])->on('estado_rutas')->onUpdate('CASCADE')->onDelete('CASCADE');
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
            $table->dropForeign('destinos_ibfk_2');
            $table->dropForeign('destinos_ibfk_1');
            $table->dropForeign('destinos_ibfk_3');
        });
    }
};

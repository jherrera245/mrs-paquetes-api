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
        Schema::table('bodegas', function (Blueprint $table) {
            $table->foreign(['id_municipio'], 'bodegas_ibfk_2')->references(['id'])->on('municipios')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_departamento'], 'bodegas_ibfk_1')->references(['id'])->on('departamento')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bodegas', function (Blueprint $table) {
            $table->dropForeign('bodegas_ibfk_2');
            $table->dropForeign('bodegas_ibfk_1');
        });
    }
};

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
            $table->foreign(['id_departamento'], 'direcciones_ibfk_2')->references(['id'])->on('departamento')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_municipio'], 'direcciones_ibfk_1')->references(['id'])->on('municipios')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_cliente'], 'direcciones_ibfk_3')->references(['id'])->on('clientes')->onUpdate('CASCADE')->onDelete('CASCADE');
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
            $table->dropForeign('direcciones_ibfk_2');
            $table->dropForeign('direcciones_ibfk_1');
            $table->dropForeign('direcciones_ibfk_3');
        });
    }
};

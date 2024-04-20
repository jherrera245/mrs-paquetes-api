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
            $table->foreign(['id_estado'], 'clientes_ibfk_5')->references(['id'])->on('estado_clientes')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_genero'], 'clientes_ibfk_2')->references(['id'])->on('genero')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_municipio'], 'clientes_ibfk_4')->references(['id'])->on('municipios')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_tipo_persona'], 'clientes_ibfk_1')->references(['id'])->on('tipo_persona')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_departamento'], 'clientes_ibfk_3')->references(['id'])->on('departamento')->onUpdate('CASCADE')->onDelete('CASCADE');
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
            $table->dropForeign('clientes_ibfk_5');
            $table->dropForeign('clientes_ibfk_2');
            $table->dropForeign('clientes_ibfk_4');
            $table->dropForeign('clientes_ibfk_1');
            $table->dropForeign('clientes_ibfk_3');
        });
    }
};

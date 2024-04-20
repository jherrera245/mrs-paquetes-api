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
        Schema::table('rutas', function (Blueprint $table) {
            $table->foreign(['id_bodega'], 'rutas_ibfk_2')->references(['id'])->on('bodegas')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_destino'], 'rutas_ibfk_1')->references(['id'])->on('destinos')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_estado'], 'rutas_ibfk_3')->references(['id'])->on('estado_rutas')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rutas', function (Blueprint $table) {
            $table->dropForeign('rutas_ibfk_2');
            $table->dropForeign('rutas_ibfk_1');
            $table->dropForeign('rutas_ibfk_3');
        });
    }
};

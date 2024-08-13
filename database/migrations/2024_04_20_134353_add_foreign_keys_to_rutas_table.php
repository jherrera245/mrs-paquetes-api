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
            $table->foreign(['id_bodega'], 'rutas_fk_id_rutas')->references(['id'])->on('bodegas');
            $table->foreign(['id_destino'], 'rutas_fk_id_destino')->references(['id'])->on('destinos');
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
            $table->dropForeign('rutas_fk_id_rutas');
            $table->dropForeign('rutas_fk_id_destino');
            $table->dropForeign('rutas_id_estado');
        });
    }
};

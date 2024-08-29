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
        Schema::table('historial_ordenes_tracking', function (Blueprint $table) {
            $table->foreign('id_orden')->references('id')->on('ordenes');
            $table->foreign('id_estado_paquete')->references('id')->on('estado_paquetes');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historial_ordenes_tracking', function (Blueprint $table) {
            $table->dropForeign(['id_orden']);
            $table->dropForeign(['id_estado_paquete']);
        });
    }
};

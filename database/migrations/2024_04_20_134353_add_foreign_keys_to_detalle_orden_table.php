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
        Schema::table('detalle_orden', function (Blueprint $table) {
            $table->foreign(['id_orden'], 'detalle_orden_ibfk_2')->references(['id'])->on('ordenes')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_paquete'], 'detalle_orden_ibfk_1')->references(['id'])->on('paquetes')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detalle_orden', function (Blueprint $table) {
            $table->dropForeign('detalle_orden_ibfk_2');
            $table->dropForeign('detalle_orden_ibfk_1');
        });
    }
};

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
            $table->foreign(['id_orden'], 'detalle_orden_fk_id_orden')->references(['id'])->on('ordenes');
            $table->foreign(['id_paquete'], 'detalle_orden_fk_id_paquete')->references(['id'])->on('paquetes');
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
            $table->dropForeign('detalle_orden_fk_id_orden');
            $table->dropForeign('detalle_orden_fk_id_paquete');
        });
    }
};

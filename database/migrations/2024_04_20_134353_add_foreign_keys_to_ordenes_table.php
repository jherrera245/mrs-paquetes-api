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
        Schema::table('ordenes', function (Blueprint $table) {
            $table->foreign(['id_cliente'], 'ordenes_fk_id_cliente_orden')->references(['id'])->on('clientes');
            $table->foreign(['id_tipo_pago'], 'ordenes_fk_id_tipo_pago')->references(['id'])->on('tipo_pago');
            $table->foreign(['id_direccion'], 'ordenes_fk_id_direccion')->references(['id'])->on('direcciones');
            $table->foreign(['id_estado_paquetes'], 'ordenes_fk_id_estado_paquetes')->references(['id'])->on('estado_paquetes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ordenes', function (Blueprint $table) {
            $table->dropForeign('ordenes_fk_id_cliente_orden');
            $table->dropForeign('ordenes_fk_id_tipo_pago');
            $table->dropForeign('ordenes_fk_id_direccion');
            $table->dropForeign('ordenes_fk_id_estado_paquetes');
        });
    }
};

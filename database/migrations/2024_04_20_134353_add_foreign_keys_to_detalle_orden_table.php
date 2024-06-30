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
            $table->foreign(['id_tipo_entrega'], 'detalle_orden_fk_id_tipo_entrega')->references(['id'])->on('tipo_entrega');
            $table->foreign(['id_estado_paquetes'], 'detalle_orden_fk_id_estado_paquetes')->references(['id'])->on('tipo_entrega');
            $table->foreign(['id_cliente_entrega'], 'detalle_orden_fk_id_cliente_entrega')->references(['id'])->on('clientes');
            $table->foreign(['id_direccion_entrega'], 'detalle_orden_fk_id_direccion_entrega')->references(['id'])->on('direcciones');
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
            $table->dropForeign('detalle_orden_fk_id_tipo_entrega');
            $table->dropForeign('detalle_orden_fk_id_estado_paquetes');
            $table->dropForeign('detalle_orden_fk_id_cliente_entrega');
            $table->dropForeign('detalle_orden_fk_id_direccion_entrega');
        });
    }
};

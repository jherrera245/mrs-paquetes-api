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
            $table->foreign(['id_cliente_recibe'], 'ordenes_fk_id_cliente_recibe')->references(['id'])->on('clientes');
            $table->foreign(['id_tipo_entrega'], 'ordenes_fk_id_tipo_entrega')->references(['id'])->on('tipo_entrega');
            $table->foreign(['id_estado_paquetes'], 'ordenes_fk_id_estado_paquete')->references(['id'])->on('estado_paquetes');
            $table->foreign(['id_cliente_entrega'], 'ordenes_fk_id_cliente_entrega')->references(['id'])->on('clientes');
            $table->foreign(['id_tipo_pago'], 'ordenes_fk_id_tipo_pago')->references(['id'])->on('tipo_pago');
            $table->foreign(['id_paquete'], 'ordenes_fk_id_paquete')->references(['id'])->on('paquetes');
            $table->foreign(['id_direccion'], 'ordenes_fk_id_direccion')->references(['id'])->on('direcciones');
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
            $table->dropForeign('ordenes_fk_id_cliente_recibe');
            $table->dropForeign('ordenes_fk_id_tipo_entrega');
            $table->dropForeign('ordenes_fk_id_estado_paquete');
            $table->dropForeign('ordenes_fk_id_cliente_entrega');
            $table->dropForeign('ordenes_fk_id_tipo_pago');
            $table->dropForeign('ordenes_fk_id_paquete');
            $table->dropForeign('ordenes_fk_id_direccion');
        });
    }
};

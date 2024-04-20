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
            $table->foreign(['id_cliente_recibe'], 'ordenes_ibfk_5')->references(['id'])->on('clientes')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_tipo_entrega'], 'ordenes_ibfk_7')->references(['id'])->on('tipo_entrega')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_estado_paquetes'], 'ordenes_ibfk_2')->references(['id'])->on('estado_paquetes');
            $table->foreign(['id_cliente_entrega'], 'ordenes_ibfk_4')->references(['id'])->on('clientes')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_tipo_pago'], 'ordenes_ibfk_6')->references(['id'])->on('tipo_pago')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_paquete'], 'ordenes_ibfk_1')->references(['id'])->on('paquetes')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_direccion'], 'ordenes_ibfk_3')->references(['id'])->on('direcciones')->onUpdate('CASCADE')->onDelete('CASCADE');
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
            $table->dropForeign('ordenes_ibfk_5');
            $table->dropForeign('ordenes_ibfk_7');
            $table->dropForeign('ordenes_ibfk_2');
            $table->dropForeign('ordenes_ibfk_4');
            $table->dropForeign('ordenes_ibfk_6');
            $table->dropForeign('ordenes_ibfk_1');
            $table->dropForeign('ordenes_ibfk_3');
        });
    }
};

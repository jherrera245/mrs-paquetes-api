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
        Schema::create('ordenes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_cliente_entrega')->index('id_cliente_entrega');
            $table->string('telefono_entrega', 9);
            $table->integer('id_cliente_recibe')->index('id_cliente_recibe');
            $table->integer('id_direccion')->index('id_direccion');
            $table->integer('id_tipo_entrega')->index('id_tipo_entrega');
            $table->integer('id_estado_paquetes')->index('id_estado_paquetes');
            $table->integer('id_paquete')->index('id_paquete');
            $table->decimal('precio', 10);
            $table->integer('id_tipo_pago')->index('id_tipo_pago');
            $table->string('validacion_entrega');
            $table->decimal('costo_adicional', 10);
            $table->text('instrucciones_entrega');
            $table->dateTime('fecha_ingreso');
            $table->dateTime('fecha_entrega');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordenes');
    }
};

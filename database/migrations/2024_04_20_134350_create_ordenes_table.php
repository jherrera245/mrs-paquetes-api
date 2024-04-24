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
            $table->id();
            $table->foreignId('id_cliente_entrega');
            $table->string('telefono_entrega', 9);
            $table->foreignId('id_cliente_recibe');
            $table->foreignId('id_direccion');
            $table->foreignId('id_tipo_entrega');
            $table->foreignId('id_estado_paquetes');
            $table->foreignId('id_paquete');
            $table->decimal('precio', 10);
            $table->foreignId('id_tipo_pago');
            $table->string('validacion_entrega');
            $table->decimal('costo_adicional', 10);
            $table->text('instrucciones_entrega');
            $table->dateTime('fecha_ingreso');
            $table->dateTime('fecha_entrega');
            $table->timestamps();
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

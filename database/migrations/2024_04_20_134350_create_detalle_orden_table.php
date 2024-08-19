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
        Schema::create('detalle_orden', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_orden');
            $table->foreignId('id_paquete');
            $table->foreignId('id_tipo_entrega');
            $table->foreignId('id_estado_paquetes');
            $table->foreignId('id_cliente_entrega');
            $table->foreignId('id_direccion_entrega');
            $table->string('validacion_entrega');
            $table->text('instrucciones_entrega');
            $table->string('descripcion');
            $table->decimal('precio', 10);
            $table->dateTime('fecha_ingreso');
            $table->dateTime('fecha_entrega');
            $table->string('numero_seguimiento')->nullable(); 
            $table->timestamps();

            // Agregar la columna numero_seguimiento para almacenar el código de seguimiento único
          //  $table->string('numero_seguimiento')->unique()->nullable()->after('id_paquete');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_orden');
    }
};

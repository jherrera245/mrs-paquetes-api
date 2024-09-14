<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaqueteReporteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paquete_reporte', function (Blueprint $table) {

            $table->id(); 
            $table->foreignId('id_paquete');
            $table->foreignId('id_orden');   
            $table->foreignId('id_cliente'); 
            $table->foreignId('id_empleado_reporta'); 
            $table->text('descripcion_dano')->nullable(); 
            $table->decimal('costo_reparacion', 10)->nullable(); 
            $table->enum('estado', ['no reparado', 'en reparacion','reparado',  'devuelto']); 

            //llaves foraneas
            $table->foreign('id_paquete')->references('id')->on('paquetes')->onDelete('cascade');
            $table->foreign('id_orden')->references('id')->on('ordenes')->onDelete('cascade');
            $table->foreign('id_cliente')->references('id')->on('clientes')->onDelete('cascade');
            $table->foreign('id_empleado_reporta')->references('id')->on('empleados')->onDelete('cascade'); 

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
        Schema::dropIfExists('paquete_reporte');
    }
}

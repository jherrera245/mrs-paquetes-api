<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrasladosTable extends Migration
{
    public function up()
    {
        Schema::create('traslados', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('id_bodega')->constrained('bodegas'); 
            $table->string('codigo_qr'); 
            $table->foreignId('id_ubicacion_paquete')->nullable()->constrained('ubicaciones_paquetes'); 
            $table->foreignId('id_asignacion_ruta')->nullable()->constrained('asignacion_rutas'); 
            $table->foreignId('id_orden')->nullable()->constrained('ordenes'); 
            $table->string('numero_ingreso')->nullable(); 
            $table->date('fecha_traslado'); 
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo'); 
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('traslados');
    }
}

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
            $table->foreignId('bodega_origen')->constrained('bodegas');
            $table->foreignId('bodega_destino')->constrained('bodegas');
            $table->string('numero_traslado')->unique();
            $table->date('fecha_traslado'); 
            $table->enum('estado', ['Pendiente', 'Completado', 'Cancelado'])->default('Pendiente'); 
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('traslados');
    }
}

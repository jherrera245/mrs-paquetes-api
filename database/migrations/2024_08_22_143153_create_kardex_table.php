<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKardexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kardex', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_paquete')->constrained('paquetes');
            $table->foreignId('id_orden')->nullable()->constrained('ordenes');
            $table->integer('cantidad');
            $table->string('numero_ingreso');
            $table->string('tipoMovimiento');
            $table->date('fecha');
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
        Schema::dropIfExists('transacciones');
    }
}

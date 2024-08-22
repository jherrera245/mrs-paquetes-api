<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaccionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transacciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_paquete')->constrained('paquetes');
            $table->foreignId('id_bodega')->nullable()->constrained('bodegas');
            $table->foreignId('id_pasillo')->nullable()->constrained('pasillos');
            $table->foreignId('id_anaquel')->nullable()->constrained('anaqueles');
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

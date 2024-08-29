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
        Schema::create('historial_ordenes_tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_orden');
            $table->string('numero_seguimiento');
            $table->unsignedBigInteger('id_estado_paquete');
            $table->dateTime('fecha_hora');
            $table->text('comentario')->nullable();
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
        Schema::dropIfExists('historial_ordenes_tracking');
    }
};

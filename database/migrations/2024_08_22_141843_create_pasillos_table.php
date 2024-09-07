<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasillosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pasillos', function (Blueprint $table) {
            $table->id();
            // Relación con bodega
            $table->foreignId('id_bodega')->constrained('bodegas');
            $table->string('nombre');
            $table->integer('capacidad')->default(0);
            $table->boolean('estado')->default(1); 
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
        Schema::dropIfExists('pasillos');
    }
}


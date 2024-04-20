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
        Schema::table('paquetes', function (Blueprint $table) {
            $table->foreign(['id_estado_paquete'], 'paquetes_ibfk_2')->references(['id'])->on('estado_paquetes')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_tipo_paquete'], 'paquetes_ibfk_1')->references(['id'])->on('tipo_paquete')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_empaque'], 'paquetes_ibfk_3')->references(['id'])->on('empaquetado')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paquetes', function (Blueprint $table) {
            $table->dropForeign('paquetes_ibfk_2');
            $table->dropForeign('paquetes_ibfk_1');
            $table->dropForeign('paquetes_ibfk_3');
        });
    }
};

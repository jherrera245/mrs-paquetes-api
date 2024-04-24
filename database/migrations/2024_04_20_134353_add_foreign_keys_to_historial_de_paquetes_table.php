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
        Schema::table('historial_de_paquetes', function (Blueprint $table) {
            $table->foreign(['id_usuario'], 'historial_de_paquetes_fk_id_user')->references(['id'])->on('users');
            $table->foreign(['id_paquete'], 'historial_de_paquetes_fk_id_paquete')->references(['id'])->on('paquetes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historial_de_paquetes', function (Blueprint $table) {
            $table->dropForeign('historial_de_paquetes_fk_id_user');
            $table->dropForeign('historial_de_paquetes_fk_id_paquete');
        });
    }
};

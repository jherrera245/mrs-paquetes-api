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
        Schema::table('users', function (Blueprint $table) {
            $table->foreign(['id_empleado'], 'users_fk_id_empleado')->references(['id'])->on('empleados');
            $table->foreign(['id_cliente'], 'users_fk_id_cliente')->references(['id'])->on('clientes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_fk_id_empleado');
            $table->dropForeign('users_fk_id_cliente');
        });
    }
};

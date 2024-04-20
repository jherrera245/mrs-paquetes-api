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
        Schema::table('usuarios', function (Blueprint $table) {
            $table->foreign(['id_rol'], 'usuarios_ibfk_3')->references(['id'])->on('roles')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_empleado'], 'usuarios_ibfk_1')->references(['id'])->on('empleados')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_estado'], 'usuarios_ibfk_4')->references(['id'])->on('estado_usuarios')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign('usuarios_ibfk_3');
            $table->dropForeign('usuarios_ibfk_1');
            $table->dropForeign('usuarios_ibfk_4');
        });
    }
};

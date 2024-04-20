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
        Schema::table('asignar_roles', function (Blueprint $table) {
            $table->foreign(['id_rol'], 'asignar_roles_ibfk_2')->references(['id'])->on('roles')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['id_permiso'], 'asignar_roles_ibfk_1')->references(['id'])->on('permisos')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asignar_roles', function (Blueprint $table) {
            $table->dropForeign('asignar_roles_ibfk_2');
            $table->dropForeign('asignar_roles_ibfk_1');
        });
    }
};

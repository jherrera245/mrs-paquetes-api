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
        Schema::table('tipo_incidencia', function (Blueprint $table) {
            $table->foreign(['estado'], 'tipo_incidencia_ibfk_1')->references(['id'])->on('estado_incidencias')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tipo_incidencia', function (Blueprint $table) {
            $table->dropForeign('tipo_incidencia_ibfk_1');
        });
    }
};

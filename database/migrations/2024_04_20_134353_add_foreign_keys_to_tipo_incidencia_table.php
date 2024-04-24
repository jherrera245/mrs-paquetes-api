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
            $table->foreign(['id_estado'], 'tipo_incidencia_fk_id_id_estado')->references(['id'])->on('estado_incidencias');
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
            $table->dropForeign('tipo_incidencia_fk_id_id_estado');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNitNrcGiroNombreEmpresaDireccion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('nit', 20)->nullable();
            $table->string('nrc', 20)->nullable();
            $table->string('giro')->nullable();
            $table->string('nombre_empresa', 200)->nullable();
            $table->longText('direccion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('nit');
            $table->dropColumn('nrc');
            $table->dropColumn('giro');
            $table->dropColumn('nombre_empresa');
            $table->dropColumn('direccion');
        });
    }
}

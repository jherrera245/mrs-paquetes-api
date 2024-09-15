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
        Schema::table('vehiculos', function (Blueprint $table) {
            $table->enum('tipo', ['camion', 'moto'])->default('camion');

            // Actualizar columnas existentes para permitir valores null
            $table->unsignedBigInteger('id_empleado_apoyo')->nullable()->change();
            $table->unsignedBigInteger('id_bodega')->nullable()->change();

            $table->foreign(['id_estado'], 'vehiculos_ibfk_6')->references(['id'])->on('estado_vehiculos');
            $table->foreign(['id_modelo'], 'vehiculos_ibfk_2')->references(['id'])->on('modelos');
            $table->foreign(['id_empleado_apoyo'], 'vehiculos_ibfk_5')->references(['id'])->on('empleados');
            $table->foreign(['id_marca'], 'vehiculos_ibfk_1')->references(['id'])->on('marcas');
            $table->foreign(['id_empleado_conductor'], 'vehiculos_ibfk_4')->references(['id'])->on('empleados');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehiculos', function (Blueprint $table) {
            // Revertir los cambios, eliminando la columna 'tipo' y restaurando las restricciones de no nulidad
            $table->dropColumn('tipo');

            $table->unsignedBigInteger('id_empleado_apoyo')->nullable(false)->change();
            $table->unsignedBigInteger('id_bodega')->nullable(false)->change();

            $table->dropForeign('vehiculos_ibfk_6');
            $table->dropForeign('vehiculos_ibfk_2');
            $table->dropForeign('vehiculos_ibfk_5');
            $table->dropForeign('vehiculos_ibfk_1');
            $table->dropForeign('vehiculos_ibfk_4');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDireccionRecoleccionOrdenesTable extends Migration
{
    /**
     * Ejecuta las migraciones.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ordenes', function (Blueprint $table) {
            // Agrega la columna direccion_recoleccion
            $table->foreignId('direccion_recoleccion')
                  ->nullable() // Permite valores null
                  ->after('id_direccion'); // Opcional: coloca la columna después de la columna 'id_direccion'
        });

        Schema::table('ordenes', function (Blueprint $table) {
            // Define la columna direccion_recoleccion como una llave foránea
            $table->foreign('direccion_recoleccion')
                  ->references('id')
                  ->on('direcciones')
                  ->onDelete('set null'); // Acción en caso de eliminación en la tabla referenciada
        });
    }

    /**
     * Revierte las migraciones.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ordenes', function (Blueprint $table) {
            // Elimina la clave foránea
            $table->dropForeign(['direccion_recoleccion']);
            
            // Elimina la columna
            $table->dropColumn('direccion_recoleccion');
        });
    }
}


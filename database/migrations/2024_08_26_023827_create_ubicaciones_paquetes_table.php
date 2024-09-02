<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUbicacionesPaquetesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ubicaciones_paquetes', function (Blueprint $table) {
            $table->id();
            
            // Relación con paquetes
            $table->foreignId('id_paquete')
                  ->constrained('paquetes')
                  ->onDelete('cascade'); // Elimina la asignación si el paquete se elimina

            // Relación con ubicaciones
            $table->foreignId('id_ubicacion')
                  ->constrained('ubicaciones')
                  ->onDelete('cascade'); // Elimina la asignación si la ubicación se elimina

            $table->boolean('estado')->default(1); // 1 puede significar 'en bodega' por defecto

            $table->timestamps();

            // Índice único para evitar duplicados de paquete en la misma ubicación
            $table->unique(['id_paquete', 'id_ubicacion']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ubicaciones_paquetes');
    }
}

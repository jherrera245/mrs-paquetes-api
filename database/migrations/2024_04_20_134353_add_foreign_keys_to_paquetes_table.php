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
        Schema::table('paquetes', function (Blueprint $table) {
            $table->foreign(['id_estado_paquete'], 'paquetes_fk_id_estado_paquetes')->references(['id'])->on('estado_paquetes');
            $table->foreign(['id_tamano_paquete'], 'paquetes_fk_id_tamano_paquete')->references(['id'])->on('tamano_paquete');
            $table->foreign(['id_tipo_paquete'], 'paquetes_fk_id_tipo_paquete')->references(['id'])->on('tipo_paquete');
            $table->foreign(['id_empaque'], 'paquetes_fk_id_empaque')->references(['id'])->on('empaquetado');
      
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paquetes', function (Blueprint $table) {
            $table->dropForeign('paquetes_fk_id_estado_paquetes');
            $table->dropForeign('paquetes_fk_id_tamano_paquete');
            $table->dropForeign('paquetes_fk_id_tipo_paquete');
            $table->dropForeign('paquetes_fk_id_empaque');
            
        });
    }
};

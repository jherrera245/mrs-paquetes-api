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
        Schema::table('detalle_orden', function (Blueprint $table) {
            // Hacer que el campo 'id_paquete' sea nullable
            $table->foreignId('id_paquete')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detalle_orden', function (Blueprint $table) {
            // Revertir el cambio si es necesario
            $table->foreignId('id_paquete')->nullable(false)->change();
        });
    }
};

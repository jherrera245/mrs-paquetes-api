<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentStatusToOrdenesTable extends Migration
{
    public function up()
    {
        Schema::table('ordenes', function (Blueprint $table) {
            $table->enum('estado_pago', ['pendiente', 'pagado'])->default('pendiente');
        });
    }

    public function down()
    {
        Schema::table('ordenes', function (Blueprint $table) {
            $table->dropColumn('estado_pago');
        });
    }
}

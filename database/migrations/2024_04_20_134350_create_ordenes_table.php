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
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_cliente')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('id_direccion');
            $table->foreignId('id_tipo_pago');
            $table->decimal('total_pagar', 10);
            $table->decimal('costo_adicional', 10);
            $table->longText('concepto');
            $table->boolean('finished')->default(0);
            $table->string('numero_seguimiento')->nullable(); 
            $table->enum('tipo_documento', ['consumidor_final', 'credito_fiscal']);
            $table->enum('tipo_orden', ['orden', 'preorden']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordenes');
    }
};

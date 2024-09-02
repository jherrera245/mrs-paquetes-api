<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ubicaciones', function (Blueprint $table) {
            $table->id(); // Asegúrate de usar $table->id() o $table->bigIncrements('id')
            $table->string('nomenclatura')->unique();
            $table->foreignId('id_bodega')->constrained('bodegas');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ubicaciones');
    }
};

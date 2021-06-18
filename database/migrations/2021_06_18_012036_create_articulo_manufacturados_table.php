<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticuloManufacturadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articulo_manufacturados', function (Blueprint $table) {
            $table->id();
            $table->integer('tiempoEstimadoCocina');
            $table->string('denominacion');
            $table->double('precioVenta');
            $table->text('imagen');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articulo_manufacturados');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticuloInsumosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articulo_insumos', function (Blueprint $table) {
            $table->id();
                $table->string('denominación');
                $table->double('precioCompra');
                $table->double('precioVenta');
                $table->double('stockActual');
                $table->double('stockMinimo');
                $table->string('unidadMedida');
                $table->boolean('esInsumo');
            $table->unsignedBigInteger('rubro_articulo_id');
            $table->foreign('rubro_articulo_id')->references('id')->on('rubro_articulos');
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
        Schema::dropIfExists('articulo_insumos');
    }
}

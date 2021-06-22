<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticuloManufacturadoDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articulo_manufacturado_detalles', function (Blueprint $table) {
            $table->id();
            $table->double('cantidad');
            $table->string('unidadMedida');
            $table->unsignedBigInteger('articulo_insumo_id')->nullable();
            $table->foreign('articulo_insumo_id')->references('id')->on('articulo_insumos')->constrained()->index('ai_id');
            $table->unsignedBigInteger('articulo_manufacturado_id')->nullable();
            $table->foreign('articulo_manufacturado_id')->references('id')->on('articulo_manufacturados')->constrained()->index('am_id');
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
        Schema::dropIfExists('articulo_manufacturado_detalles');
    }
}

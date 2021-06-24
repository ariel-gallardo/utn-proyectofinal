<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetallePedidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_pedidos', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad')->default(1);
            $table->double('subtotal')->nullable();
            $table->unsignedBigInteger('pedido_id');
            $table->foreign('pedido_id')->references('id')->on('pedidos');
            $table->unsignedBigInteger('articulo_insumo_id')->nullable();
            $table->foreign('articulo_insumo_id')->references('id')->on('articulo_insumos');
            $table->unsignedBigInteger('articulo_manufacturado_id')->nullable();
            $table->foreign('articulo_manufacturado_id')->references('id')->on('articulo_manufacturados');
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
        Schema::dropIfExists('detalle_pedidos');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMercadoPagoDatosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mercado_pago_datos', function (Blueprint $table) {
            $table->string('identificadorPago')->index();
            $table->date('fechaCreacion')->nullable();
            $table->date('fechaAprobacion')->nullable();
            $table->string('formaPago')->nullable();
            $table->string('metodoPago')->nullable();
            $table->string('nroTarjeta')->nullable();
            $table->string('estado')->nullable();
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
        Schema::dropIfExists('mercado_pago_datos');
    }
}

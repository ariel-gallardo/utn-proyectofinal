<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRubroArticulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rubro_articulos', function (Blueprint $table) {
            $table->id();
            $table->string('denominacion');
            $table->unsignedBigInteger('rubro_articulo_id')->nullable();
            $table->foreign('rubro_articulo_id')->references('id')->on('rubro_articulos')->onDelete('cascade');
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
        Schema::dropIfExists('rubro_articulos');
    }
}

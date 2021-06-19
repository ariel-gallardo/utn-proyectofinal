<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
                $table->string('correo');
                $table->string('clave');
                $table->text('imagen')->nullable();
                $table->unsignedBigInteger('persona_id')->nullable();
                $table->foreign('persona_id')->references('id')->on('personas')->onDelete('cascade');
                $table->unsignedBigInteger('rol_id')->default(1);
                $table->foreign('rol_id')->references('id')->on('rols');
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
        Schema::dropIfExists('users');
    }
}

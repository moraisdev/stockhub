<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopplanosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopplanos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('descricao')->nullable();
            $table->string('valor')->nullable();
            $table->string('titulo')->nullable();
            $table->string('ciclo')->nullable();
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
        Schema::dropIfExists('shopplanos');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartxAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cartx_apps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('shop_id')->comment('id da loja');
            $table->string('domain')->nullable()->comment('dominio (login) do cartx');
            $table->string('token')->nullable()->comment('token do cartx');
            $table->softDeletes();
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
        Schema::dropIfExists('cartx_apps');
    }
}

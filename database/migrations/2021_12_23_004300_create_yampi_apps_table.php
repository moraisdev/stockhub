<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYampiAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yampi_apps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('shop_id')->comment('id da loja');
            $table->string('domain')->nullable()->comment('dominio (login) do yampi');
            $table->string('app_key')->nullable()->comment('dominio (login) do yampi');
            $table->string('app_password')->nullable()->comment('token do yampi');
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
        Schema::dropIfExists('yampi_apps');
    }
}

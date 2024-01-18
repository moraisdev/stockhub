<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWoocommerceAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('woocommerce_apps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('shop_id')->comment('id da loja');
            $table->string('domain')->nullable()->comment('dominio (login) do woocommerce');
            $table->string('app_key')->nullable()->comment('key do woocommerce');
            $table->string('app_password')->nullable()->comment('secret do woocommerce');
            $table->integer('automatic_order_update')->nullable()->comment('order update do woocommerce');
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
        Schema::dropIfExists('woocommerce_apps');
    }
}

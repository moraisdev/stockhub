<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopifyAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopify_apps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('shop_id');
            $table->string('domain')->nullable();
            $table->string('app_key')->nullable();
            $table->string('app_password')->nullable();
            $table->boolean('automatic_order_update')->nullable()->default(false);
            $table->timestamps();
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
        Schema::dropIfExists('shopify_apps');
    }
}

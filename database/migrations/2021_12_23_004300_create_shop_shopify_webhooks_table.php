<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopShopifyWebhooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_shopify_webhooks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('shop_id');
            $table->string('topic')->nullable();
            $table->string('address')->nullable();
            $table->string('format')->nullable();
            $table->boolean('success')->nullable()->default(true);
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
        Schema::dropIfExists('shop_shopify_webhooks');
    }
}

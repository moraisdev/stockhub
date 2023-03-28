<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsedCouponInternalSubscriptionShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('used_coupon_internal_subscription_shops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('coupon_internal_subscription_shop_id');
            $table->bigInteger('token_card_id');
            $table->bigInteger('shop_id');
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
        Schema::dropIfExists('used_coupon_internal_subscription_shops');
    }
}

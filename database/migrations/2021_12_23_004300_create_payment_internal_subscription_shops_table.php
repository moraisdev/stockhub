<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentInternalSubscriptionShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_internal_subscription_shops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('internal_subscription_shop_id');
            $table->char('status', 100)->nullable();
            $table->integer('transaction_id')->nullable();
            $table->text('payment_json')->nullable();
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
        Schema::dropIfExists('payment_internal_subscription_shops');
    }
}

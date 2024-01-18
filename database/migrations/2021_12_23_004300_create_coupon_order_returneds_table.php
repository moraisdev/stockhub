<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponOrderReturnedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_order_returneds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('order_returned_id');
            $table->bigInteger('supplier_id');
            $table->double('amount', 8, 2);
            $table->enum('status', ['pending', 'used'])->default('pending');
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
        Schema::dropIfExists('coupon_order_returneds');
    }
}

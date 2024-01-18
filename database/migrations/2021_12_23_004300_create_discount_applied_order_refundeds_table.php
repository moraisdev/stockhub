<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountAppliedOrderRefundedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_applied_order_refundeds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('order_returned_id');
            $table->bigInteger('supplier_order_group_id');
            $table->double('amount', 8, 2);
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
        Schema::dropIfExists('discount_applied_order_refundeds');
    }
}

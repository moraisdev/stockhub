<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderReturnedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_returneds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('supplier_order_id');
            $table->bigInteger('order_id');
            $table->bigInteger('shop_id');
            $table->bigInteger('supplier_id');
            $table->enum('decision', ['resend', 'credit'])->nullable();
            $table->enum('status', ['pending', 'solved'])->default('pending');
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
        Schema::dropIfExists('order_returneds');
    }
}

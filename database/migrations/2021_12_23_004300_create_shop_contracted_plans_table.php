<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopContractedPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_contracted_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('shop_id');
            $table->text('plan_id')->nullable();
            $table->text('subscription')->nullable();
            $table->text('transaction')->nullable();
            $table->text('subscription_status')->nullable();
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
        Schema::dropIfExists('shop_contracted_plans');
    }
}

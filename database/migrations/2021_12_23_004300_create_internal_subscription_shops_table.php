<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInternalSubscriptionShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('internal_subscription_shops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('shop_id');
            $table->integer('plan_id')->nullable();
            $table->enum('status', ['active', 'inactive', 'overdue', 'pending'])->nullable();
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
        Schema::dropIfExists('internal_subscription_shops');
    }
}

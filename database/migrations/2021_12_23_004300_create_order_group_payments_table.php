<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderGroupPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_group_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('group_id')->nullable();
            $table->integer('shop_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->string('gateway_id', 45)->nullable();
            $table->decimal('amount', 10)->nullable();
            $table->string('status', 45)->nullable();
            $table->string('hash', 200)->nullable()->unique('hash_UNIQUE');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
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
        Schema::dropIfExists('order_group_payments');
    }
}

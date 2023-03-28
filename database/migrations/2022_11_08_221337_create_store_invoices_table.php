<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('shop_id')->nullable();
            $table->string('plan')->nullable();
            $table->double('sub_total', 16, 2);
            $table->double('discount', 16, 2)->default(0);
            $table->double('total', 16, 2);
            $table->double('received_amount', 16, 2)->default(0);
            $table->date('due_date');
            $table->date('date_payment');
            $table->string('status')->nullable();
            $table->string('payment')->nullable();
            $table->string('payment_bank')->nullable();
            $table->string('transaction')->nullable();
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
        Schema::dropIfExists('store_invoices');
    }
}

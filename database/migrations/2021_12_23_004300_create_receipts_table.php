<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('supplier_id')->nullable();
            $table->bigInteger('shop_id')->nullable();
            $table->bigInteger('customer_id')->nullable();
            $table->enum('type', ['order', 'shipping']);
            $table->enum('to', ['shop', 'customer', 'supplier']);
            $table->string('file');
            $table->string('description')->nullable();
            $table->decimal('total_amount', 10);
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
        Schema::dropIfExists('receipts');
    }
}

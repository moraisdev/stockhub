<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('supplier_order_id')->nullable();
            $table->integer('product_variant_id')->nullable();
            $table->decimal('amount', 10)->nullable();
            $table->integer('quantity')->nullable();
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
        Schema::dropIfExists('supplier_order_items');
    }
}

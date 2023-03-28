<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('order_id');
            $table->integer('product_variant_id')->nullable();
            $table->enum('external_service', ['shopify', 'safecheckout', 'cartx', 'woocommerce', 'yampi', 'planilha'])->nullable();
            $table->string('external_product_id')->nullable();
            $table->string('external_variant_id')->nullable();
            $table->string('sku')->nullable();
            $table->string('title')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('amount', 10)->nullable();
            $table->decimal('external_price', 10)->nullable();
            $table->boolean('charge')->nullable();
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
        Schema::dropIfExists('order_items');
    }
}

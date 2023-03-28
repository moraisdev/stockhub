<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('shop_id');
            $table->integer('customer_id')->nullable();
            $table->enum('external_service', ['shopify', 'safecheckout', 'cartx', 'woocommerce', 'yampi', 'planilha'])->nullable();
            $table->string('external_id')->nullable()->unique('external_id_UNIQUE');
            $table->string('name', 100)->nullable();
            $table->string('email')->nullable();
            $table->decimal('items_amount', 10)->default(0);
            $table->decimal('shipping_amount', 10)->default(0);
            $table->decimal('amount', 10)->default(0);
            $table->decimal('external_price', 10)->default(0);
            $table->decimal('external_usd_price', 10)->default(0);
            $table->string('landing_site')->nullable();
            $table->enum('status', ['pending', 'paid', 'canceled'])->nullable();
            $table->boolean('supplier_order_created')->nullable()->default(false);
            $table->timestamp('external_created_at')->nullable();
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
        Schema::dropIfExists('orders');
    }
}

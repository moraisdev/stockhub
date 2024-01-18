<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierOrderShippingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_order_shippings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('supplier_id')->nullable();
            $table->integer('supplier_order_id')->nullable();
            $table->decimal('amount', 10)->nullable()->default(0);
            $table->enum('status', ['pending', 'sent', 'completed', 'canceled'])->nullable()->default('pending');
            $table->string('company')->nullable();
            $table->string('tracking_url')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('external_service')->nullable()->default('shopify');
            $table->string('external_fulfillment_id')->nullable();
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
        Schema::dropIfExists('supplier_order_shippings');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('display_id')->nullable();
            $table->integer('order_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->integer('payment_id')->nullable();
            $table->integer('group_id')->nullable();
            $table->decimal('amount', 10)->nullable();
            $table->decimal('total_amount', 10)->nullable();
            $table->enum('status', ['pending', 'paid', 'canceled', 'returned'])->nullable();
            $table->text('comments')->nullable();
            $table->boolean('exported_to_bling')->nullable()->default(false);
            $table->unsignedTinyInteger('exported_to_china_division')->default('0');
            $table->tinyInteger('exported_to_total_express')->default(0);
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
        Schema::dropIfExists('supplier_orders');
    }
}

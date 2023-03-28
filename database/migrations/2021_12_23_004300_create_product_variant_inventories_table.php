<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariantInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variant_inventories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('product_variant_id');
            $table->string('sku')->nullable();
            $table->string('barcode')->nullable();
            $table->integer('quantity');
            $table->boolean('allow_out_of_stock_purchases');
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
        Schema::dropIfExists('product_variant_inventories');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('product_id');
            $table->string('url_hash', 300)->nullable();
            $table->string('title', 500);
            $table->string('img_source')->nullable();
            $table->decimal('price', 10)->default(0);
            $table->decimal('cost', 10)->default(0);
            $table->decimal('compare_at_price', 10)->nullable();
            $table->boolean('requires_shipping')->default(true);
            $table->decimal('shipping_cost', 10)->nullable();
            $table->float('weight_in_grams', 10, 0)->nullable();
            $table->enum('weight_unit', ['g', 'kg', 'lb', 'oz'])->default('g');
            $table->float('width', 10, 0)->nullable();
            $table->float('height', 10, 0)->nullable();
            $table->float('depth', 10, 0)->nullable();
            $table->char('origin_country', 5)->nullable();
            $table->string('harmonized_system_code', 200)->nullable();
            $table->string('sku', 500)->nullable();
            $table->boolean('published')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->decimal('internal_cost', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_variants');
    }
}

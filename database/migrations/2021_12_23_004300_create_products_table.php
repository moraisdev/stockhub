<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('category_id')->nullable();
            $table->integer('supplier_id');
            $table->string('title', 500);
            $table->decimal('ignore_percentage_on_tax', 10)->nullable();
            $table->boolean('icms_exemption')->nullable()->default(true);
            $table->string('currency', 5)->nullable()->default('R$');
            $table->string('ncm')->nullable();
            $table->string('hash')->nullable();
            $table->text('description')->nullable();
            $table->string('url_title', 500)->nullable();
            $table->string('img_source')->nullable();
            $table->string('seo_title', 500)->nullable();
            $table->text('seo_description')->nullable();
            $table->boolean('public')->default(false);
            $table->boolean('show_in_products_page')->nullable()->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->enum('shipping_method_china_division', ['PUAM', 'CDEUB', 'SZEUB'])->nullable();
            $table->double('packing_weight', 8, 2)->nullable();
            $table->enum('products_from', ['BR', 'CN'])->default('BR');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}

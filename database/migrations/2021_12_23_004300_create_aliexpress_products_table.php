<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAliexpressProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aliexpress_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('aliexpress_id', 600);
            $table->string('title', 600)->nullable();
            $table->string('url_title', 600)->nullable();
            $table->decimal('min_cost', 10)->nullable();
            $table->decimal('max_cost', 10)->nullable();
            $table->string('images_ids', 500)->nullable();
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
        Schema::dropIfExists('aliexpress_products');
    }
}

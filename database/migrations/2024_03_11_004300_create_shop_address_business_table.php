<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopAddressBusinessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_address_business', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('shop_id')->nullable();
            $table->string('street_company')->nullable();
            $table->string('number_company')->nullable();
            $table->string('district_company')->nullable();
            $table->string('complement_company')->nullable();
            $table->string('city_company')->nullable();
            $table->string('state_code_company')->nullable();
            $table->string('country_company')->default('brazil');
            $table->string('zipcode_company', 20)->nullable();
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
        Schema::dropIfExists('shop_address_business');
    }
}

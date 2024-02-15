<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressCompanyToShopAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_address', function (Blueprint $table) {
            $table->string('street_company')->nullable();
            $table->string('number_company')->nullable();
            $table->string('district_company')->nullable();
            $table->string('complement_company')->nullable();
            $table->string('city_company')->nullable();
            $table->string('state_code_company')->nullable();
            $table->string('country_company')->default('brazil');
            $table->string('zipcode_company', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_address', function (Blueprint $table) {
            //
        });
    }
}

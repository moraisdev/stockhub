<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_address', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('supplier_id')->nullable();
            $table->enum('type', ['default', 'shipment'])->nullable()->default('default');
            $table->string('street')->nullable();
            $table->string('number', 45)->nullable();
            $table->string('complement', 45)->nullable();
            $table->string('district', 45)->nullable();
            $table->string('city')->nullable();
            $table->string('state_code', 2)->nullable();
            $table->string('country')->nullable();
            $table->string('zipcode', 20)->nullable();
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
        Schema::dropIfExists('supplier_address');
    }
}

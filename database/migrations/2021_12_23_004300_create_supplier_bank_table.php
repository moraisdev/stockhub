<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierBankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_bank', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('supplier_id')->nullable();
            $table->string('code', 10)->nullable();
            $table->char('account_type', 2)->nullable();
            $table->text('agency')->nullable();
            $table->integer('agency_digit')->nullable();
            $table->integer('account')->nullable();
            $table->integer('account_digit')->nullable();
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
        Schema::dropIfExists('supplier_bank');
    }
}

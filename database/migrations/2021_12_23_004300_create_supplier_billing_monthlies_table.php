<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierBillingMonthliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_billing_monthlies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('start_date');
            $table->date('final_date');
            $table->double('total_billing', 8, 2)->nullable();
            $table->bigInteger('supplier_id');
            $table->enum('status', ['pending', 'executed'])->default('pending');
            $table->softDeletes();
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
        Schema::dropIfExists('supplier_billing_monthlies');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierOrderGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_order_group', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('shop_id')->nullable();
            $table->string('description', 20)->nullable();
            $table->string('status', 45)->nullable()->default('pending');
            $table->string('payment_json', 10000)->nullable();
            $table->integer('transaction_id')->nullable();
            $table->decimal('bankslip_amount', 10)->nullable();
            $table->string('bankslip_url', 100)->nullable();
            $table->string('bankslip_digitable_line', 100)->nullable();
            $table->string('bankslip_barcode', 100)->nullable();
            $table->date('bankslip_duedate')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->softDeletes();
            $table->text('status_pix')->nullable();
            $table->text('message_pix')->nullable();
            $table->text('description_pix')->nullable();
            $table->text('qrcode_pix')->nullable();
            $table->text('key_pix')->nullable();
            $table->text('payment_json_pix')->nullable();
            $table->integer('transaction_id_pix')->nullable();
            $table->enum('paid_by', ['boleto', 'pix'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_order_group');
    }
}

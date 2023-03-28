<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorreiosContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('correios_contracts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('supplier_id')->nullable();
            $table->integer('active')->nullable()->default(0);
            $table->string('sigep_user', 100)->nullable();
            $table->string('sigep_password', 100)->nullable();
            $table->string('contract_id', 45)->nullable();
            $table->string('post_card_id')->nullable();
            $table->string('administrative_code', 100)->nullable();
            $table->string('service_code', 20)->nullable();
            $table->text('services')->nullable();
            $table->dateTime('post_card_last_check')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
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
        Schema::dropIfExists('correios_contracts');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreteMelhorEnviosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('frete_melhor_envios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('order_id');
            $table->bigInteger('supplier_id');
            $table->bigInteger('supplier_order_id');
            $table->double('amount', 8, 2)->nullable();
            $table->text('service_id')->nullable();
            $table->text('melhor_envio_id')->nullable();
            $table->text('protocol')->nullable();
            $table->text('status')->nullable();
            $table->text('tracking')->nullable();
            $table->text('tag_url')->nullable();
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
        Schema::dropIfExists('frete_melhor_envios');
    }
}

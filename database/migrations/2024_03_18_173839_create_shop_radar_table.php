<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopRadarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_radar', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('status');
            $table->string('document')->nullable();
            $table->string('social_contract')->nullable();
            $table->string('cnpj')->nullable();
            $table->string('bank_extract')->nullable();
            $table->string('whatsapp');
            $table->string('radar_qualification')->nullable();
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
        Schema::dropIfExists('shop_radar');
    }
}

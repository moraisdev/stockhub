<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorreiosSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('correios_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('supplier_id')->nullable();
            $table->integer('percentage')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('correios_services_bling')->nullable()->comment('servicos utilizados pelos correios para entregar as encomendas no bling');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('correios_settings');
    }
}

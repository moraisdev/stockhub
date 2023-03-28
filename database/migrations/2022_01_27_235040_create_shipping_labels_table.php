<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_labels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('orders_ids')->nullable(); //vetor com os ids das ordens que tem essa etiqueta
            $table->text('url_planilha')->nullable(); //url da planilha
            $table->text('url_labels')->nullable(); //url das etiquetas
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
        Schema::dropIfExists('shipping_labels');
    }
}

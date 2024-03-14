<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryDeadlineCostPriceTrackingCodeToCollectiveImportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('collective_import', function (Blueprint $table) {
            // Adicionando os novos campos
            $table->integer('delivery_deadline')->nullable();
            $table->decimal('cost_price', 8, 2)->nullable();
            $table->string('tracking_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('collective_import', function (Blueprint $table) {
            $table->dropColumn('delivery_deadline');
            $table->dropColumn('cost_price');
            $table->dropColumn('tracking_code');
        });
    }
}

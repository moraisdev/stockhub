<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditExternalServiceInOrderItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            DB::statement("ALTER TABLE order_items MODIFY external_service ENUM('shopify','safecheckout', 'cartx', 'woocommerce', 'yampi', 'planilha')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('order_items', function (Blueprint $table) {
        //     DB::statement("ALTER TABLE order_items MODIFY external_service ENUM('shopify','safecheckout', 'cartx', 'woocommerce')");
        // });
    }
}

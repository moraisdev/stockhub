<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

class EditExternalServiceInOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            DB::statement("ALTER TABLE orders MODIFY external_service ENUM('shopify','safecheckout', 'cartx', 'woocommerce', 'yampi', 'planilha')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('orders', function (Blueprint $table) {
        //     DB::statement("ALTER TABLE orders MODIFY external_service ENUM('shopify','safecheckout', 'cartx', 'woocommerce')");
        // });
    }
}

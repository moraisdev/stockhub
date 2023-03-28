<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditExternalServiceInCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            DB::statement("ALTER TABLE customers MODIFY external_service ENUM('shopify','safecheckout', 'cartx', 'woocommerce', 'yampi', 'planilha')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('customers', function (Blueprint $table) {
        //     DB::statement("ALTER TABLE order_items MODIFY external_service ENUM('shopify','safecheckout', 'cartx', 'woocommerce')");
        // });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class AddSupplierDetailsToCollectiveImportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('collective_import', function (Blueprint $table) {
            // Adiciona os novos campos
            $table->string('china_supplier_name')->after('packing_list_path');
            $table->string('china_supplier_contact')->after('china_supplier_name');
            $table->string('product_hs_code')->after('china_supplier_contact');
            $table->string('product_description')->after('product_hs_code');
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
            $table->dropColumn(['china_supplier_name', 'china_supplier_contact', 'product_hs_code', 'product_description']);
        });
    }
}
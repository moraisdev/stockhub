<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAndShopIdToCollectiveImportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('collective_import', function (Blueprint $table) {
            $table->string('status')->nullable()->comment('EM ANALISE, PAGAMENTO PENDENTE, PAGO, ENVIADO, ENTREGUE, CANCELADO');
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('set null');
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
            //
        });
    }
}

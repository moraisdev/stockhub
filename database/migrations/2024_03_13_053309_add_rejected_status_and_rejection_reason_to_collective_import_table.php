<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRejectedStatusAndRejectionReasonToCollectiveImportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('collective_import', function (Blueprint $table) {
            $table->string('status')->nullable()->comment('EM ANALISE, PAGAMENTO PENDENTE, PAGO, ENVIADO, ENTREGUE, CANCELADO, REJEITADO')->change();
            $table->string('rejection_reason', 255)->nullable()->after('status')->comment('Motivo da rejeição');
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

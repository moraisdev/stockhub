<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('safe2pay_id')->nullable();
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('commercial_name')->nullable();
            $table->string('responsible_name')->nullable();
            $table->string('responsible_document')->nullable();
            $table->string('email')->unique('users_email_unique');
            $table->string('document')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('tech_name')->nullable();
            $table->string('tech_document')->nullable();
            $table->string('tech_email')->nullable();
            $table->decimal('mawa_post_tax', 10)->nullable()->default(0);
            $table->string('password');
            $table->rememberToken();
            $table->string('hash', 100)->nullable();
            $table->string('private_hash', 100)->nullable();
            $table->string('password_recovery_hash')->nullable();
            $table->string('payment_api_token')->nullable();
            $table->string('payment_api_secret')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('safe2pay_subaccount_id', 100)->nullable();
            $table->string('bling_apikey', 100)->nullable();
            $table->enum('status', ['active', 'inactive'])->nullable()->default('active');
            $table->enum('login_status', ['authorized', 'unauthorized'])->nullable()->default('authorized');
            $table->boolean('terms_agreed')->nullable()->default(false);
            $table->boolean('use_shipment_address')->nullable()->default(false);
            $table->enum('shipping_method', ['melhor_envio', 'correios', 'total_express', 'no_shipping'])->nullable();
            $table->decimal('shipping_fixed_fee', 10)->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->enum('bling_automatic_tracking_code', ['true', 'false'])->default('false');
            $table->string('china_division_apikey')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suppliers');
    }
}

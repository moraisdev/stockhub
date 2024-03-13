<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('responsible_name')->nullable();
            $table->string('responsible_document')->nullable() -> unique();
            $table->string('document')->nullable()-> unique();
            $table->string('fantasy_name')->nullable();
            $table->string('corporate_name')->nullable();
            $table->string('state_registration')->nullable();
            $table->string('email')->nullable();
            $table->string('hash')->nullable();
            $table->string('private_hash')->nullable();
            $table->string('phone')->nullable();
            $table->string('password')->nullable();
            $table->string('remember_token')->nullable();
            $table->string('password_recovery_hash')->nullable();
            $table->dateTime('email_verified_at')->nullable();
            $table->enum('status', ['active', 'inactive'])->nullable()->default('inactive');
            $table->enum('login_status', ['authorized', 'unauthorized'])->nullable()->default('authorized');
            $table->boolean('terms_agreed')->nullable()->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shops');
    }
}

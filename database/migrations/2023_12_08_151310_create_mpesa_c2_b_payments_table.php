<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mpesa_c2_b_payments', function (Blueprint $table) {
            $table->id();
            $table->string('TransactionType')->nullable();
            $table->string('TransID')->nullable();
            $table->string('TransTime')->nullable();
            $table->decimal('TransAmount', 8, 2)->nullable();
            $table->string('BusinessShortCode')->nullable();
            $table->string('BillRefNumber')->nullable();
            $table->string('InvoiceNumber')->nullable();
            $table->decimal('OrgAccountBalance', 8, 2)->nullable();
            $table->string('ThirdPartyTransID')->nullable();
            $table->string('MSISDN')->nullable();
            $table->string('FirstName')->nullable();
            $table->string('MiddleName')->nullable();
            $table->string('LastName')->nullable();
            $table->text('ResponseData')->nullable();
            $table->string('MerchantRequestID')->nullable();
            $table->string('CheckoutRequestID')->nullable();
            $table->string('type')->nullable();
            $table->boolean('used')->default(0);
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
        Schema::dropIfExists('mpesa_c2_b_payments');
    }
};

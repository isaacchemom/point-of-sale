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
        Schema::create('current_stk_push_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('c2b_id');
            $table->string('MerchantRequestID');
            $table->string('CheckoutRequestID');
            $table->boolean('c2bdata_received')->default(0);
            $table->enum('status', ['PENDING', 'COMPLETED', 'FAILED'])->default('PENDING');
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
        Schema::dropIfExists('current_stk_push_requests');
    }
};

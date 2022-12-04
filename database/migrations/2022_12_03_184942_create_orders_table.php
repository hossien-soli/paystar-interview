<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // $table->bigInteger('user_id')->unsigned()->index();
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->string('card_number');
            $table->double('amount')->unsigned();

            $table->string('token')->nullable();
            $table->double('payment_amount')->unsigned()->nullable();  // محاسبه کارمزد
            $table->string('ref_num')->nullable();
            
            $table->string('transaction_id')->nullable();
            $table->string('tracking_code')->nullable();

            $table->boolean('paid')->nullable();

            $table->dateTime('created_at');
            $table->dateTime('paid_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};

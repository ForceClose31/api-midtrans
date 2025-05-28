<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
public function up()
{
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->string('order_id')->unique();
        $table->string('name');
        $table->string('email');
        $table->integer('amount');
        $table->string('transaction_status')->nullable();
        $table->string('payment_type')->nullable();
        $table->string('transaction_id')->nullable();
        $table->timestamps();
    });
}


    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}

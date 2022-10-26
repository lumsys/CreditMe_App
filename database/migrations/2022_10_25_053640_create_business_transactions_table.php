<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('principal_id');
            $table->string('email');
            $table->string('name');
            $table->string('unique_code');
            $table->string('description');
            $table->string('account_number');
            $table->string('bankName');
            $table->string('bankCode');
            $table->decimal('payable')->nullable();
            $table->foreignId('moto_id');
            $table->string('productId')->nullable();
            $table->dateTime('transactionDate')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))->nullable();
            $table->decimal('merchantReference')->nullable();
            $table->string('fiName')->nullable();
            $table->string('paymentMethod')->nullable();
            $table->decimal('payThruReference')->nullable();
            $table->string('paymentReference')->nullable();
            $table->string('responseCode')->nullable();
            $table->string('responseDescription')->nullable();
            $table->enum('status', array(1, 2, 3))->nullable();
            $table->decimal('amount')->nullable();
            $table->decimal('commission')->nullable();
            $table->decimal('residualAmount')->nullable();
            $table->string('customerName')->nullable();
            $table->string('resultCode')->nullable();
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
        Schema::dropIfExists('business_transactions');
    }
}
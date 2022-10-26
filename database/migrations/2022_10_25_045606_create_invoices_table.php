<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id');
            $table->string('email');
            $table->integer('user_id');
            $table->date('issue_date');
            $table->date('due_days')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->nullable();
            $table->string('total')->nullable();
            $table->string('vat')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('description')->nullable();
            $table->integer('qty')->nullable();
            $table->string('rate')->nullable();
            $table->string('amount')->nullable();
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
        Schema::dropIfExists('invoices');
    }
}
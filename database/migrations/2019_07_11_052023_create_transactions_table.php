<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('initiator')->nullable();
            $table->foreign('initiator')->references('id')->on('users');
            $table->decimal('old_balance', 16, 2)->default(0);
            $table->decimal('amount', 16, 2)->default(0);
            $table->decimal('new_balance', 16, 2)->default(0);
            $table->unsignedSmallInteger('type')->nullable();
            $table->nullableMorphs('reason');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('ticket_categories');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('initiator_id');
            $table->foreign('initiator_id')->references('id')->on('users');
            $table->nullableMorphs('ticket_reference');
            $table->unsignedTinyInteger('urgency')->default(\App\Ticket::URGENCY_NORMAL);
            $table->unsignedBigInteger('last_interactor')->nullable();
            $table->foreign('last_interactor')->references('id')->on('users');
            $table->timestamp('last_interaction_at')->nullable();
            $table->timestamp('progress_date')->nullable();
            $table->timestamp('last_response_at')->nullable();
            $table->unsignedBigInteger('assignee')->nullable();
            $table->foreign('assignee')->references('id')->on('users');
            $table->string('subject', 500);
            $table->unsignedMediumInteger('status')->default(\App\Ticket::STATUS_NEEDS_ACTION);
            $table->boolean('reopen_allowed')->default(true);
            $table->boolean('reopened')->default(false);
            $table->boolean('closed')->default(false);
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
        Schema::dropIfExists('tickets');
    }
}

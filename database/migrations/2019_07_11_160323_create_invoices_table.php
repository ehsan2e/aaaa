<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->string('country_code', 2)->nullable();
            $table->foreign('country_code')->references('code')->on('countries');
            $table->string('province_code', 5)->nullable();
            $table->foreign('province_code')->references('code')->on('provinces');
            $table->nullableMorphs('target');
            $table->unsignedBigInteger('substituted')->nullable();
            $table->foreign('substituted')->references('id')->on('invoices');
            $table->unsignedBigInteger('original_invoice')->nullable();
            $table->foreign('original_invoice')->references('id')->on('invoices');
            $table->decimal('discount', 16, 2)->default(0);
            $table->decimal('grand_total', 16, 2)->default(0);
            $table->decimal('sub_total', 16, 2)->default(0);
            $table->decimal('tax', 16, 2)->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->unsignedTinyInteger('status')->default(\App\Invoice::STATUS_NEW);
            $table->boolean('cancelled_by_system')->default(false);
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->foreign('cancelled_by')->references('id')->on('users');
            $table->timestamp('cancelled_at')->nullable();
            $table->boolean('can_be_paid_by_credit')->default(false);
            $table->json('extra_information')->nullable();

            $table->string('tag')->nullable()->index();
            $table->string('processor')->default(\NovaVoip\InvoiceProcessor\Manual::class);
            $table->unsignedBigInteger('follower')->nullable();
            $table->foreign('follower')->references('id')->on('clients');
            $table->timestamp('processed_at')->nullable();
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

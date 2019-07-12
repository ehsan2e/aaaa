<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id');
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('invoice_items')->onDelete('cascade');
            $table->unsignedBigInteger('product_type_id')->nullable();
            $table->foreign('product_type_id')->references('id')->on('product_types');
            $table->text('description')->nullable();
            $table->string('tag')->nullable()->index();
            $table->unsignedInteger('amount')->default(1);
            $table->decimal('cost', 16, 2)->default(0);
            $table->decimal('price', 16, 2)->default(0);
            $table->decimal('discount', 16, 2)->default(0);
            $table->decimal('grand_total', 16, 2)->default(0);
            $table->boolean('include_in_calculations')->default(true);
            $table->boolean('hide_from_client')->default(false);
            $table->decimal('sub_total', 16, 2)->default(0);
            $table->decimal('tax', 16, 2)->default(0);
            $table->json('extra_information')->nullable();
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
        Schema::dropIfExists('invoice_items');
    }
}

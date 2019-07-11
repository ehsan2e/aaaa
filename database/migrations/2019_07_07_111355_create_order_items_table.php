<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('order_items')->onDelete('cascade');
            $table->unsignedBigInteger('product_type_id');
            $table->foreign('product_type_id')->references('id')->on('product_types');
            $table->unsignedInteger('amount')->default(1);
            $table->timestamp('cached_at')->nullable()->index();
            $table->decimal('discount', 16, 2)->default(0);
            $table->decimal('grand_total', 16, 2)->default(0);
            $table->boolean('include_in_calculations')->default(true);
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
        Schema::dropIfExists('order_items');
    }
}

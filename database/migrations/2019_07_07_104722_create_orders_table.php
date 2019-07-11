<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cart_id');
            $table->foreign('cart_id')->references('id')->on('carts');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('country_code', 2)->nullable();
            $table->foreign('country_code')->references('code')->on('countries');
            $table->string('province_code', 5)->nullable();
            $table->foreign('province_code')->references('code')->on('provinces');
            $table->timestamp('cached_at')->nullable()->index();
            $table->decimal('discount', 16, 2)->default(0);
            $table->decimal('grand_total', 16, 2)->default(0);
            $table->decimal('sub_total', 16, 2)->default(0);
            $table->decimal('tax', 16, 2)->default(0);
            $table->boolean('can_be_invoiced')->default(true);
            $table->unsignedSmallInteger('invoices_issued_count')->default(0);
            $table->boolean('needs_negotiation')->default(false);
            $table->unsignedBigInteger('negotiator')->nullable();
            $table->foreign('negotiator')->references('id')->on('users');
            $table->timestamp('negotiated_at')->nullable();
            $table->boolean('can_be_cancelled')->default(true);
            $table->boolean('is_cancelled')->default(false);
            $table->boolean('cancelled_by_system')->default(false);
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->foreign('cancelled_by')->references('id')->on('users');
            $table->timestamp('cancelled_at')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->timestamp('paid_at')->nullable();
            $table->boolean('price_should_be_recalculated_for_new_invoice')->default(true);
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
        Schema::dropIfExists('orders');
    }
}

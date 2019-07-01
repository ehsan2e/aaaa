<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('product_categories');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->foreign('edited_by')->references('id')->on('users');

            $table->string('sku', 45)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('picture')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('on_sale')->default(true);

            $table->unsignedInteger('stock')->default(0);
            $table->boolean('stock_less')->default(false);
            $table->boolean('allow_back_order')->default(false);
            $table->boolean('show_out_of_stock')->default(false);

            $table->decimal('cost', 16, 2)->nullable();
            $table->decimal('original_price', 16, 2);
            $table->decimal('special_price', 16, 2)->nullable();

            $table->string('supplier_sku')->nullable();
            $table->decimal('supplier_share', 16, 2)->nullable();

            $table->decimal('promotion_price',16, 2)->nullable();
            $table->boolean('in_promotion')->default(false);
            $table->timestamp('promotion_starts_at')->nullable();
            $table->timestamp('promotion_ends_at')->nullable();
            $table->json('custom_attributes')->nullable();
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
        Schema::dropIfExists('product_types');
    }
}

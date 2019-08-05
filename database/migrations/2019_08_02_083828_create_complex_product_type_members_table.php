<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComplexProductTypeMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complex_product_type_members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('complex_product_type_id');
            $table->foreign('complex_product_type_id')->references('id')->on('product_types');
            $table->unsignedBigInteger('simple_product_type_id');
            $table->foreign('simple_product_type_id')->references('id')->on('product_types');
            $table->json('settings')->nullable();
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
        Schema::dropIfExists('complex_product_type_members');
    }
}

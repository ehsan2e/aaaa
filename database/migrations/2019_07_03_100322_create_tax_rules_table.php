<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('tax_group_id');
            $table->foreign('tax_group_id')->references('id')->on('tax_groups');
            $table->string('country_code', 2)->nullable();
            $table->foreign('country_code')->references('code')->on('countries');
            $table->string('province_code', 5)->nullable();
            $table->foreign('province_code')->references('code')->on('provinces');
            $table->unsignedSmallInteger('priority')->default(100);
            $table->decimal('amount', 16, 4)->nullable();
            $table->boolean('is_percentage')->default(true);
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('tax_rules');
    }
}

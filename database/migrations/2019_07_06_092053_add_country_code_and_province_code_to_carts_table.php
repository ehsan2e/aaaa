<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountryCodeAndProvinceCodeToCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->string('country_code', 2)->nullable();
            $table->foreign('country_code')->references('code')->on('countries');
            $table->string('province_code', 5)->nullable();
            $table->foreign('province_code')->references('code')->on('provinces');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['country_code']);
            $table->dropForeign(['province_code']);
            $table->dropColumn(['country_code', 'province_code']);
        });
    }
}

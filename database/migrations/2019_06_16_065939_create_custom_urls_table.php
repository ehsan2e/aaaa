<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_urls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('path')->unique();
            $table->string('redirect_url')->nullable();
            $table->unsignedInteger('redirect_status')->nullable();
            $table->string('handler')->nullable();
            $table->json('parameters')->nullable();
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
        Schema::dropIfExists('custom_urls');
    }
}

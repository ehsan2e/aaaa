<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeoConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seo_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_site_name')->nullable();
            $table->string('og_type')->nullable();
            $table->string('og_image')->nullable();
            $table->string('twitter_site')->nullable();
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_creator')->nullable();
            $table->string('twitter_card')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('twitter_image')->nullable();
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
        Schema::dropIfExists('seo_configs');
    }
}

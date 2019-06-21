<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('post_categories');
            $table->unsignedBigInteger('seo_config_id')->nullable();
            $table->foreign('seo_config_id')->references('id')->on('seo_configs');
            $table->string('code');
            $table->string('language')->default('en');
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('picture')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('url_id')->nullable();
            $table->foreign('url_id')->references('id')->on('custom_urls');
            $table->timestamps();
            $table->unique(['code', 'language']);
            $table->unique(['language', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_categories');
    }
}
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('post_categories');
            $table->unsignedBigInteger('seo_config_id')->nullable();
            $table->foreign('seo_config_id')->references('id')->on('seo_configs');
            $table->unsignedBigInteger('author_id')->nullable();
            $table->foreign('author_id')->references('id')->on('users');
            $table->string('code');
            $table->string('language')->default('en');
            $table->string('title');
            $table->mediumText('excerpt');
            $table->mediumText('content')->nullable();
            $table->string('picture')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('disclose_author')->default(true);
            $table->unsignedBigInteger('url_id')->nullable();
            $table->foreign('url_id')->references('id')->on('custom_urls');
            $table->timestamps();
            $table->unique(['code', 'language']);
            $table->unique(['title', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}

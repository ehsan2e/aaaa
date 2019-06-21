<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaImageVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_image_versions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('media_image_id');
            $table->foreign('media_image_id')->references('id')->on('media_images')
            ->onDelete('cascade');
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
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
        Schema::dropIfExists('media_image_versions');
    }
}

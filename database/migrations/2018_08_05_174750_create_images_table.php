<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('width')->nullable(true);
            $table->integer('height')->nullable(true);
            $table->integer('thumbnail_width')->nullable(true);
            $table->integer('thumbnail_height')->nullable(true);
            $table->boolean('processed')->default(false);
            $table->string('unprocessed_path', 255)->nullable(true);
            $table->string('path', 255)->nullable(true);
            $table->string('thumbnail_path', 255)->nullable(true);
            $table->string('filename', 255)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}

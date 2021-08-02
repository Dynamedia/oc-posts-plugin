<?php namespace Dynamedia\Posts\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreatePostTranslationsTable Migration
 */
class CreatePostTranslationsTable extends Migration
{
    public function up()
    {
        Schema::create('dynamedia_posts_post_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dynamedia_posts_post_translations');
    }
}

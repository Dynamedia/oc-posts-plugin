<?php namespace Dynamedia\Posts\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreatePostSlugsTable Migration
 */
class CreateTagSlugsTableV0908 extends Migration
{
    public function up()
    {
        Schema::create('dynamedia_posts_tag_slugs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->index();
            $table->integer('tag_id')->unsigned()->index();
            $table->timestamps();
        });

        // Slug belongs to one post, but many translations of that post.
        Schema::create('dynamedia_posts_tag_trans_slug', function (Blueprint $table) {
            $table->integer('trans_id')->unsigned();
            $table->integer('slug_id')->unsigned();
            $table->primary(['trans_id', 'slug_id']);
        });
    }



    public function down()
    {
        Schema::dropIfExists('dynamedia_posts_tag_slugs');
        Schema::dropIfExists('dynamedia_posts_tag_trans_slug');
    }
}

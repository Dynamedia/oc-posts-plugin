<?php namespace Dynamedia\Posts\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreatePostSlugsTable Migration
 */
class CreatePostSlugsTable extends Migration
{
    public function up()
    {
        Schema::create('dynamedia_posts_post_slugs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->index();
            $table->integer('post_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('post_id')->references('id')->on('dynamedia_posts_posts');

        });

        // Slug belongs to one post, but many translations of that post.
        Schema::create('dynamedia_posts_post_trans_slug', function (Blueprint $table) {
            $table->integer('trans_id')->unsigned();
            $table->integer('slug_id')->unsigned();
            $table->primary(['trans_id', 'slug_id']);

            $table->foreign('trans_id')->references('id')->on('dynamedia_posts_post_translations');
            $table->foreign('slug_id')->references('id')->on('dynamedia_posts_post_slugs');
        });
    }



    public function down()
    {
        Schema::table('dynamedia_posts_post_slugs', function (Blueprint $table) {
            $table->dropForeign('dynamedia_posts_post_slugs_post_id_foreign');
        });

        Schema::table('dynamedia_posts_post_trans_slug', function (Blueprint $table) {
            $table->dropForeign('dynamedia_posts_post_trans_slug_trans_id_foreign');
            $table->dropForeign('dynamedia_posts_post_trans_slug_slug_id_foreign');
        });

        Schema::dropIfExists('dynamedia_posts_post_slugs');
        Schema::dropIfExists('dynamedia_posts_post_trans_slug');
    }
}

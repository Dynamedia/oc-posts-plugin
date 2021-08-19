<?php namespace Dynamedia\Posts\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreatePostSlugsTable Migration
 */
class CreateTagSlugsTable extends Migration
{
    public function up()
    {
        Schema::create('dynamedia_posts_tag_slugs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->index();
            $table->integer('tag_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('tag_id')->references('id')->on('dynamedia_posts_tags')
                ->onDelete('cascade');
        });

        // Slug belongs to one post, but many translations of that post.
        Schema::create('dynamedia_posts_tag_trans_slug', function (Blueprint $table) {
            $table->integer('trans_id')->unsigned();
            $table->integer('slug_id')->unsigned();
            $table->primary(['trans_id', 'slug_id']);

            $table->foreign('trans_id')->references('id')->on('dynamedia_posts_tag_translations')
                ->onDelete('cascade');
            $table->foreign('slug_id')->references('id')->on('dynamedia_posts_tag_slugs')
                ->onDelete('cascade');
        });
    }



    public function down()
    {
        Schema::table('dynamedia_posts_tag_slugs', function (Blueprint $table) {
            $table->dropForeign('dynamedia_posts_tag_slugs_tag_id_foreign');
        });

        Schema::table('dynamedia_posts_tag_trans_slug', function (Blueprint $table) {
            $table->dropForeign('dynamedia_posts_tag_trans_slug_trans_id_foreign');
            $table->dropForeign('dynamedia_posts_tag_trans_slug_slug_id_foreign');
        });

        Schema::dropIfExists('dynamedia_posts_tag_slugs');
        Schema::dropIfExists('dynamedia_posts_tag_trans_slug');
    }
}

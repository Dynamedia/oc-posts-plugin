<?php namespace Dynamedia\Posts\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateTagsTable extends Migration
{
    public function up()
    {
        Schema::create('dynamedia_posts_tags', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->index();
            $table->json('images')->nullable()->default(null);
            $table->text('excerpt')->nullable()->default(null);
            $table->json('body_document')->nullable()->default(null);
            // searchable representation of the body document without having a cross-db solution for generating columns
            $table->longText('body_text')->nullable()->default(null);
            $table->json('seo')->nullable()->default(null);
            $table->boolean('is_approved')->index()->default(false);
            $table->string('cms_layout')->default('__inherit__');
            $table->json('post_list_options')->nullable()->default(null);
            $table->timestamps();
        });

        Schema::create('dynamedia_posts_posts_tags', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('post_id')->unsigned();
            $table->integer('tag_id')->unsigned();
            $table->primary(['post_id', 'tag_id']);

            $table->foreign('post_id')->references('id')->on('dynamedia_posts_posts')
                ->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('dynamedia_posts_tags')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('dynamedia_posts_posts_tags', function (Blueprint $table) {
            $table->dropForeign('dynamedia_posts_posts_tags_post_id_foreign');
            $table->dropForeign('dynamedia_posts_posts_tags_tag_id_foreign');
        });

        Schema::dropIfExists('dynamedia_posts_tags');
        Schema::dropIfExists('dynamedia_posts_posts_tags');
    }
}

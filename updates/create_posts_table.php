<?php namespace Dynamedia\Posts\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreatePostsTable extends Migration
{
    public function up()
    {
        Schema::create('dynamedia_posts_posts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('author_id')->nullable()->unsigned()->index();
            $table->integer('editor_id')->nullable()->unsigned()->index();
            $table->integer('primary_category_id')->unsigned()->nullable()->index();
            $table->string('slug')->index();
            $table->string('title');
            $table->json('images')->nullable()->default(null);
            $table->text('excerpt')->nullable()->default(null);
            $table->json('body')->nullable()->default(null);
            $table->json('seo')->nullable()->default(null);
            $table->boolean('show_contents')->default(true);
            $table->string('cms_layout')->default('__inherit__');
            $table->boolean('is_published')->index()->default(false);
            $table->dateTime('published_at')->nullable()->index()->default(null);
            $table->dateTime('published_until')->index()->nullable()->default(null);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dynamedia_posts_posts');
    }
}

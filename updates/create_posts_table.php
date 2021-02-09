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
            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->integer('primary_category_id')->unsigned()->nullable()->index();
            $table->string('slug')->index();
            $table->string('title');
            $table->json('main_images')->nullable()->default(null);
            $table->text('introduction')->nullable()->default(null);
            $table->json('body');
            $table->boolean('show_contents')->default(true);
            $table->string('cms_layout')->default('default.htm');
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

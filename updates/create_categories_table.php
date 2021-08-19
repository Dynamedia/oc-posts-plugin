<?php namespace Dynamedia\Posts\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('dynamedia_posts_categories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->index();
            $table->json('images')->nullable()->default(null);
            $table->text('excerpt')->nullable()->default(null);
            $table->json('body')->nullable()->default(null);
            $table->json('seo')->nullable()->default(null);
            $table->json('post_list_options')->nullable()->default(null);
            $table->string('cms_layout')->default('__inherit__');
            $table->integer('parent_id')->unsigned()->index()->nullable();
            $table->integer('nest_left')->nullable();
            $table->integer('nest_right')->nullable();
            $table->integer('nest_depth')->nullable();
            $table->timestamps();
        });

        // Add foreign key to posts table referencing this id
        Schema::table('dynamedia_posts_posts', function (Blueprint $table) {
            $table->foreign('primary_category_id')->references('id')->on('dynamedia_posts_categories')
                ->onDelete('set null');
        });

        Schema::create('dynamedia_posts_posts_categories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('post_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->primary(['post_id', 'category_id']);

            $table->foreign('post_id')->references('id')->on('dynamedia_posts_posts')
                ->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('dynamedia_posts_categories')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        // Add foreign key to posts table referencing this id
        Schema::table('dynamedia_posts_posts', function (Blueprint $table) {
            $table->dropForeign('dynamedia_posts_posts_primary_category_id_foreign');
        });

        Schema::table('dynamedia_posts_posts_categories', function (Blueprint $table) {
            $table->dropForeign('dynamedia_posts_posts_categories_post_id_foreign');
            $table->dropForeign('dynamedia_posts_posts_categories_category_id_foreign');
        });

        Schema::dropIfExists('dynamedia_posts_categories');
        Schema::dropIfExists('dynamedia_posts_posts_categories');
    }
}

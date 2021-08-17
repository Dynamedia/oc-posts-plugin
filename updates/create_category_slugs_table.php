<?php namespace Dynamedia\Posts\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreatePostSlugsTable Migration
 */
class CreateCategorySlugsTable extends Migration
{
    public function up()
    {
        Schema::create('dynamedia_posts_category_slugs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->index();
            $table->integer('category_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('dynamedia_posts_categories');
        });

        // Slug belongs to one category, but many translations of that category.
        Schema::create('dynamedia_posts_category_trans_slug', function (Blueprint $table) {
            $table->integer('trans_id')->unsigned();
            $table->integer('slug_id')->unsigned();
            $table->primary(['trans_id', 'slug_id']);

            $table->foreign('trans_id')->references('id')->on('dynamedia_posts_category_translations');
            $table->foreign('slug_id')->references('id')->on('dynamedia_posts_category_slugs');
        });
    }



    public function down()
    {
        Schema::table('dynamedia_posts_category_slugs', function (Blueprint $table) {
            $table->dropForeign('dynamedia_posts_category_slugs_category_id_foreign');
        });

        Schema::table('dynamedia_posts_category_trans_slug', function (Blueprint $table) {
            $table->dropForeign('dynamedia_posts_category_trans_slug_trans_id_foreign');
            $table->dropForeign('dynamedia_posts_category_trans_slug_slug_id_foreign');
        });

        Schema::dropIfExists('dynamedia_posts_category_slugs');
        Schema::dropIfExists('dynamedia_posts_category_trans_slug');
    }
}

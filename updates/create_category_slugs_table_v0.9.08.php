<?php namespace Dynamedia\Posts\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreatePostSlugsTable Migration
 */
class CreateCategorySlugsTableV0908 extends Migration
{
    public function up()
    {
        Schema::create('dynamedia_posts_category_slugs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->index();
            $table->integer('category_id')->unsigned()->index();
            $table->timestamps();
        });

        // Slug belongs to one category, but many translations of that category.
        Schema::create('dynamedia_posts_category_trans_slug', function (Blueprint $table) {
            $table->integer('trans_id')->unsigned();
            $table->integer('slug_id')->unsigned();
            $table->primary(['trans_id', 'slug_id']);
        });
    }



    public function down()
    {
        Schema::dropIfExists('dynamedia_posts_category_slugs');
        Schema::dropIfExists('dynamedia_posts_category_trans_slug');
    }
}

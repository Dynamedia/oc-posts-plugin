<?php namespace Dynamedia\Posts\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateCategoryTranslationsTable Migration
 */
class CreateCategoryTranslationsTable extends Migration
{
    public function up()
    {
        Schema::create('dynamedia_posts_category_translations', function (Blueprint $table) {
            $table->increments('id');
            // Keep nullable initially
            $table->integer('native_id')->unsigned()->nullable()->index();
            // Rainlab translate
            $table->integer('locale_id')->unsigned()->nullable()->index();
            $table->string('name');
            $table->string('slug')->index();
            $table->json('images')->nullable()->default(null);
            $table->text('excerpt')->nullable()->default(null);
            $table->json('body')->nullable()->default(null);
            $table->json('seo')->nullable()->default(null);
            $table->string('cms_layout')->default('__inherit__');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dynamedia_posts_category_translations');
    }
}

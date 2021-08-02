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
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dynamedia_posts_category_translations');
    }
}

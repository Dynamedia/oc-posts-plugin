<?php namespace Dynamedia\Posts\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateTagTranslationsTable Migration
 */
class CreateTagTranslationsTable extends Migration
{
    public function up()
    {
        Schema::create('dynamedia_posts_tag_translations', function (Blueprint $table) {
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

            $table->foreign('native_id')->references('id')->on('dynamedia_posts_tags')
                ->onDelete('cascade');
            $table->foreign('locale_id')->references('id')->on('rainlab_translate_locales')
                ->onDelete('set null');
        });

    }

    public function down()
    {
        Schema::table('dynamedia_posts_tag_translations', function (Blueprint $table) {
            $table->dropForeign('dynamedia_posts_tag_translations_native_id_foreign');
            $table->dropForeign('dynamedia_posts_tag_translations_locale_id_foreign');
        });

        Schema::dropIfExists('dynamedia_posts_tag_translations');
    }
}

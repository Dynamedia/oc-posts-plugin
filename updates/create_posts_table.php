<?php namespace Dynamedia\Posts\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreatePostsTable extends Migration
{
    public function up()
    {
        //todo missed foreign keys
        Schema::create('dynamedia_posts_posts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('locale_id')->nullable()->unsigned()->index();
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

            // Translate plugin is a dependency
            $table->foreign('locale_id')->references('id')->on('rainlab_translate_locales')
                ->onDelete('set null');
            $table->foreign('author_id')->references('id')->on('backend_users')
                ->onDelete('set null');
            $table->foreign('editor_id')->references('id')->on('backend_users')
                ->onDelete('set null');

            // Set Primary category id foreign key in category migration
        });
    }


    public function down()
    {
        Schema::table('dynamedia_posts_posts', function (Blueprint $table) {
            $table->dropForeign('dynamedia_posts_posts_locale_id_foreign');
            $table->dropForeign('dynamedia_posts_posts_author_id_foreign');
            $table->dropForeign('dynamedia_posts_posts_editor_id_foreign');
        });
        Schema::dropIfExists('dynamedia_posts_posts');
    }
}

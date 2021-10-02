<?php namespace Dynamedia\Posts\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * drop fk's and re set with cascade
 */
class FixCatSlugs extends Migration
{
    public function up()
    {
        Schema::table('dynamedia_posts_category_slugs', function (Blueprint $table) {
            $table->dropForeign('dynamedia_posts_category_slugs_category_id_foreign');
            $table->foreign('category_id')->references('id')->on('dynamedia_posts_categories')
                ->onDelete('cascade');
        });

        Schema::table('dynamedia_posts_category_trans_slug', function (Blueprint $table) {
            $table->dropForeign('dynamedia_posts_category_trans_slug_trans_id_foreign');
            $table->dropForeign('dynamedia_posts_category_trans_slug_slug_id_foreign');
            $table->foreign('trans_id')->references('id')->on('dynamedia_posts_category_translations')
                ->onDelete('cascade');
            $table->foreign('slug_id')->references('id')->on('dynamedia_posts_category_slugs')
                ->onDelete('cascade');
        });
    }



    public function down()
    {
        

        Schema::dropIfExists('dynamedia_posts_category_slugs');
        Schema::dropIfExists('dynamedia_posts_category_trans_slug');
    }
}

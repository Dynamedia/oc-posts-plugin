<?php namespace Dynamedia\Posts\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class UpdateCategoriesTableV0903 extends Migration
{
    public function up()
    {
        Schema::table('dynamedia_posts_categories', function (Blueprint $table) {
            $table->json('post_list_options')->nullable();
        });
    }

    public function down()
    {
        Schema::table('dynamedia_posts_categories', function (Blueprint $table) {
            $table->dropColumn('post_list_options');
        });
    }
}

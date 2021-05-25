<?php namespace Dynamedia\Posts\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class UpdatePostsTableV0906 extends Migration
{
    public function up()
    {
        Schema::table('dynamedia_posts_posts', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->integer('author_id')->unsigned()->nullable()->index();
            $table->integer('editor_id')->unsigned()->nullable()->index();
        });
    }

    public function down()
    {
        Schema::table('dynamedia_posts_posts', function (Blueprint $table) {
            $table->dropColumn('author_id');
            $table->dropColumn('editor_id');
            $table->integer('user_id')->unsigned()->index();
        });
    }
}

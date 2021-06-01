<?php namespace Dynamedia\Posts\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class UpdatePostsTableV0906 extends Migration
{
    public function up()
    {
        Schema::table('dynamedia_posts_posts', function (Blueprint $table) {
            $table->integer('author_id')->nullable()->unsigned()->index();
            $table->integer('editor_id')->nullable()->unsigned()->index();
            //$table->dropColumn('user_id');
        });
    }

    public function down()
    {
        Schema::table('dynamedia_posts_posts', function (Blueprint $table) {
            $table->integer('user_id')->nullable()->unsigned()->index();
            $table->dropColumn('author_id');
            $table->dropColumn('editor_id');
        });
    }
}

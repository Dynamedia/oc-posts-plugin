<?php namespace Dynamedia\Posts\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class UpdatePostsTableV0907 extends Migration
{
    public function up()
    {
        Schema::table('dynamedia_posts_posts', function (Blueprint $table) {
            $table->integer('editor_id')->unsigned()->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('dynamedia_posts_posts', function (Blueprint $table) {

        });
    }
}

<?php namespace Dynamedia\Posts\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class UpdateTagsTableV0902 extends Migration
{
    public function up()
    {
        Schema::table('dynamedia_posts_tags', function (Blueprint $table) {
            $table->string('cms_layout')->default('__inherit__');
        });
    }

    public function down()
    {
        Schema::table('dynamedia_posts_tags', function (Blueprint $table) {
            $table->dropColumn('cms_layout');
        });
    }
}
